<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Tangani CSRF Token Expired (419) secara anggun agar user tidak melihat error 419
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                return redirect()->route('login');
            }

            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman.',
                ], 419);
            }

            return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir karena tidak aktif cukup lama. Silakan login kembali.');
        });
    }
}
