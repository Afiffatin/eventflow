<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Authenticate user and generate JWT token.
     */
    public function login(Request $request)
    {
        // Mocking advanced JWT authentication for PRK42-32
        return response()->json([
            'status' => 'success',
            'token' => 'mock-jwt-token-12345',
            'message' => 'User authenticated successfully.'
        ]);
    }
}
