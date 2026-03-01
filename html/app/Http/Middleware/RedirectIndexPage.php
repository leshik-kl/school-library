<?php
// app/Http/Middleware/RedirectIndexPage.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIndexPage
{
    public function handle(Request $request, Closure $next)
    {
        // Редирект с index-page на crud
        if (str_contains($request->path(), 'index-page')) {
            $newPath = str_replace('index-page', 'crud', $request->path());
            return redirect($newPath);
        }

        return $next($request);
    }
}
