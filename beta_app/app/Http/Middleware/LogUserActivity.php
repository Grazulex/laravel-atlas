<?php

namespace App\Http\Middleware;

use App\Services\ContentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Create a new middleware instance.
     */
    public function __construct(
        private ContentService $contentService
    ) {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, \Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log request start
        if (Auth::check()) {
            Log::channel('user-activity')->info('User activity started', [
                'user_id' => Auth::id(),
                'route' => $request->route()?->getName(),
                'method' => $request->method(),
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);
        }

        $response = $next($request);

        // Log request completion
        $this->logRequestCompletion($request, $response, $startTime);

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (Auth::check()) {
            // Update user's last activity
            Auth::user()->update(['last_activity' => now()]);
            
            // Log detailed activity
            Log::channel('user-activity')->debug('Request terminated', [
                'user_id' => Auth::id(),
                'status_code' => $response->getStatusCode(),
                'memory_usage' => memory_get_peak_usage(true),
            ]);
        }
    }

    /**
     * Log request completion details
     */
    private function logRequestCompletion(Request $request, Response $response, float $startTime): void
    {
        $executionTime = microtime(true) - $startTime;
        
        if (Auth::check()) {
            Log::channel('user-activity')->info('User activity completed', [
                'user_id' => Auth::id(),
                'execution_time' => round($executionTime * 1000, 2) . 'ms',
                'status_code' => $response->getStatusCode(),
                'route' => $request->route()?->getName(),
            ]);
        }
    }
}
