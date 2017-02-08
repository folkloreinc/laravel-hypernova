<?php

namespace Folklore\Hypernova;

use Closure;

class HypernovaMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        return app('hypernova')->modifyResponse($response);
    }
}
