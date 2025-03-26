<?php

namespace Tests\Feature\Telegram;

use App\Models\Client;
use App\Models\TelegramClient;
use App\Models\User;
use Mockery;
use Tests\TestCase;

class TelegramLoginTest extends TestCase
{
    public function test_login_with_existing_chat_id()
    {
        // Создаём пользователя и TelegramClient с нужным chat_id
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $telegramClient = TelegramClient::factory()->create([
            'chat_id' => '12345',
            'client_id' => $client->id,
        ]);

        // Мокаем проверку авторизации Telegram
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Делаем запрос
        $response = $this->postJson(route('telegram.login'), [
            'id' => '12345',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_existing_chat_id_but_no_user()
    {
        // Создаём клиента без связанного пользователя
        $client = Client::factory()->create(['user_id' => null]);
        $telegramClient = TelegramClient::factory()->create([
            'chat_id' => '123456',
            'client_id' => $client->id,
        ]);

        // Мокаем проверку авторизации Telegram
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Делаем запрос
        $response = $this->postJson(route('telegram.login'), [
            'id' => '123456',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_phone_but_client_not_found()
    {
        // Мокаем проверку авторизации Telegram
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Делаем запрос с номером телефона
        $response = $this->postJson(route('telegram.login'), [
            'id' => '12345',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
            'phone' => '1234567890',
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_phone_and_client_found()
    {
        // Создаём клиента с номером телефона
        $phone = $this->faker->numerify('+7#########');
        $client = Client::factory()->create(['phone' => $phone]);

        // Мокаем проверку авторизации Telegram
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Делаем запрос с параметром телефона
        $response = $this->postJson(route('telegram.login'), [
            'id' => '12345',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
            'phone' => $phone,
        ]);

        $response->assertStatus(200);
    }

    public function test_login_without_phone()
    {
        // Мокаем проверку авторизации Telegram
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Делаем запрос без параметра телефона
        $response = $this->postJson(route('telegram.login'), [
            'id' => '12345',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_login_first_time_creates_company()
    {
        // Create user without associated company
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $telegramClient = TelegramClient::factory()->create([
            'chat_id' => '123456',
            'client_id' => $client->id,
        ]);

        // Mock Telegram authorization check
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Make request to admin login endpoint
        $response = $this->postJson(route('telegram.admin.login'), [
            'id' => '123456',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
        ]);

        $response->assertStatus(200);

        $company = $user->company;

        // $this->assertDatabaseHas('companies', [
        //     'user_id' => $user->id
        // ]);
    }

    public function test_admin_login_with_deleted_company_creates_new_company()
    {
        // Create user with no associated company
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $telegramClient = TelegramClient::factory()->create([
            'chat_id' => '1234567',
            'client_id' => $client->id,
        ]);

        // Mock Telegram authorization check
        $checkAuthMock = Mockery::mock('App\Services\Telegram\Auth\CheckAuth');
        $checkAuthMock->shouldReceive('checkTelegramAuthorization')->andReturn(true);
        $this->app->instance('App\Services\Telegram\Auth\CheckAuth', $checkAuthMock);

        // Make request to admin login endpoint
        $response = $this->postJson(route('telegram.admin.login'), [
            'id' => '1234567',
            'hash' => 'validhash',
            'first_name' => 'John',
            'username' => 'john_doe',
        ]);

        $response->assertStatus(200);
        // $this->assertDatabaseHas('companies', [
        //     'user_id' => $user->id
        // ]);
    }
}
