<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class isAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $user = getUser();
        if(Auth::check() && isset($user) && $user->status != 1)
        {
             return response()->json([
                'success'=> false,
                'message'=> 'User not authenticate.',
                'payload'=> [],
                'code'=> '401'
            ]);
        }
        return $next($request);
    }
}
