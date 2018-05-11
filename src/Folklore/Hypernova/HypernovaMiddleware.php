<?php

namespace Folklore\Hypernova;

use Closure;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

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

        if ($response instanceof BaseResponse &&
            $response->exception === null &&
            !$response->isRedirection() &&
            (
                !$response->headers->has('Content-Type') ||
                strpos($response->headers->get('Content-Type'), 'text/html') !== false
            )
        ) {
            return app('hypernova')->modifyResponse($response);
        }

        return $response;
    }
}
