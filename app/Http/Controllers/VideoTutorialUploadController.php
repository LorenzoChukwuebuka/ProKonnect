<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorialUpload;
use Illuminate\Http\Request;
use Validator;

class VideoTutorialUploadController extends Controller
{
    public function create_tutorial(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [

                "course_title" => "required",

            ]);
            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            if ($request->hasFile('video_file')) {

                $validator = Validator::make($request->all(), [
                    "video_file" => "mimes:mp4,mkv,flv,avi|max:1000000|required",
                ]);

                if ($validator->fails()) {
                    return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
                }

                $files = $request->video_file->store('video_tutorials', 'public');

            }

            $video_tutorial = VideoTutorialUpload::create([
                "course_title" => $request->course_title,
                "file_uploaded" => $files,
                "proguide_id" => auth()->user()->id,
            ]);

            return response(["code" => 1, "message"=>"tutorial created successfully"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_tutorials()
    {
        try {
            $video_tutorial = VideoTutorialUpload::with('proguide')->latest()->get();

            if ($video_tutorial->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $video_tutorial]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_all_tutorials_for_a_particular_proguide($id)
    {
        try {
            $video_tutorial = VideoTutorialUpload::with('proguide')->where('proguide_id', $id)->latest()->get();

            if ($video_tutorial->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $video_tutorial]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function edit_tutorial(Request $request, $id)
    {
        try {
            $video_tutorial = VideoTutorialUpload::find($id);

            $video_tutorial->course_title = $request->course_title ?? $video_tutorial->course_title;
            if ($request->hasFile('video_file')) {
                if ($request->hasFile('video_file')) {

                    $files = $request->video_file->store('video_tutorials', 'public');

                }
            }
            $video_tutorial->file_uploaded = $files ?? $video_tutorial->file_uploaded;

            $video_tutorial->save();

            return response(["code" => 1, "message" => "update successful"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function delete_tutorial($id)
    {
        try {
            $video_tutorial = VideoTutorialUpload::find($id)->delete();
            return response(["code" => 1, "message" => "delete successful"]);

        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_single_tutorial($id)
    {

        try {
            $video_tutorial = VideoTutorialUpload::with('proguide')->where('id', $id)->latest()->get();

            if ($video_tutorial->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $video_tutorial]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }

    }

    public function get_proguide_tutorial()
    {
        try {
            $video_tutorial = VideoTutorialUpload::with('proguide')->where('proguide_id', auth()->user()->id)->latest()->get();

            if ($video_tutorial->count() == 0) {
                return response(["code" => 3, "message" => "No record found"]);
            }
            return response(["code" => 1, "data" => $video_tutorial]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
