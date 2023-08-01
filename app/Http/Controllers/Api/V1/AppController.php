<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\App;

class AppController extends Controller
{
    public function __invoke(App $app)
    {
        return $app->load(['platform', 'subscription']);
    }
}
