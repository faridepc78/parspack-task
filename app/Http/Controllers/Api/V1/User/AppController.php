<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\User\App\AppResource;
use App\Models\App;

class AppController extends Controller
{
    public function show(App $app)
    {
        return $this->success_response(
            AppResource::make($app->load(['platform', 'subscription'])),
            'show app information'
        );
    }
}
