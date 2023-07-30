<?php

namespace App\Exceptions;

use Lanin\Laravel\ApiExceptions\ApiException;
use Lanin\Laravel\ApiExceptions\LaravelExceptionHandler;
use Throwable;

class Handler extends LaravelExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function formatApiResponse(ApiException $exception): array
    {
        return [
            'error' => $exception->toArray(),
        ];
    }
}
