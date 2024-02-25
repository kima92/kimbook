<?php

namespace App\Http\Middleware;

use App\Utils\Vite;
use Illuminate\Support\Collection;

class AddLinkHeadersForPreloadedAssets
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return tap($next($request), function ($response) {
            if (app(Vite::class)->preloadedAssets() !== []) {
                $response->header('Link', Collection::make(app(Vite::class)->preloadedAssets())
                    ->map(fn ($attributes, $url) => "<{$url}>; ".implode('; ', $attributes))
                    ->join(', '));
            }
        });
    }
}
