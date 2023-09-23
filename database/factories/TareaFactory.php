<?php

namespace Database\Factories;

use App\Models\Tarea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tarea>
 */
class TareaFactory extends Factory
{
    protected $model = Tarea::class;

    public function definition()
    {
        return [
            'id_usuario' => function () {
                return \App\Models\User::factory()->create()->id;
            },            
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph,
            'fecha_limite' => $this->faker->date(),
            'estado' => $this->faker->randomElement(['pendiente', 'en progreso', 'completada']), // Asume posibles estados. Ajusta según tu diseño.
        ];
    }
}
