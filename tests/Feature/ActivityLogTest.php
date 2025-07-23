<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Beneficiary;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    protected $user;
    protected $beneficiary;
    protected $payment;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario para las pruebas
        $this->user = User::factory()->create();

        // Crear un beneficiario
        $this->beneficiary = Beneficiary::create([
            'nombre' => 'Test Beneficiary',
            'ci' => '123456',
            'proyecto' => 'TEST-001',
            'idepro' => 'TEST123'
        ]);

        // Crear un pago
        $this->payment = Payment::create([
            'numprestamo' => 'TEST123',
            'fecha_pago' => now(),
            'montopago' => 1000
        ]);
    }

    /** @test */
    public function it_logs_beneficiary_creation()
    {
        $beneficiary = Beneficiary::create([
            'nombre' => 'New Beneficiary',
            'ci' => '654321',
            'proyecto' => 'TEST-002',
            'idepro' => 'TEST456'
        ]);

        $activity = Activity::latest()->first();

        $this->assertEquals('created', $activity->event);
        $this->assertEquals('beneficiary', $activity->log_name);
        $this->assertEquals('New Beneficiary', $activity->properties['attributes']['nombre']);
    }

    /** @test */
    public function it_logs_payment_creation()
    {
        $payment = Payment::create([
            'numprestamo' => 'TEST456',
            'fecha_pago' => now(),
            'montopago' => 2000
        ]);

        $activity = Activity::latest()->first();

        $this->assertEquals('created', $activity->event);
        $this->assertEquals('payment', $activity->log_name);
        $this->assertEquals(2000, $activity->properties['attributes']['montopago']);
    }

    /** @test */
    public function it_logs_user_actions()
    {
        $this->actingAs($this->user);

        // Simular login
        $this->user->logLogin();

        $activity = Activity::latest()->first();
        $this->assertEquals('login', $activity->event);
        $this->assertEquals('user', $activity->log_name);
        $this->assertEquals($this->user->id, $activity->causer_id);

        // Simular una acción importante
        $this->user->logImportantAction('test_action', ['test' => 'data']);

        $activity = Activity::latest()->first();
        $this->assertEquals('important_action', $activity->event);
        $this->assertEquals('test_action', $activity->description);
        $this->assertEquals(['test' => 'data'], $activity->properties->toArray());
    }

    /** @test */
    public function it_logs_model_updates()
    {
        // Actualizar beneficiario
        $this->beneficiary->update([
            'nombre' => 'Updated Name'
        ]);

        $activity = Activity::latest()->first();

        $this->assertEquals('updated', $activity->event);
        $this->assertEquals('beneficiary', $activity->log_name);
        $this->assertEquals('Updated Name', $activity->properties['attributes']['nombre']);
        $this->assertEquals('Test Beneficiary', $activity->properties['old']['nombre']);
    }

    /** @test */
    public function it_logs_model_deletion()
    {
        // ID del beneficiario antes de eliminarlo
        $beneficiaryId = $this->beneficiary->id;

        // Eliminar beneficiario
        $this->beneficiary->delete();

        $activity = Activity::latest()->first();

        $this->assertEquals('deleted', $activity->event);
        $this->assertEquals('beneficiary', $activity->log_name);
        $this->assertEquals($beneficiaryId, $activity->subject_id);
    }

    /** @test */
    public function it_has_correct_properties_format()
    {
        $this->beneficiary->update([
            'nombre' => 'New Name Test'
        ]);

        $activity = Activity::latest()->first();

        $this->assertArrayHasKey('attributes', $activity->properties->toArray());
        $this->assertArrayHasKey('old', $activity->properties->toArray());

        // Verificar que los atributos tienen el formato correcto
        $this->assertEquals([
            'nombre' => 'New Name Test'
        ], $activity->properties['attributes']);

        $this->assertEquals([
            'nombre' => 'Test Beneficiary'
        ], $activity->properties['old']);
    }
}
