<?php

namespace Tests\Feature;

use App\Models\FirstTimer;
use App\Models\User;
use App\Models\Church;
use App\Models\ChurchCategory;
use App\Models\ChurchGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirstTimerContactCheckTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Setup necessary dependencies
        $category = ChurchCategory::create(['name' => 'ZONAL CHURCH']);
        $group = ChurchGroup::create([
            'church_category_id' => $category->id,
            'group_name' => 'Test Group',
            'pastor_name' => 'Test Pastor',
            'pastor_contact' => '0240000001'
        ]);
        $church = Church::create([
            'church_group_id' => $group->id,
            'name' => 'Test Church',
            'leader_name' => 'Test Leader',
            'leader_contact' => '0240000002'
        ]);

        FirstTimer::create([
            'church_id' => $church->id,
            'full_name' => 'John Doe',
            'primary_contact' => '0241234567',
            'gender' => 'Male',
            'date_of_birth' => '01-01',
            'residential_address' => 'Test Address',
            'date_of_visit' => now(),
            'marital_status' => 'Single',
        ]);
    }

    /** @test */
    public function it_returns_exists_true_for_duplicate_contact()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('first-timers.check-contact', ['contact' => '0241234567']));

        $response->assertStatus(200)
            ->assertJson([
                'exists' => true,
                'visitor' => [
                    'name' => 'John Doe'
                ]
            ]);
    }

    /** @test */
    public function it_returns_exists_false_for_unique_contact()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('first-timers.check-contact', ['contact' => '0249999999']));

        $response->assertStatus(200)
            ->assertJson([
                'exists' => false,
                'visitor' => null
            ]);
    }

    /** @test */
    public function it_handles_empty_contact()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('first-timers.check-contact', ['contact' => '']));

        $response->assertStatus(200)
            ->assertJson([
                'exists' => false,
                'visitor' => null
            ]);
    }
}
