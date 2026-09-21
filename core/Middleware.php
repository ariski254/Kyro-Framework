<?php

namespace Core;

use Closure;

interface Middleware {
    public function handle(Request $request, Closure $next);
}
