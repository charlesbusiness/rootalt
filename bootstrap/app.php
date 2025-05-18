<?php

use App\Exception\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Modules\Authentication\Http\Middleware\ResourceAccess;
use Modules\Authentication\Http\Middleware\VerifyEmailMiddleware;
use Modules\Core\Console\AppConfiguration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verify-email' => VerifyEmailMiddleware::class,
            'resource.access' => ResourceAccess::class,
        ]);
    })
    ->withCommands([
        AppConfiguration::class,
    ])
    // ->withSchedule(function (Schedule $schedule) {
    //     // $schedule->command('some:signature')->daily();
    // })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $exception, $request) {

            if ($request->expectsJson()) {
                return (new ExceptionHandler)->handleApiException($exception);
            }

            return (new ExceptionHandler)->defaultExceptionHandler($exception);
        });
    })->create();
