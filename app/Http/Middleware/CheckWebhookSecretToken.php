<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;

class CheckWebhookSecretToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {    
        $secretKey = $request->header('Secret-Key');
        if($secretKey){
            if($secretKey == env('WEBHOOK_SECRET_KEY')){
                return $next($request);
            }else{
                return redirect()->back();
            }
        }
        return redirect()->back();
    }
}
