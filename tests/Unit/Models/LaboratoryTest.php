<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Spatie\Activitylog\Contracts\Activity as ActivityContract;
use Spatie\Activitylog\LogOptions;
use Tests\TestCase;

class LaboratoryTest extends TestCase
{
    public function test_get_activitylog_options_returns_correct_configuration(): void
    {
        $laboratory = new Laboratory();

        $options = $laboratory->getActivitylogOptions();

        $this->assertInstanceOf(LogOptions::class, $options);
    }

    public function test_fillable_attributes_are_correct(): void
    {
        $expectedFillable = ['name', 'building', 'computers_count', 'photos', 'softwares'];

        $this->assertEquals($expectedFillable, (new Laboratory())->getFillable());
    }

    public function test_casts_are_configured_correctly(): void
    {
        $laboratory = new Laboratory();

        $casts = $laboratory->getCasts();

        $this->assertEquals('integer', $casts['computers_count']);
        $this->assertEquals('array', $casts['photos']);
        $this->assertEquals('array', $casts['softwares']);
    }

    public function test_tap_activity_sets_correct_description_for_created_event(): void
    {
        $user = new User();
        $user->id = 1;
        $user->setAttribute('name', 'João Silva');
        $user->setAttribute('email', 'joao@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 1;
        $laboratory->name = 'Laboratório de Testes';
        $laboratory->building = 'Prédio 1';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'created');

        $expectedDescription = 'Usuário João Silva criou o laboratório Laboratório de Testes';
        $this->assertEquals($expectedDescription, $activity->description);
        $this->assertEquals(Activity::LOG_TYPE_SYSTEM, $activity->log_name);
        $this->assertEquals(1, $activity->causer_id);
        $this->assertEquals(User::class, $activity->causer_type);
    }

    public function test_tap_activity_sets_correct_description_for_updated_event(): void
    {
        $user = new User();
        $user->id = 2;
        $user->setAttribute('name', 'Maria Santos');
        $user->setAttribute('email', 'maria@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 2;
        $laboratory->name = 'Laboratório de Redes';
        $laboratory->building = 'Prédio 2';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'updated');

        $expectedDescription = 'Usuário Maria Santos editou o laboratório Laboratório de Redes';
        $this->assertEquals($expectedDescription, $activity->description);
    }

    public function test_tap_activity_sets_correct_description_for_deleted_event(): void
    {
        $user = new User();
        $user->id = 3;
        $user->setAttribute('name', 'Pedro Costa');
        $user->setAttribute('email', 'pedro@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 3;
        $laboratory->name = 'Laboratório de Química';
        $laboratory->building = 'Prédio 3';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'deleted');

        $expectedDescription = 'Usuário Pedro Costa deletou o laboratório Laboratório de Química';
        $this->assertEquals($expectedDescription, $activity->description);
    }

    public function test_tap_activity_sets_correct_description_for_default_event(): void
    {
        $user = new User();
        $user->id = 4;
        $user->setAttribute('name', 'Ana Lima');
        $user->setAttribute('email', 'ana@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 4;
        $laboratory->name = 'Laboratório de Física';
        $laboratory->building = 'Prédio 4';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'unknown_event');

        $expectedDescription = 'Atividade realizada no laboratório Laboratório de Física';
        $this->assertEquals($expectedDescription, $activity->description);
    }

    public function test_tap_activity_sets_properties_correctly_with_user(): void
    {
        $user = new User();
        $user->id = 5;
        $user->setAttribute('name', 'Carlos Oliveira');
        $user->setAttribute('email', 'carlos@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 5;
        $laboratory->name = 'Laboratório de Informática';
        $laboratory->building = 'Prédio 5';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'created');

        $this->assertIsArray($activity->properties);
        $this->assertEquals(5, $activity->properties['laboratory_id']);
        $this->assertEquals('Laboratório de Informática', $activity->properties['laboratory_name']);
        $this->assertEquals('Prédio 5', $activity->properties['building']);
        $this->assertEquals(5, $activity->properties['user_id']);
        $this->assertEquals('Carlos Oliveira', $activity->properties['user_name']);
        $this->assertEquals('carlos@example.com', $activity->properties['user_email']);
    }

    public function test_tap_activity_sets_properties_correctly_without_user(): void
    {
        Auth::shouldReceive('user')->andReturn(null);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 6;
        $laboratory->name = 'Laboratório de Biologia';
        $laboratory->building = 'Prédio 6';

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn([]);
        $activity->properties = [];
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'created');

        $expectedDescription = 'Usuário Sistema criou o laboratório Laboratório de Biologia';
        $this->assertEquals($expectedDescription, $activity->description);
        $this->assertNull($activity->causer_id);
        $this->assertNull($activity->causer_type);
        $this->assertEquals('N/A', $activity->properties['user_name']);
        $this->assertEquals('N/A', $activity->properties['user_email']);
        $this->assertNull($activity->properties['user_id']);
    }

    public function test_tap_activity_handles_existing_properties_collection(): void
    {
        $user = new User();
        $user->id = 7;
        $user->setAttribute('name', 'Test User');
        $user->setAttribute('email', 'test@example.com');
        $user->syncOriginal();

        Auth::shouldReceive('user')->andReturn($user);
        $this->app['request'] = Request::create('/', 'GET', [], [], [], ['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'Test Agent']);

        $laboratory = new Laboratory();
        $laboratory->id = 7;
        $laboratory->name = 'Test Lab';
        $laboratory->building = 'Test Building';

        $existingProperties = collect(['existing_key' => 'existing_value']);

        $activity = Mockery::mock(ActivityContract::class);
        $activity->shouldReceive('getAttribute')->with('properties')->andReturn($existingProperties);
        $activity->properties = $existingProperties;
        $activity->log_name = null;
        $activity->causer_id = null;
        $activity->causer_type = null;
        $activity->description = null;

        $laboratory->tapActivity($activity, 'created');

        $this->assertArrayHasKey('existing_key', $activity->properties);
        $this->assertArrayHasKey('laboratory_id', $activity->properties);
        $this->assertEquals('existing_value', $activity->properties['existing_key']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
