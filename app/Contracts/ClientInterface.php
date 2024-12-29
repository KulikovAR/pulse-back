<?php

namespace App\Contracts;

use App\Http\Responses\ApiJsonResponse;
use Illuminate\Http\Request;

interface ClientInterface
{
    public function login(Request $request): ApiJsonResponse;
}
