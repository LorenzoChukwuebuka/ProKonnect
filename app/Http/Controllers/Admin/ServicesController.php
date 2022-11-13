<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function create_services(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "services" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Service::create([
                array_merge($validator->validated()),
            ]);

            return response(["code" => 1, "message" => "Qualification created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th]);
        }
    }


    public function get_all_services(){
        
    }
}
