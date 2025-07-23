<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

class SimpleActivityLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_logs_user_login()
    {
        // Crear un usuario
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Autenticar al usuario
        $this->actingAs($user);

        // Registrar el login
        $user->logLogin();

        // Verificar que se creó el registro de actividad
        $activity = Activity::latest()->first();

        $this->assertEquals('login', $activity->event);
        $this->assertEquals('user', $activity->log_name);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals("Usuario {$user->name} inició sesión", $activity->description);
    }

    /** @test */
    public function it_logs_important_action()
    {
        // Crear un usuario
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        // Autenticar al usuario
        $this->actingAs($user);

        // Registrar una acción importante
        $user->logImportantAction('crear_beneficiario', [
            'beneficiario' => 'Juan Pérez',
            'monto' => 1000
        ]);

        // Verificar que se creó el registro de actividad
        $activity = Activity::latest()->first();

        $this->assertEquals('important_action', $activity->event);
        $this->assertEquals('user', $activity->log_name);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals("Usuario {$user->name} realizó la acción: crear_beneficiario", $activity->description);
        $this->assertEquals([
            'beneficiario' => 'Juan Pérez',
            'monto' => 1000
        ], $activity->properties->toArray());
    }
}
