<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\CompanyClient;
use App\Http\Resources\ClientResource;

class ClientService
{
    public function getAllClients()
    {
        return Client::all();
    }

    public function getClientById(string $id): Client
    {
        return Client::findOrFail($id);
    }

    public function createClient(array $data): Client
    {
        return Client::create($data);
    }

    public function updateClient(Client $client, array $data): Client
    {
        $client->update($data);
        return $client;
    }

    public function deleteClient(Client $client): void
    {
        $client->delete();
    }

    public function deleteClientFromCompany(string $clientId, $company): void
    {
        $companyClient = CompanyClient::where('company_id', $company->id)
            ->where('client_id', $clientId)
            ->firstOrFail();

        $companyClient->delete();
    }

    public function getClientsByUser($user)
    {
        $company = $user->company;
        if (!$company) {
            return collect([]);
        }

        return Client::whereHas('companyClient', function($query) use ($company) {
            $query->where('company_id', $company->id);
        })
        ->with(['companyClient' => function($query) use ($company) {
            $query->where('company_id', $company->id);
        }])
        ->get();
    }

    public function createClientForCompany($data, $company)
    {
        // Check if client with this phone exists
        $existingClient = Client::where('phone', $data['phone'])->first();

        if ($existingClient) {
            // Case 1: Client exists
            return CompanyClient::create([
                'name' => $data['name'],
                'verify' => false,
                'client_id' => $existingClient->id,
                'company_id' => $company->id
            ]);
        }

        // Case 2: Check if user with this phone exists
        $existingUser = User::where('phone', $data['phone'])->first();

        if ($existingUser && $existingUser->client) {
            // Case 2.1: User exists and has a client
            return CompanyClient::create([
                'name' => $data['name'],
                'verify' => false,
                'client_id' => $existingUser->client->id,
                'company_id' => $company->id
            ]);
        }

        // Case 2.2: Create new client
        $client = Client::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'user_id' => null
        ]);

        return CompanyClient::create([
            'name' => $data['name'],
            'verify' => false,
            'client_id' => $client->id,
            'company_id' => $company->id
        ]);
    }
}