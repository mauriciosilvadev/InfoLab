<?php

namespace Tests\Feature\Models;

use App\Models\Activity;
use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LaboratoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_laboratory_with_all_fields(): void
    {
        $data = [
            'name' => 'Laboratório de Informática',
            'building' => 'Prédio 1',
            'computers_count' => 30,
            'photos' => ['photo1.jpg', 'photo2.jpg'],
            'softwares' => ['LibreOffice', 'MATLAB', 'Python'],
        ];

        $laboratory = Laboratory::create($data);

        $this->assertDatabaseHas('laboratories', [
            'id' => $laboratory->id,
            'name' => 'Laboratório de Informática',
            'building' => 'Prédio 1',
            'computers_count' => 30,
        ]);

        $this->assertNotNull($laboratory->created_at);
        $this->assertNotNull($laboratory->updated_at);
    }

    public function test_can_create_laboratory_with_nullable_fields(): void
    {
        $data = [
            'name' => 'Laboratório de Testes',
            'building' => 'Prédio 2',
            'computers_count' => null,
            'photos' => null,
            'softwares' => null,
        ];

        $laboratory = Laboratory::create($data);

        $this->assertDatabaseHas('laboratories', [
            'id' => $laboratory->id,
            'name' => 'Laboratório de Testes',
            'building' => 'Prédio 2',
        ]);

        $this->assertNull($laboratory->computers_count);
        $this->assertNull($laboratory->photos);
        $this->assertNull($laboratory->softwares);
    }

    public function test_computers_count_is_cast_to_integer(): void
    {
        $laboratory = Laboratory::create([
            'name' => 'Test Lab',
            'building' => 'Test Building',
            'computers_count' => '25',
        ]);

        $this->assertIsInt($laboratory->computers_count);
        $this->assertEquals(25, $laboratory->computers_count);

        $retrieved = Laboratory::find($laboratory->id);
        $this->assertIsInt($retrieved->computers_count);
        $this->assertEquals(25, $retrieved->computers_count);
    }

    public function test_photos_is_cast_to_array(): void
    {
        $photos = ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'];

        $laboratory = Laboratory::create([
            'name' => 'Test Lab',
            'building' => 'Test Building',
            'photos' => $photos,
        ]);

        $this->assertIsArray($laboratory->photos);
        $this->assertEquals($photos, $laboratory->photos);

        $retrieved = Laboratory::find($laboratory->id);
        $this->assertIsArray($retrieved->photos);
        $this->assertEquals($photos, $retrieved->photos);
    }

    public function test_softwares_is_cast_to_array(): void
    {
        $softwares = ['LibreOffice', 'MATLAB', 'AutoCAD'];

        $laboratory = Laboratory::create([
            'name' => 'Test Lab',
            'building' => 'Test Building',
            'softwares' => $softwares,
        ]);

        $this->assertIsArray($laboratory->softwares);
        $this->assertEquals($softwares, $laboratory->softwares);

        $retrieved = Laboratory::find($laboratory->id);
        $this->assertIsArray($retrieved->softwares);
        $this->assertEquals($softwares, $retrieved->softwares);
    }

    public function test_empty_arrays_are_handled_correctly(): void
    {
        $laboratory = Laboratory::create([
            'name' => 'Test Lab',
            'building' => 'Test Building',
            'photos' => [],
            'softwares' => [],
        ]);

        $this->assertIsArray($laboratory->photos);
        $this->assertEmpty($laboratory->photos);
        $this->assertIsArray($laboratory->softwares);
        $this->assertEmpty($laboratory->softwares);
    }

    public function test_mass_assignment_works_correctly(): void
    {
        $data = [
            'name' => 'Mass Assignment Test',
            'building' => 'Prédio 3',
            'computers_count' => 20,
            'photos' => ['test.jpg'],
            'softwares' => ['Test Software'],
        ];

        $laboratory = Laboratory::create($data);

        $this->assertEquals('Mass Assignment Test', $laboratory->name);
        $this->assertEquals('Prédio 3', $laboratory->building);
        $this->assertEquals(20, $laboratory->computers_count);
    }

    public function test_non_fillable_attributes_are_not_assigned(): void
    {
        $laboratory = new Laboratory([
            'name' => 'Test Lab',
            'building' => 'Test Building',
            'id' => 999,
        ]);

        $this->assertEquals('Test Lab', $laboratory->name);
        $this->assertNotEquals(999, $laboratory->id);
    }

    public function test_activity_log_is_created_on_create_with_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Auth::login($user);

        $laboratory = Laboratory::create([
            'name' => 'Activity Log Test',
            'building' => 'Prédio 4',
            'computers_count' => 15,
        ]);

        $activity = Activity::where('subject_type', Laboratory::class)
            ->where('subject_id', $laboratory->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals(Activity::LOG_TYPE_SYSTEM, $activity->log_name);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals(User::class, $activity->causer_type);
        $this->assertStringContainsString('criou o laboratório', $activity->description);
        $this->assertArrayHasKey('laboratory_id', $activity->properties);
        $this->assertArrayHasKey('laboratory_name', $activity->properties);
        $this->assertEquals($laboratory->id, $activity->properties['laboratory_id']);
        $this->assertEquals('Activity Log Test', $activity->properties['laboratory_name']);
        $this->assertEquals($user->id, $activity->properties['user_id']);
        $this->assertEquals('Test User', $activity->properties['user_name']);
    }

    public function test_activity_log_is_created_on_create_without_user(): void
    {
        Auth::logout();

        $laboratory = Laboratory::create([
            'name' => 'No User Test',
            'building' => 'Prédio 5',
            'computers_count' => 10,
        ]);

        $activity = Activity::where('subject_type', Laboratory::class)
            ->where('subject_id', $laboratory->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertNull($activity->causer_id);
        $this->assertNull($activity->causer_type);
        $this->assertStringContainsString('Sistema criou o laboratório', $activity->description);
        $this->assertEquals('N/A', $activity->properties['user_name']);
        $this->assertEquals('N/A', $activity->properties['user_email']);
    }

    public function test_activity_log_is_created_on_update_with_dirty_fields_only(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $laboratory = Laboratory::create([
            'name' => 'Original Name',
            'building' => 'Original Building',
            'computers_count' => 10,
        ]);

        Activity::query()->delete();

        $laboratory->update([
            'name' => 'Updated Name',
            'building' => 'Original Building',
            'computers_count' => 20,
        ]);

        $activities = Activity::where('subject_type', Laboratory::class)
            ->where('subject_id', $laboratory->id)
            ->where('event', 'updated')
            ->get();

        $this->assertCount(1, $activities);

        $activity = $activities->first();
        $this->assertStringContainsString('editou o laboratório', $activity->description);

        $changes = $activity->changes;
        $this->assertArrayHasKey('attributes', $changes);
        $this->assertArrayHasKey('old', $changes);

        $this->assertArrayHasKey('name', $changes['attributes']);
        $this->assertArrayHasKey('computers_count', $changes['attributes']);
        $this->assertArrayNotHasKey('building', $changes['attributes']);
    }

    public function test_activity_log_is_created_on_delete(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $laboratory = Laboratory::create([
            'name' => 'To Delete',
            'building' => 'Prédio 6',
            'computers_count' => 5,
        ]);

        $laboratoryId = $laboratory->id;

        $laboratory->delete();

        $activity = Activity::where('subject_type', Laboratory::class)
            ->where('subject_id', $laboratoryId)
            ->where('event', 'deleted')
            ->first();

        $this->assertNotNull($activity);
        $this->assertStringContainsString('deletou o laboratório', $activity->description);
        $this->assertEquals(Activity::LOG_TYPE_SYSTEM, $activity->log_name);
    }

    public function test_factory_creates_valid_laboratory(): void
    {
        $laboratory = Laboratory::factory()->create();

        $this->assertDatabaseHas('laboratories', [
            'id' => $laboratory->id,
        ]);

        $this->assertNotNull($laboratory->name);
        $this->assertNotNull($laboratory->building);
        $this->assertIsInt($laboratory->computers_count);
        $this->assertIsArray($laboratory->softwares);
    }

    public function test_factory_generates_unique_names(): void
    {
        $laboratory1 = Laboratory::factory()->create();
        $laboratory2 = Laboratory::factory()->create();
        $laboratory3 = Laboratory::factory()->create();

        $this->assertNotEquals($laboratory1->name, $laboratory2->name);
        $this->assertNotEquals($laboratory2->name, $laboratory3->name);
        $this->assertNotEquals($laboratory1->name, $laboratory3->name);
    }

    public function test_can_update_individual_fields(): void
    {
        $laboratory = Laboratory::create([
            'name' => 'Original',
            'building' => 'Original Building',
            'computers_count' => 10,
        ]);

        $laboratory->update(['name' => 'Updated']);

        $this->assertDatabaseHas('laboratories', [
            'id' => $laboratory->id,
            'name' => 'Updated',
            'building' => 'Original Building',
            'computers_count' => 10,
        ]);
    }

    public function test_can_delete_laboratory(): void
    {
        $laboratory = Laboratory::create([
            'name' => 'To Delete',
            'building' => 'Prédio 7',
            'computers_count' => 8,
        ]);

        $laboratoryId = $laboratory->id;

        $laboratory->delete();

        $this->assertDatabaseMissing('laboratories', [
            'id' => $laboratoryId,
        ]);
    }
}
