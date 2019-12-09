<?php

namespace JustusTheis\Matryoshka;

use Cache;

class FlushViews
{
    /**
     * Handle the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        Cache::tags('views')->flush();

        return $next($request);
    }
}

