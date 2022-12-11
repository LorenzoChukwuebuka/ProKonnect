<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        if (!Auth::guard('admin')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email or password',
            ]);
        }

        $admin = Admin::where('email', $request->email)->first();
        $token = $admin->createToken('myapptoken')->plainTextToken;
        return response()->json([
            'code' => 1,
            'message' => 'success',
            'type' => 'admin',
            'token' => $token,
            'data' => $admin,
        ]);
    }

    public function ChangePassword(Request $request)
    {
        $status = 401;
        $response = ['error' => 'Unauthorised'];
        $user = Auth::admin();

        if (!$user) {
            return response()->json(["error" => "Invalid user"]);
        }

        $password = $user->password;
        $old_pass = $request->currentPass;
        if (Hash::check($old_pass, $password)) {
            // The passwords match...
            $data = $request->newPass;

            $newPass = $request->admin()->fill([
                'password' => Hash::make($data),
            ])->save();
            return response()->json([
                'admin' => $newPass,
                'message' => 'Password Changed Successfully',
            ]);
        } else {
            return response()->json(['error' => $status]);
        }

    }

    public function forgotPassword(Request $request)
    {
        $token = Str::random(64);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:admins',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        //insert into password reset db
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        //send mail to them
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token,
            ])
            ->first();

        // if (!$updatePassword) {
        //     return response()->json(['error'=>"Invalid token"]);
        // }

        $user = Admin::where('email', $request->email)->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return response()->json(['message' => "Password has been updated"]);

    }
}
