<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\Page;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
      $middleware->redirectGuestsTo(function(Request $request) {
        return '/';
      });
      // $middleware->appendToGroup('web', [
      //   \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
      //   \Illuminate\Session\Middleware\StartSession::class,
      //   \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      // ]);
    })
    ->withEvents(discover: [
      __DIR__.'/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(\Exception $e) {
          if ($e instanceof AccessDeniedHttpException) {
            return redirect('/undefined');
          }

          if (method_exists($e, 'getStatusCode') && $e->getStatusCode() === 403) {
            return redirect('/undefined');
          } 
        });
    })
    ->create();
