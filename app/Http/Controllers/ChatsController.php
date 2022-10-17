<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\Chats;
use Illuminate\Http\Request;
use Validator;

class ChatsController extends Controller
{
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required',
                'message' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            if (auth()->user()->id > $request->receiver_id) {
                $code = auth()->user()->id . "" . $request->receiver_id;
            } else {
                $code = $request->receiver_id . "" . auth()->user()->id;

            }

            $message = Chats::create([
                'sender_id' => auth()->user()->id,
                'receiver_id' => $request->receiver_id,
                'message' => $request->message,
                'chat_code' => $code,
            ]);

            return response()->json(['code' => 1, 'success' => 'Messages sent successfully'], 200);
        } catch (\Throwable$th) {
            return $th;
        }

    }

    #show messages between two users
    public function show($id)
    {
        try {

            $messages = Chats::with(['sender', 'receiver'])
                ->where('sender_id', $id)
                ->where('receiver_id', auth()->user()->id)
                ->orWhere(function ($query) use ($id) {
                    $query->where('sender_id', auth()->user()->id);
                    $query->where('receiver_id', $id);
                })
                ->orderBy('id', 'asc')
                ->get();

            if ($messages->count() == 0) {
                return response(['code' => 3, 'message' => 'No record found']);
            }

            return response()->json(['code' => 1, 'data' => $messages]);
        } catch (\Throwable$th) {
            return $th;
            return response()->json(['code' => 3, 'error' => 'Something went wrong'], 500);
        }

    }

    #edit message

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'max:500',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }

            $message = Chats::find($id);
            if (!$message) {
                return response(['code' => 3, 'message' => "No record found"]);
            }

            $message->update([
                'message' => $request->message ?? $message->message,
                'receiver_id' => $request->receiver_id ?? $message->receiver_id,

            ]);

            return response()->json(['code' => 1, 'success' => 'Messages updated successfully'], 200);
        } catch (\Throwable$th) {
            return response()->json(['code' => 3, 'error' => 'Something went wrong'], 500);
        }
    }

    #  last messages for a user
    public function getMessages()
    {

        try {
            # this method fetches list of chatted users conversations
            $message = Chats::with(['sender', 'receiver',
            ])->where('sender_id', auth()->user()->id)
                ->orWhere('receiver_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
            //  ->groupBy('chat_code')
                ->get()->unique('chat_code');

            if ($message->count() == null) {
                return response(['code' => 3, 'message' => "No record found"]);
            }

            $toArray = new MessageResource($message);

            return response(['code' => 1, 'data' => $toArray]);

        } catch (Throwable $th) {
            return response()->json(['code' => 3, 'error' => 'Something went wrong'], 500);
        }
    }
}
