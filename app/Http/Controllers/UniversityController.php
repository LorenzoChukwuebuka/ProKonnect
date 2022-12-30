<?php

namespace App\Http\Controllers;

use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function create_university(Request $request)
    {

    }

    public function get_all_university()
    {
        try {
            $university = University::get();
            if (count($university) == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }
        } catch (\Throwable$th) {
            return response(["code" => "3", "error" => $th->getMessage()]);
        }
    }
}
