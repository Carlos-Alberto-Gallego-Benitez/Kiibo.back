<?php

namespace Tests\Feature;

use App\Models\Tarea;
use App\Models\User;
use Tests\TestCase;

class TareaTest extends TestCase
{
    public function test_index_returns_all_tareas_with_usuario()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/api/tareas');

        $response->assertStatus(200);
    }

    public function test_store_can_create_a_tarea()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'id_usuario' => $user->id, // Aquí debes proporcionar un ID de un usuario que exista en tu BD o hacer uso de un factory
            'titulo' => 'Test tarea',
            'descripcion' => 'Descripción de la tarea de prueba',
            'fecha_limite' => now()->addWeek()->toDateString(),
            'estado' => 'pendiente'
        ];

        $response = $this->post('/api/tareas', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tareas', ['titulo' => 'Test tarea']);
    }

    public function test_show_returns_tarea_with_usuario()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tarea = Tarea::factory()->create(['id_usuario' => $user->id]);

        $response = $this->get("/api/tareas/{$tarea->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment($user->toArray());
    }

    public function test_update_updates_a_tarea()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $tarea = Tarea::factory()->create();

        $updatedData = [
            'id_usuario' => $user->id,
            'titulo' => 'Título Actualizado',
            'descripcion' => 'Descripción Actualizada',
            'fecha_limite' => now()->addMonth()->toDateString(),
            'estado' => 'pendiente'
        ];

        $response = $this->put(route('tareas.update', $tarea->id), $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tareas', [
            'id' => $tarea->id,
            'titulo' => 'Título Actualizado',
            'descripcion' => 'Descripción Actualizada',
        ]);
    }

    public function test_destroy_can_delete_a_tarea()
    {
        $user = User::factory()->create();
        $this->actingAs($user);


        $tarea = Tarea::factory()->create();

        $response = $this->delete("/api/tareas/{$tarea->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tareas', ['id' => $tarea->id]);
    }
}
