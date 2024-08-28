<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use App\Traits\LoggerTrait;

class Handler extends ExceptionHandler
{
    use LoggerTrait;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        if(!app()->runningInConsole()){
            $this->reportable(function (Throwable $e) {
                $error = $e->getCode() > 0 ? 'Error '.$e->getCode().": \"" : '';
                $error .= $e->getMessage();
                $error .= $e->getCode() > 0 ? "\"" : '';
                $error .= $e->getLine() > 0 ? " at Line ".$e->getLine() : '';
                $this->logError($error, "SYSTEM", 0, null, $e->getTrace());
            });
        }
    }
}
