<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Http\Responses\ApiJsonResponse;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function __construct(
        private ServiceService $serviceService
    ) {}

    public function index()
    {
        $services = $this->serviceService->getAllServices(Auth::user());
        return new ApiJsonResponse(data: ServiceResource::collection($services));
    }

    public function store(StoreServiceRequest $request)
    {
        $service = $this->serviceService->createService($request->validated(), Auth::user());
        return new ApiJsonResponse(data: new ServiceResource($service), message: 'Service created successfully', httpCode: 201);
    }

    public function show($id)
    {
        $service = $this->serviceService->getServiceById($id);
        return new ApiJsonResponse(data: new ServiceResource($service));
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $service = $this->serviceService->getServiceById($id);
        $service = $this->serviceService->updateService($service, $request->validated());
        return new ApiJsonResponse(data: new ServiceResource($service), message: 'Service updated successfully');
    }

    public function destroy($id)
    {
        $service = $this->serviceService->getServiceById($id);
        $this->serviceService->deleteService($service, Auth::user());
        return new ApiJsonResponse(message: 'Service deleted successfully');
    }
}