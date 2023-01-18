<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Validator;

class RatingController extends Controller
{
    public function create_rating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'note' => "string|max:500",
                'star' => [],
            ]);

            $rating = Rating::create([
                'user_id' => auth()->user()->id,
                'rating' => $request->star ?? 0,
                'note' => $request->note,
            ]);

            return response(["code" => 1, "message" => "Rating added successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_reviews()
    {
        try {
            $rating = Rating::with('user_review')->latest()->get();
            if ($rating->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $rating]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
