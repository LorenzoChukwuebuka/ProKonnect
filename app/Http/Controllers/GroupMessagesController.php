<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Validator;

class GroupMessagesController extends Controller
{
    public function create_group_messages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "group_id" => "required",
                "user_id" => [],
                "message" => [],

            ]);

            if ($validator->fails()) {
                return response()->json(["code" => 3, 'error' => $validator->errors()], 401);
            }

            if ($request->hasFile('file')) {
                $validate = Validator::make($request->all(), [
                    'file' => 'mimes:jpeg,png,jpg,gif,svg,pdf,docx|max:10000',
                ]);
                if ($validate->fails()) {
                    return response()->json(["code" => 3, 'error' => $validate->errors()->first()]);
                }
                $files = $request->file->store('group_messages_files', 'public');
            }

            $createMessage = GroupMessage::create([
                "user_id" => auth()->user()->id,
                "group_id" => $request->group_id,
                "message" => $request->message,
                "files" => $file ?? null,
                "chat_code" => auth()->user()->id . "" . $request->group_id,
            ]);

            return response(["code" => 1, "message" => "message sent"]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_group_messages($id)
    {
        try {
            $groupMessages = GroupMessage::with('user')
                ->where('group_id', $id)
                ->where('user_id', auth()->user()->id)
                ->orderBy('id', 'asc')
                ->get();

            if ($groupMessages->count() == 0) {
                return response(['code' => 3, 'message' => "No record found"]);
            }
            return response(["code" => 1, "data" => $groupMessages]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }

    public function get_last_messages_in_a_group()
    {
        try {
            $message = GroupMessage::with('user')
                ->where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->get()->unique('chat_code');

            if ($message->count() == null) {
                return response(['code' => 3, 'message' => "No record found"]);
            }

            $toArray = new MessageResource($message);

            return response(["code" => 1, "data" => $message]);
        } catch (\Throwable$th) {
            return response(["code" => 3, "error" => $th->getMessage()]);
        }
    }
}
