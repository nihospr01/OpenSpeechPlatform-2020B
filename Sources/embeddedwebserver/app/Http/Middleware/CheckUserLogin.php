<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->session()->get('listener', 'NULL') != 'NULL'){
            return $next($request);
        }
        else{
            return redirect('/goldilocks/listener/login');
        }
    }
}
