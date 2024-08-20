<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetAppLang
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userLang = Auth::user()->lang; // Get the user's language preference

            if (in_array($userLang, config('app.available_locales'))) {
                App::setLocale($userLang); // Set the locale from user's preference
            } else {
                abort(400, 'User language not available'); // Abort if the language is not available
            }
        } else {
            // Fallback to header if user is not authenticated
            $userLang = $request->header('Accept-Language');

            // Log the Accept-Language header for debugging
            Log::info('Accept-Language header: ' . $userLang);

            // Check if Accept-Language header contains a valid locale
            if (in_array($userLang, config('app.available_locales'))) {
                App::setLocale($userLang);
            } else {
                abort(400, 'Header language not available'); // Abort if the language is not available
            }
        }
        return $next($request);
    }
}
