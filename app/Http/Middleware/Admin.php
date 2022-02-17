<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class Admin
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
        if(!\Auth::check()){
            return redirect(route('login'));
        }
        if(!$request->user()->user_type_id == '1'){
            return response()->json([
                'success'=> false,
                'message'=> 'User not authorized.',
                'payload'=> [],
                'code'=> '401'
            ]);
        }
        return $next($request);
    }
}
