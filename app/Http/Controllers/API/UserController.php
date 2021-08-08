<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class UserController extends Controller
{
    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        try {
            if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
                $user = Auth::user();
                $success['token'] =  $user->createToken('Vangold')-> accessToken;
                return response()->json(['success' => $success], 200);
            }
            else {
                return response()->json(['error' => 'Unauthorised'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $input = $req->all();
        $input['password'] = bcrypt($input['password']);

        try {
            $user = User::create($input);
            // $user->sendEmailVerificationNotification();

            $success['token'] =  $user->createToken('Vangold')-> accessToken;
            $success['data'] =  $user;

            return response()->json(['success' => $success], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verify(Request $req, $id)
    {
        try {
            if (!$req->hasValidSignature()) {
                return response()->json(["error" => "Invalid/Expired url provided."], 401);
            }

            $user = User::findOrFail($id);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return response()->json(['success' => "Email Verified Successfully!"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyResend()
    {
        try {
            if (auth()->user()->hasVerifiedEmail()) {
                return response()->json(["error" => "Email already verified."], 400);
            }

            auth()->user()->sendEmailVerificationNotification();

            return response()->json(["success" => "Email verification link sent on your email"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
