<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 *    @OA\Info(
 *        title="Api Restfull Laravel",
 *        version="1.0",
 *    )
 */

class ApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"user"},
     *     summary="Register a new user",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function register(Request $request)
    {

        $request->validate([
            "name" => "required|string",
            "email" => "required|string|email|unique:users",
            "password" => "required|confirmed",
        ]);

        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => bcrypt($request->password),
        ]);

        return response()->json([
            "status" => true,
            "message" => "User successfully registered",
            "data" => [

            ],
        ]);

    }
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"user"},
     *     summary="Log in a user",
     *     description="Authenticates a user and returns a JWT token if successful.",
     *     operationId="loginUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="johndoe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged in"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */

    public function login(Request $request)
    {
        $request->validate(
            [
                "email" => "required|string|email",
                "password" => "required|string"
            ]
        );

        $token = auth()->attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!$token) {

            return response()->json([
                "status" => false,
                "message" => "Unauthorized",
                "data" => []
            ], 401);
        }

        return response()->json([
            "status" => true,
            "message" => "Successfully logged in",
            "token" => $token,
            "expires_in" => auth()->factory()->getTTL() * 60
        ]);
    }
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"protected"},
     *     summary="Log out the user",
     *     description="Logs out the authenticated user and invalidates the JWT token.",
     *     operationId="logoutUser",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out"),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "Successfully logged out",
            "data" => []
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"protected"},
     *     summary="Get user profile",
     *     description="Returns the profile information of the authenticated user.",
     *     operationId="getUserProfile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User profile"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
     *             ),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function profile()
    {
        $userData = request()->user();

        return response()->json([
            "status" => true,
            "message" => "User profile",
            "user" => $userData,
            "user_id" => request()->user()->id,
            "email" => request()->user()->email
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/refresh-token",
     *     tags={"protected"},
     *     summary="Refresh the JWT token",
     *     description="Refreshes the JWT token and returns a new one along with its expiration time.",
     *     operationId="refreshToken",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token successfully refreshed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token successfully refreshed"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="expires_in", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function refreshToken()
    {
        $token = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "Token successfully refreshed",
            "token" => $token,
            "expires_in" => auth()->factory()->getTTL() * 60
        ]);
    }

}
