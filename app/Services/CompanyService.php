<?php

namespace App\Services;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\User;
use App\Http\Responses\ApiJsonResponse;
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
        $company = Company::create($validated);
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

        if (isset($validated['image']) && $validated['image'] instanceof UploadedFile) {
            $validated['image'] = $this->handleImageUpload($validated['image'], $company->image);
        }

        $company->update($validated);
        return (new CompanyResource($company))->response()->getData()->data;
    }

    private function handleImageUpload(UploadedFile $image, ?string $oldImagePath = null): string
    {
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $image->store('companies', 'public');
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
        $companies = Company::whereHas('events', function($query) use ($clientId) {
            $query->where('client_id', $clientId);
        })->get();

        return CompanyResource::collection($companies)->response()->getData()->data;
    }
}
