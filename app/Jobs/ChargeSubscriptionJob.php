<?php

namespace App\Jobs;

use App\Enums\EnvironmentTypeEnum;
use App\Http\Controllers\PaymentController;
use App\Models\Order;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ChargeSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $users = User::inRandomOrder()
                     ->where('subscription_expires_at', '<=', Carbon::now())
                     ->where('charge_attempts', '<', 10)
                     ->limit(50)
                     ->get();

        if($users->isNotEmpty() &&  App::environment(EnvironmentTypeEnum::notProductEnv())){
            Log::info('Subscription charging job...');
            Log::info(print_r($users, true));
        }

        foreach ($users as $user) {
            $order = $user->orders()
                ->whereNotNull('rebill_id')
                ->orderBy('updated_at', 'desc')
                ->first();

            $newOrder = new Order();
            $newOrder ->user_id = $user->id;
            $newOrder ->price = $order->price;
            $newOrder ->duration = $order->duration;
            $newOrder ->order_status = $order->order_status;
            $newOrder ->order_type = $order->order_type;
            $newOrder ->payment_id = $order->payment_id;
            $newOrder ->rebill_id = $order->rebill_id;
            $newOrder->save();

            !$order
                ? NotificationService::notifyAdmin('No order for charging subscription. Check orders for user id:' . $user->id)
                : (new PaymentController())->charge($newOrder->id);
        }

    }
}
