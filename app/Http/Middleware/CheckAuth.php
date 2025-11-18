<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log all cookies and session info for debugging
        \Log::info('CheckAuth middleware - Request details', [
            'path' => $request->path(),
            'has_session' => $request->hasSession(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : 'no-session',
            'cookies' => $request->cookies->all(),
            'session_data' => $request->hasSession() ? $request->session()->all() : 'no-session',
            'user_id_from_session' => $request->hasSession() ? $request->session()->get('user_id') : null,
        ]);
        
        // Get user ID from session
        $userId = $request->hasSession() ? $request->session()->get('user_id') : null;
        
        if (!$userId) {
            \Log::warning('CheckAuth: User not authenticated', [
                'path' => $request->path(),
                'has_session' => $request->hasSession(),
                'session_id' => $request->hasSession() ? $request->session()->getId() : 'no-session',
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            return redirect()->route('login');
        }
        
        \Log::info('CheckAuth: User authenticated', [
            'user_id' => $userId,
            'path' => $request->path(),
        ]);
        
        return $next($request);
    }
}

