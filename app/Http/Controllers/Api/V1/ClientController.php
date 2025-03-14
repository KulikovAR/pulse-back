<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Client;
use App\Models\CompanyClient;
use App\Models\User;
use App\Services\ClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {}

    public function index()
    {
        $user = Auth::user();
        $clients = $this->clientService->getClientsByUser($user);
        return new ApiJsonResponse(data: ClientResource::collection($clients));
    }

    public function store(StoreClientRequest $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return new ApiJsonResponse(message: 'User is not associated with any company', httpCode: 403);
        }

        $companyClient = $this->clientService->createClientForCompany($request->validated(), $company);
        return new ApiJsonResponse(data: $companyClient, message: 'Client created successfully', httpCode: 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $client = $this->clientService->getClientById($id);
        
        if ($client->user_id !== $user->id) {
            return new ApiJsonResponse(message: 'Unauthorized access', httpCode: 403);
        }
        
        return new ApiJsonResponse(data: new ClientResource($client));
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $user = Auth::user();
        $client = $this->clientService->getClientById($id);
        
        if ($client->user_id !== $user->id) {
            return new ApiJsonResponse(message: 'Unauthorized access', httpCode: 403);
        }
        
        $client = $this->clientService->updateClient($client, $request->validated());
        return new ApiJsonResponse(data: new ClientResource($client), message: 'Client updated successfully');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return new ApiJsonResponse(message: 'User is not associated with any company', httpCode: 403);
        }

        try {
            $this->clientService->deleteClientFromCompany($id, $company);
            return new ApiJsonResponse(message: 'Client successfully removed from company');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return new ApiJsonResponse(message: 'Client not found for this company', httpCode: 404);
        }
    }
}