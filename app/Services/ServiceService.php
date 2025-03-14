<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ServiceService
{
    public function getAllServices(User $user)
    {
        $company = Company::where('user_id', $user->id)->firstOrFail();
        return Service::where('company_id', $company->id)->get();
    }

    public function getServiceById(string $id): Service
    {
        return Service::findOrFail($id);
    }

    public function createService(array $data, User $user): Service
    {
        $company = Company::where('user_id', $user->id)->firstOrFail();
        $data['company_id'] = $company->id;
        return Service::create($data);
    }

    public function updateService(Service $service, array $data): Service
    {
        $service->update($data);
        return $service;
    }

    public function deleteService(Service $service, User $user): void
    {
        // Verify company ownership before deletion
        Company::where('id', $service->company_id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $service->delete();
    }
}