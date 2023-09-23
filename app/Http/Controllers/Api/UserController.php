<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Info(
 *             title="API de prueba", 
 *             version="1.0",
 *             description="Api de prueba"
 * )
 *
 * @OA\Server(url="http://127.0.0.1:8000")
 */


class UserController extends Controller
{
    /**
     * Listado de usuarios
     * @OA\Get (
     *     path="/api/users",
     *     tags={"Usuarios"},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 type="array",
     *                 property="rows",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="name",
     *                         type="string",
     *                         example="Joe Doe"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="prueba@gmail.com"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2023-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2023-02-23T12:33:45.000000Z"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index()
    {
        try {
            $users = User::with('tareas')->get();

            return response($users, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Registrar un nuevo usuario
     * 
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Usuarios"},
     *     summary="Crear un nuevo usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Joe Doe"),
     *              @OA\Property(property="email", type="string", example="prueba@gmail.com"),
     *              @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="CREATED",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Joe Doe"),
     *              @OA\Property(property="email", type="string", example="prueba@gmail.com"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="BAD REQUEST",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Validation Error")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|string'
            ]);

            //Creación de usuario
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
     * Mostrar la información de un usuario
     * @OA\Get (
     *     path="/api/users/{id}",
     *     tags={"Usuarios"},
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Joe Doe"),
     *              @OA\Property(property="email", type="string", example="prueba@gmail.com"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="NOT FOUND",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Cliente] #id"),
     *          )
     *      )
     * )
     */
    public function show(string $id)
    {
        try {
            $user = User::with('tareas')->findOrFail($id);

            return response($user, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Actualizar la información de un usuario
     * 
     * @OA\Put(
     *     path="/api/users/{id}",
     *     tags={"Usuarios"},
     *     summary="Actualizar un usuario existente",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              @OA\Property(property="name", type="string", example="Jon Doe"),
     *              @OA\Property(property="email", type="string", example="jon.doe@gmail.com"),
     *              @OA\Property(property="password", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Jon Doe"),
     *              @OA\Property(property="email", type="string", example="jon.doe@gmail.com"),
     *              @OA\Property(property="created_at", type="string", example="2023-02-23T00:09:16.000000Z"),
     *              @OA\Property(property="updated_at", type="string", example="2023-02-23T12:33:45.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="BAD REQUEST",
     *         @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Validation Error")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email,' . $user->id,
                'password' => 'required|string'
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response($user, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * Eliminar un usuario
     * 
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     tags={"Usuarios"},
     *     summary="Eliminar un usuario existente",
     *     @OA\Parameter(
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Registro eliminado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="BAD REQUEST",
     *         @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Error message")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="NOT FOUND",
     *         @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response('Registro eliminado', Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
