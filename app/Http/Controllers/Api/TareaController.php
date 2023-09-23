<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class TareaController extends Controller
{
    /**
     * Listado de usuarios
     * @OA\Get (
     *     path="/api/tareas",
     *     tags={"Tareas"},
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
     *                         property="id_usuario",
     *                         type="number",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="titulo",
     *                         type="string",
     *                         example="prueba"
     *                     ),
     *                     @OA\Property(
     *                         property="descripcion",
     *                         type="string",
     *                         example="prueba"
     *                     ),
     *                     @OA\Property(
     *                         property="fecha_limite",
     *                         type="string",
     *                         example="2023-02-23"
     *                     ),
     *                     @OA\Property(
     *                         property="estado",
     *                         type="string",
     *                         example="Pendiente"
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
            $tareas = Tarea::with('usuario')->get();

            return response($tareas, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

/**
 * @OA\Post(
 *     path="/api/tareas",
 *     tags={"Tareas"},
 *     summary="Crear una nueva tarea",
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="id_usuario", type="integer", example=1),
 *             @OA\Property(property="titulo", type="string", example="Tarea 1"),
 *             @OA\Property(property="descripcion", type="string", example="Descripcion de la tarea"),
 *             @OA\Property(property="fecha_limite", type="string", format="date"), example="2023-02-23",
 *             @OA\Property(property="estado", type="string", example="Pendiente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="CREATED"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="BAD REQUEST"
 *     )
 * )
 */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_usuario' => 'required|integer',
                'titulo' => 'required|string',
                'descripcion' => 'required|string',
                'fecha_limite' => 'required|date',
                'estado' => 'required|string'
            ]);


            $tarea = Tarea::create([
                'id_usuario' => $request->id_usuario,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_limite' => $request->fecha_limite,
                'estado' => $request->estado
            ]);

            return response($tarea, Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

/**
 * @OA\Get(
 *     path="/api/tareas/{id}",
 *     tags={"Tareas"},
 *     summary="Mostrar una tarea específica",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent(
 *             @OA\Property(property="id_usuario", type="integer", example=1),
 *             @OA\Property(property="titulo", type="string", example="prueba"),
 *             @OA\Property(property="descripcion", type="string", example="prueba"),
 *             @OA\Property(property="fecha_limite", type="string", format="date", example="2023-6-7"),
 *             @OA\Property(property="estado", type="string", example="pendiente")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="BAD REQUEST"
 *     )
 * )
 */

    public function show(string $id)
    {
        try {
            $tarea = Tarea::with('usuario')->find($id);

            return response($tarea, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

/**
 * @OA\Put(
 *     path="/api/tareas/{id}",
 *     tags={"Tareas"},
 *     summary="Actualizar una tarea específica",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="id_usuario", type="integer"),
 *             @OA\Property(property="titulo", type="string"),
 *             @OA\Property(property="descripcion", type="string"),
 *             @OA\Property(property="fecha_limite", type="string", format="date"),
 *             @OA\Property(property="estado", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="BAD REQUEST"
 *     )
 * )
 */
    public function update(Request $request, string $id)
    {
        try {
            $tarea = Tarea::findOrFail($id);

            $request->validate([
                'id_usuario' => 'required|integer',
                'titulo' => 'required|string',
                'descripcion' => 'required|string',
                'fecha_limite' => 'required|date',
                'estado' => 'required|string'
            ]);

            $tarea->update([
                'id_usuario' => $request->id_usuario,
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'fecha_limite' => $request->fecha_limite,
                'estado' => $request->estado
            ]);

            return response($tarea, Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

/**
 * @OA\Delete(
 *     path="/api/tareas/{id}",
 *     tags={"Tareas"},
 *     summary="Eliminar una tarea específica",
 *     @OA\Parameter(
 *         in="path",
 *         name="id",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="BAD REQUEST"
 *     )
 * )
 */
    public function destroy(string $id)
    {
        try {
            $tarea = Tarea::findOrFail($id);

            $tarea->delete();

            return response([
                'message' => 'Tarea eliminada correctamente'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response([
                'error' => $th->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
