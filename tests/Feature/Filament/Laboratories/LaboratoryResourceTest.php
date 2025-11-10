<?php

namespace Tests\Feature\Filament\Laboratories;

use App\Filament\Resources\Laboratories\LaboratoryResource;
use App\Filament\Resources\Laboratories\Pages\CreateLaboratory;
use App\Filament\Resources\Laboratories\Pages\EditLaboratory;
use App\Filament\Resources\Laboratories\Pages\ListLaboratories;
use App\Models\Activity;
use App\Models\Laboratory;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LaboratoryResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_list_page_displays_laboratories(): void
    {
        $this->actingAs($this->createFilamentUser());

        $laboratories = Laboratory::factory()->count(3)->create();

        Livewire::test(ListLaboratories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($laboratories);
    }

    public function test_can_create_laboratory_from_create_page(): void
    {
        $this->actingAs($this->createFilamentUser());

        $data = Laboratory::factory()->make([
            'computers_count' => 25,
            'softwares' => ['LibreOffice', 'MATLAB'],
        ]);

        $component = Livewire::test(CreateLaboratory::class)
            ->fillForm([
                'name' => $data->name,
                'building' => $data->building,
                'computers_count' => $data->computers_count,
                'photos' => [],
                'softwares' => $data->softwares,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect(LaboratoryResource::getUrl('index'));

        $this->assertDatabaseHas(Laboratory::class, [
            'name' => $data->name,
            'building' => $data->building,
            'computers_count' => $data->computers_count,
        ]);

        $recordKey = $component->instance()->record->getKey();

        $storedSoftwares = Laboratory::query()->findOrFail($recordKey)->softwares;

        $normalizedSoftwares = is_array($storedSoftwares)
            ? $storedSoftwares
            : array_filter(array_map('trim', explode(',', (string) $storedSoftwares)));

        $this->assertEqualsCanonicalizing(
            ['LibreOffice', 'MATLAB'],
            $normalizedSoftwares
        );

        $this->assertNotNull(Activity::query()->where('description', 'like', '%criou o laboratório%')->first());
    }

    public function test_validation_errors_are_reported_on_create(): void
    {
        $this->actingAs($this->createFilamentUser());

        Livewire::test(CreateLaboratory::class)
            ->fillForm([
                'name' => null,
                'building' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'name' => ['required'],
                'building' => ['required'],
            ])
            ->assertNotNotified();

        $this->assertDatabaseCount(Laboratory::class, 0);
    }

    public function test_can_update_laboratory_from_edit_page(): void
    {
        $this->actingAs($this->createFilamentUser());

        $laboratory = Laboratory::factory()->create([
            'name' => 'Laboratório de Redes',
            'building' => 'Prédio 1',
            'softwares' => ['Wireshark'],
        ]);

        Livewire::test(EditLaboratory::class, ['record' => $laboratory->getKey()])
            ->assertOk()
            ->fillForm([
                'name' => 'Laboratório de Redes e Segurança',
                'building' => 'Prédio 2',
                'computers_count' => 42,
                'photos' => [],
                'softwares' => ['Wireshark', 'Nmap'],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect(LaboratoryResource::getUrl('index'));

        $this->assertDatabaseHas(Laboratory::class, [
            'id' => $laboratory->id,
            'name' => 'Laboratório de Redes e Segurança',
            'building' => 'Prédio 2',
            'computers_count' => 42,
        ]);

        $this->assertNotNull(Activity::query()->where('description', 'like', '%editou o laboratório%')->first());
    }

    protected function createFilamentUser(): User
    {
        $user = User::factory()->create();

        $role = Role::firstOrCreate(['name' => User::ADMIN_ROLE, 'guard_name' => 'web']);
        $user->assignRole($role);

        return $user;
    }
}
