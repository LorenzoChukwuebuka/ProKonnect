<?php

namespace App\Http\Controllers\Admin;

use Validator;
use Illuminate\Http\Request;
use App\Models\Qualifications;
use App\Http\Controllers\Controller;

class QualificationsController extends Controller
{
    public function create(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                "qualification" => 'required|max:255|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            Qualifications::create([
                array_merge($validator->validated()),
            ]);

            return response(["code" => 1, "message" => "Qualification created successfully"]);

        } catch (\Throwable$th) {
            return $th;
        }
    }

    public function show($id){

    }


    public function findAll(){

    }

    public function update(){

    }

    public function delete(){

    }
}
