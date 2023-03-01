<?php

namespace App\Http\Controllers;

use App\Models\ProguideRating;
use Illuminate\Http\Request;
use Validator;
use DB;

class ProguideRatingController extends Controller
{
    public function create_rating(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'note' => "string|max:500",
                'star' => [],
                'proguide_id' => ["required"],
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $rating = ProguideRating::updateOrCreate(
                ['student_id' => auth()->user()->id],
                [
                    'rating' => $request->star ?? 0,
                    'note' => $request->note,
                    'proguide_id' => $request->proguide_id,
                ]);

            return response(["code" => 1, "message" => "Rating added successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_reviews()
    {
        try {
            $rating = ProguideRating::with('rated_by', 'user_rated')
                ->select('proguide_id', DB::raw('AVG(rating) as average_rating'))->get();
            if ($rating->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $rating]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_review_for_a_user()
    {
        try {
            $rating = ProguideRating::with('rated_by', 'user_rated')->where('student_id', auth()->user()->id)
                ->orWhere('proguide_id', auth()->user()->id)->latest()->get();
            if ($rating->count() == 0) {
                return response(["code" => 3, "message" => "no record found"]);
            }

            return response(["code" => 1, "data" => $rating]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
