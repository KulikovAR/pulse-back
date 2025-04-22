<?php

namespace App\Services;

use App\Http\Resources\CompanyResource;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    public function getAll(User $user)
    {
        $companies = Company::where('user_id', $user->id)->get();

        return CompanyResource::collection($companies)->response()->getData()->data;
    }

    public function store(array $validated, User $user)
    {
        $validated['user_id'] = $user->id;
        $company              = Company::create($validated);

        return (new CompanyResource($company))->response()->getData()->data;
    }

    public function show(Company $company, User $user)
    {
        if ($company->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return (new CompanyResource($company))->response()->getData()->data;
    }

    public function update(Company $company, array $validated, User $user)
    {
        if ($company->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $company->update($validated);

        return (new CompanyResource($company))->response()->getData()->data;
    }

    public function handleImageUpload(Company $company, UploadedFile $image): string
    {
        if (Storage::disk('public')->exists($company->image)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $company->image = $image->store('companies', 'public');
    }

    public function destroy(Company $company, User $user): ApiJsonResponse
    {
        if ($company->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        $company->delete();

        return new ApiJsonResponse(httpCode: 204);
    }

    public function getCompaniesByClientId(string $clientId)
    {
        $companies = Company::whereHas('events', function ($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->get();

        return CompanyResource::collection($companies)->response()->getData()->data;
    }
}
