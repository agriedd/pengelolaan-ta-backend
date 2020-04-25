<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if($exception instanceof HttpException && $request->wantsJSON())
            /*
             | jika kesalahan http
             |
             */
            return CustomHandler::http(
                $request,
                $exception,
                parent::render($request, $exception)
            );
        elseif($exception instanceof QueryException)
            /*
             | jika terjadi kesalahan query database
             |
             */
            if(!env("APP_DEBUG"))
                /*
                 | kustom query handler response
                 |
                 */
                return CustomHandler::query($request, $exception);
        elseif($request->wantsJSON())
            if(!env("APP_DEBUG"))
                /*
                 | jika kesalahan tidak diketahui atau
                 | masalah keberadaan file
                 |
                 */
                return response()->json(CustomHandler::format("terjadi sebuah kesalahan", $exception), 500);
        return parent::render($request, $exception);
    }
}
