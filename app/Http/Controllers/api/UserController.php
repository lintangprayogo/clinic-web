<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Google_Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function index($email)
    {

        $user = User::where('email', $email)->first();
        return response()->json(['status' => 'success', 'user' => $user], 200);
    }

    public function updateGoogleId(Request $request, $id)
    {
        $request->validate(['google_id' => 'required']);
        $user  = User::find($id);
        if ($user) {
            $user->google_id = $request->google_id;
            $user->save();
            return response()->json(['status' => 'success', 'user updated' => $user], 201);
        } else {
            return response()->json(['status' => 'error', 'message' => 'user not found'], 404);
        }
    }


    public function update(Request $request, $id)
    {

        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
                'google_id' => 'required',
                'ktp_number' => 'required',
                'birth_date' => 'required',
                'gender' => 'required',
            ]
        );

        $user = User::find($id);

        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $user->google_id = $request->google_id;
            $user->ktp_number = $request->ktp_number;
            $user->birth_date = $request->birth_date;
            $user->gender = $request->gender;
            $user->save();
            return response()->json(['status' => 'success', 'user updated' => $user], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'user not found'], 404);
        }
    }


    public function checkUser(Request $request)
    {

        $request->validate(
            [
                'email' => 'required|email',
            ]
        );
        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['status' => 'success', 'message' => 'Email registered', 'valid' => false], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Email not registered', 'valid' => true], 404);
        }
    }


    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        //check user exist
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'user' => $user,
        ], 200);
    }

    public function loginGoogle(Request $request)
    {

        $idToken = $request->id_token;
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($idToken);

        if ($payload) {
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $user = User::where('email', $email)->first();

            if ($user) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'message' => 'Login success',
                    'access_token' => $token,
                    'user' => $user,
                ], 200);
            } else {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                    'google_id' => $googleId
                ]);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Login success',
                'access_token' => $token,
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid Google Id',
            'status' => 'error'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccsessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Token Deleted'], 200);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email',
                'role' => 'required',
                'password' => 'required'
            ]
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json(['status' => 'success', 'data' => $user], 201);
    }
}
