<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function __construct(
        protected CompanyService $service
    )
    {
    }

    public function index(): ApiJsonResponse
    {
        return new ApiJsonResponse(data: $this->service->getAll(Auth::user()));
    }

    public function store(StoreCompanyRequest $request): ApiJsonResponse
    {
        return new ApiJsonResponse(data: $this->service->store($request->validated(), Auth::user()), httpCode: 201);
    }

    public function show(Company $company): ApiJsonResponse
    {
        return new ApiJsonResponse(data: $this->service->show($company, Auth::user()));
    }

    public function update(UpdateCompanyRequest $request, Company $company): ApiJsonResponse
    {
        if ($request->hasFile('image')) {
            $this->service->handleImageUpload($company, $request->file('image'));
        }

        return new ApiJsonResponse(data: $this->service->update($company, $request->validated(), Auth::user()));
    }

    public function destroy(Company $company): ApiJsonResponse
    {
        return $this->service->destroy($company, Auth::user());
    }

    public function getByClientId()
    {
        $clientId = Auth::user()->client->id;

        return new ApiJsonResponse(data: $this->service->getCompaniesByClientId($clientId));
    }
}
