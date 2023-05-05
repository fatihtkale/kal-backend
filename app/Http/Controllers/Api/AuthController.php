<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rules;


class AuthController extends Controller
{
    function getUser(Request $request)
    {
        return $request->user();
    }

    public function login(Request $request)
    {
        if (auth()->attempt($request->all())) {
            return response([
                'user' => auth()->user(),
                'access_token' => auth()->user()->createToken('authToken')->accessToken
            ], Response::HTTP_OK);
        }


        return response([
            'message' => 'Forkert email eller adgangskode, prøv igen',
            'wrong' => true
        ], Response::HTTP_OK);
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $company = Company::create([
           'name' => $request->company_name
        ]);
        if ($company) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'company_id' => $company->id,
                'password' => Hash::make($request->password)
            ]);
        }


        return response($user, Response::HTTP_CREATED);
    }
}
