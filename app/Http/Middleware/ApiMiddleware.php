<?php

namespace App\Http\Middleware;

use App\Models\Apikey;
use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
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
        if (!request('api_id') && !request('api_key')) {
            return response()->json(['message' => "No api key found"], 401);
        }

        $api = apikey::where(['api_id' => request('api_id'), 'status' => 'Active'])->first();

        if ($api === null) {
            return response()->json(["message" => "No  Api id was detected"], 404);
        }

        if (!password_verify(request('api_key'), $api->api_key)) {
            return response()->json(["message" => "Wrong API key provided"]);
        }

        return $next($request);
    }
}
