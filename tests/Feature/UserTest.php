<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_users()
    {
        // Crear un usuario y autenticarlo
        $user = User::factory()->create();
        $this->actingAs($user);

        // Crear otros usuarios usando un factory
        $otherUsers = User::factory(5)->create();

        // Hacer una solicitud GET al endpoint que utiliza el método index()
        $response = $this->get('/api/users');

        // Asegurarse de que la respuesta tenga estado 200 OK
        $response->assertStatus(200);

        // Asegurarse de que la respuesta contenga todos los usuarios
        $allUsers = User::all();
        $response->assertJson($allUsers->toArray());
    }


    public function test_store_can_create_a_user()
    {
        // Autentica a un usuario existente (si es necesario)
        $authUser = User::factory()->create();
        $this->actingAs($authUser);

        // Datos del nuevo usuario a crear
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        // Hacer una solicitud POST al endpoint que utiliza el método store()
        $response = $this->post('/api/users', $userData);

        // Asegurarse de que la respuesta tenga estado 201 Created
        $response->assertStatus(201);

        // Asegurarse de que el usuario fue creado en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
            // No comprobamos la contraseña porque está hasheada
        ]);

        // Asegurarse de que la respuesta contenga los datos del nuevo usuario
        $response->assertJsonFragment([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);
    }

    public function test_store_returns_error_for_invalid_input()
    {
        // Autentica a un usuario existente (si es necesario)
        $authUser = User::factory()->create();
        $this->actingAs($authUser);

        // Datos del nuevo usuario a crear (faltantes o inválidos)
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => ''
        ];

        // Hacer una solicitud POST al endpoint
        $response = $this->post('/api/users', $userData);

        // Asegurarse de que la respuesta tenga estado 400 Bad Request
        $response->assertStatus(400);
    }


  public function test_show_returns_user_with_tareas()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $user = User::factory()->create();
        
       
        $response = $this->get("/api/users/{$user->id}");
   
        $response->assertStatus(200);      
        
    } 

    public function test_update_can_modify_a_user()
{
    // Crear un usuario
    $user = User::factory()->create();
    $this->actingAs($user);

    // Datos actualizados para el usuario
    $updatedData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword123'
    ];

    // Actualizar el usuario
    $response = $this->put("/api/users/{$user->id}", $updatedData);

    // Asegurarse de que la respuesta tenga estado 200 OK
    $response->assertStatus(200);


    // Asegurarse de que el usuario se haya actualizado en la base de datos
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com'
    ]);
}

public function test_destroy_can_delete_a_user()
{
    // Crear un usuario
    $user = User::factory()->create();
    $this->actingAs($user);

    // Eliminar el usuario
    $response = $this->delete("/api/users/{$user->id}");

    // Asegurarse de que la respuesta tenga estado 200 OK
    $response->assertStatus(200);

    // Asegurarse de que el usuario ya no esté en la base de datos
    $this->assertDatabaseMissing('users', [
        'id' => $user->id
    ]);
}


}
