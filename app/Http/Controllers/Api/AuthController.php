<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;



class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario
     * 
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Autenticación"},
     *     summary="Registrar un nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del usuario a registrar",
     *         @OA\JsonContent(
     *              required={"name", "email", "password", "password_confirmation"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", example="Password123"),
     *              @OA\Property(property="password_confirmation", type="string", example="Password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="CREATED",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="BAD REQUEST",
     *         @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string|confirmed'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response($user, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Autenticar un usuario
     * 
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Autenticación"},
     *     summary="Autenticar un usuario y devolver un token",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Credenciales para autenticación",
     *         @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", example="Password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="user", type="object", 
     *                  @OA\Property(property="id", type="number", example=1),
     *                  @OA\Property(property="name", type="string", example="John Doe"),
     *                  @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *                  @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *                  @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *              ),
     *              @OA\Property(property="token", type="string", example="token_value_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="UNAUTHORIZED",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('token')->plainTextToken;

            return response([
                'user' => $user,
                'token' => $token
            ], Response::HTTP_OK);
        } else {
            return response([
                'message' => 'Invalid credentials'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Obtener perfil del usuario autenticado
     * 
     * @OA\Get(
     *     path="/api/auth/profile",
     *     tags={"Autenticación"},
     *     summary="Obtener información del perfil del usuario autenticado",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="john.doe@example.com"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="UNAUTHORIZED",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function profile()
    {

        return response(auth()->user(), Response::HTTP_OK);
    }

    /**
     * Cerrar la sesión del usuario autenticado
     * 
     * @OA\Post(
     *     path="/api/auth/logout",
     *     tags={"Autenticación"},
     *     summary="Cerrar sesión del usuario autenticado",
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logged out")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="UNAUTHORIZED",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response([
            'message' => 'Logged out'
        ], Response::HTTP_OK);
    }
}
