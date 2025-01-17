<?php

namespace Proto\Exceptions;

use App;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Exception\NotReadableException;
use Redirect;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array<string>
     */
    protected $dontReport = [
        HttpExceptionInterface::class,
        NotFoundHttpException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        HttpExceptionInterface::class,
        NotReadableException::class,
        ValidationException::class,
        AuthorizationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e)
    {
        if ($this->shouldReport($e) && app()->bound('sentry') && App::environment('production')) {
            app('sentry')->captureException($e);
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return SymfonyResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof UnauthorizedException) {
            return response()->view('errors.403');
        }

        return parent::render($request, $e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  Request $request
     * @param  AuthenticationException $exception
     * @return JsonResponse|RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return Redirect::guest('login');
    }

    /**
     * Render the given HttpException.
     *
     * @param HttpExceptionInterface $e
     * @return SymfonyResponse
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        if (! view()->exists("errors.{$e->getStatusCode()}")) {
            return response()->view('errors.default', ['exception' => $e], 500, $e->getHeaders());
        }
        return parent::renderHttpException($e);
    }
}
