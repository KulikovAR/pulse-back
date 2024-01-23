<?php

namespace App\Http\Controllers;

use App\Enums\EnvironmentTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Requests\UuidRequest;
use App\Http\Responses\ApiJsonResponse;
use App\Interfaces\PaymentServiceInterface;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\PaymentSuccessNotification;
use App\Notifications\SubscriptionCanceledNotification;
use App\Services\NotificationService;
use App\Services\TinkoffPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Log;

class PaymentController extends Controller
{
    public function __construct(
        public PaymentServiceInterface $paymentService = new TinkoffPaymentService(),
    ) {}

    public function redirect(UuidRequest $request)
    {
        $user = User::findOrFail($request->id);

        if (!in_array($request->order_type, [Order::NORMAL, Order::VIP, Order::PREMIUM])) {
            abort(422, "Must be in: " . print_r([Order::NORMAL, Order::VIP, Order::PREMIUM], true));
        }

        //TODO Transaction
        $order = Order::create([

                                   'order_status' => Order::CREATED,
                                   'order_type'   => $request->order_type,
                                   'price'        => Setting::getPriceValueFromFieldName('price_' . $request->order_type) ?? 9999,
                                   'duration'     => Setting::getValueFromFieldName('duration_' . $request->order_type) ?? 1
                               ]);

        $order->user()->associate($user);
        $order->save();

        list($paymentUrl, $paymentId) = $this->paymentService->getPaymentUrl($order);

        if (empty($paymentId)) {
            $message = 'OrderId:' . $order->id . ' No payment url. Check payment provider';
            Log::alert($message);
            NotificationService::notifyAdmin($message);

            return redirect($paymentUrl);
        }


        $order->payment_id = $paymentId;
        $order->save();

        return redirect($paymentUrl);
    }

    public function charge(string $orderId)
    {
        $oldOrder = Order::findOrFail($orderId);
        $user     = $oldOrder->user;

        $order = new Order();
        $order ->user_id = $oldOrder->user_id;
        $order ->price = $oldOrder->price;
        $order ->duration = $oldOrder->duration;
        $order ->order_status = Order::CREATED;
        $order ->order_type = $oldOrder->order_type;
        $order ->payment_id = $oldOrder->payment_id;
        $order ->rebill_id = $oldOrder->rebill_id;
        $order->save();

        list($paymentUrl, $paymentId) = $this->paymentService->getPaymentUrl($order);

        if (empty($paymentId)) {
            $message = 'Charging OrderId:' . $order->id . ' No payment url. Check payment provider';
            Log::alert($message);
            NotificationService::notifyAdmin($message);

            $order->order_status = Order::CANCELED;
            $order->save();

            return;
        }

        $order->payment_id = $paymentId;
        $order->save();

        list($paymentSuccessState, $paymentAmount) = $this->paymentService->updateSubscription($order);

        $cents      = 100;
        $priceTotal = $order->price * $cents;


        if ($paymentSuccessState !== true || $priceTotal < $paymentAmount) {
            $message = 'OrderId: ' . $order->id . ' Charging status failed or small payed amount. Payment/Amount: ' . $paymentSuccessState . ' / ' . $paymentAmount;
            Log::alert($message);
            NotificationService::notifyAdmin($message);

            $order->order_status = Order::EXPIRED;
            $order->save();

          

            return;
        }

        $duration = Setting::getValueFromFieldName('duration_' . $order->order_type) ?? 1;

        $orderType = match ($order->order_type) {
            "normal"  => SubscriptionTypeEnum::MONTH->value,
            "vip"     => SubscriptionTypeEnum::THREE_MOTHS->value,
            "premium" => SubscriptionTypeEnum::YEAR->value,
            default   => SubscriptionTypeEnum::MONTH->value
        };

        $user->subscription_type       = $orderType;
        $user->subscription_created_at = now();
        $user->charge_attempts         = 0;
        $user->subscription_expires_at = Carbon::parse($user->subscriptionAvailable() ? $user->subscription_expires_at : now())->addDays($duration)->format('Y-m-d H:i:s');
        $user->save();

        $order->order_status = Order::PAYED;
        $order->save();

        $user->notify(new PaymentSuccessNotification($order));
    }

    public function success(UuidRequest $request)
    {
        $order = Order::where(['order_status' => Order::CREATED])->findOrFail($request->id);

        list($paymentSuccess, $paymentAmount) = $this->paymentService->getPaymentState($order);

        $cents      = 100;
        $priceTotal = $order->price * $cents;

        if ($paymentSuccess !== true || $priceTotal < $paymentAmount) {
            $message = 'OrderId: ' . $order->id . ' Payment status failed or small payed amount. Payment/Amount: ' . $paymentSuccess . ' / ' . $paymentAmount;
            Log::alert($message);
            NotificationService::notifyAdmin($message);

            return redirect(
                config('front-end.payment_failed')
                . config('front-end.payment_status_failed')
                . __('order.payment_failed')
            );
        }

        $duration = Setting::getValueFromFieldName('duration_' . $order->order_type) ?? 1;

        $orderType = match ($order->order_type) {
            "normal"  => SubscriptionTypeEnum::MONTH->value,
            "vip"     => SubscriptionTypeEnum::THREE_MOTHS->value,
            "premium" => SubscriptionTypeEnum::YEAR->value,
            default   => SubscriptionTypeEnum::MONTH->value
        };

        $user                          = $order->user;
        $user->auto_subscription       = true;
        $user->subscription_type       = $orderType;
        $user->subscription_created_at = now();
        $user->subscription_expires_at = Carbon::parse($user->subscriptionAvailable() ? $user->subscription_expires_at : now())->addDays($duration)->format('Y-m-d H:i:s');
        $user->save();


        $order->order_status = Order::PAYED;
        $order->save();

        $user->notify(new PaymentSuccessNotification($order));

        $user->addSheduleLesson();

        return redirect(config('front-end.payment_success') . config('front-end.payment_status_success'));
    }

    public function failed(UuidRequest $request)
    {
        $order               = Order::where(['order_status' => Order::CREATED])->findOrFail($request->id);
        $order->order_status = Order::CANCELED;
        $order->save();

        $message = 'Payment failed! ' . 'OrderId:' . $order->id;
        Log::info($message);
        NotificationService::notifyAdmin($message);

        return redirect(
            config('front-end.payment_failed')
            . config('front-end.payment_status_failed')
            . __('order.payment_error')
        );
    }

    public function paymentStatus(Request $request)
    {
        if (App::environment(EnvironmentTypeEnum::notProductEnv())) {
            Log::info(print_r($request->all(), true));
        }

        $orderId = $request->OrderId;

        $order = Order::find($orderId);
        $order->rebill_id = $request->RebillId;
        $order->card = $request->Pan;
        $order->card_id_tinkoff = $request->CardId;
        $order->status = $request->Status;
        $order->err_code = $request->ErrorCode;
        $order->token = $request->Token;
        $order->save();

        return response("OK", 200);
    }

    public function unsubscribe(Request $request)
    {
        $user = $request->user();

        $user->orders()->delete();

        $user->auto_subscription = 0;
        $user->save();

        $user->notify(new SubscriptionCanceledNotification());

        return new ApiJsonResponse();
    }
}

