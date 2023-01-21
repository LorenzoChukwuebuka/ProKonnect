<?php

namespace App\Http\Controllers;

use App\Models\BadWords;
use Illuminate\Http\Request;
use Validator;

class BadWordsController extends Controller
{
    public function create_bad_words(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "bad_words" => "required",
            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            $badWords = BadWords::create([
                "word" => $request->bad_words,
            ]);

            return response(["code" => 1, "message" => "bad words created successfully"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_bad_words()
    {
        try {
            $badWord = BadWords::get();
            if (count($badWord) == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }
            return $badWord;
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_bad_word($id)
    {
        $badWord = Badwords::find($id)->delete();
        return response(["code" => 1, "message" => "bad word deleted successfully"]);

    }

 

}
