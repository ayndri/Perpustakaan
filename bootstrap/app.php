<?php

use App\Exceptions\CirculationException;
use App\Http\Middleware\SweepCirculation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Di Vercel, HTTPS berhenti di proxy; tanpa ini url() dan redirect jadi http://
        $middleware->trustProxies(at: '*');
        $middleware->appendToGroup('web', SweepCirculation::class);
        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.login')
            : route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => $request->is('admin', 'admin/*')
            ? route('admin.dashboard')
            : route('home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Aturan sirkulasi yang dilanggar bukan error: kembalikan ke halaman sebelumnya dengan pesannya.
        $exceptions->render(fn (CirculationException $e) => back()->with('error', $e->getMessage()));
        $exceptions->dontReport(CirculationException::class);
    })->create();
