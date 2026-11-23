<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\RpjmdContent;
use Tests\TestCase;

class RpjmdContentTest extends TestCase
{
    private function defaults(): array
    {
        return json_decode(file_get_contents(resource_path('data/rpjmd.json')), true);
    }

    public function test_public_page_shows_five_pairs_and_navigation(): void
    {
        $data = $this->defaults();
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldReceive('get')->andReturn($data));
        $response = $this->get(route('publik.rpjmd'))->assertOk()->assertSee('Misi 5')->assertSee('Tujuan 5');
        foreach ($data['pairs'] as $pair) {
            $response->assertSee($pair['misi'])->assertSee($pair['tujuan']);
        }
    }

    public function test_cms_requires_super_admin(): void
    {
        $this->get(route('cms.rpjmd.edit'))->assertRedirect(route('login'));
        $this->actingAs(new User(['role' => 'operator_opd']))->get(route('cms.rpjmd.edit'))->assertForbidden();
        $this->put(route('cms.rpjmd.update'), $this->defaults())->assertForbidden();
    }

    public function test_admin_can_save_exactly_five_pairs(): void
    {
        $data = $this->defaults();
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldReceive('save')->once()->with($data));
        $this->actingAs(new User(['role' => 'super_admin']))
            ->put(route('cms.rpjmd.update'), $data)->assertRedirect(route('cms.rpjmd.edit'));
    }

    public function test_incomplete_pairs_are_rejected(): void
    {
        $data = $this->defaults();
        array_pop($data['pairs']);
        $this->mock(RpjmdContent::class, fn ($mock) => $mock->shouldNotReceive('save'));
        $this->actingAs(new User(['role' => 'super_admin']))
            ->put(route('cms.rpjmd.update'), $data)->assertSessionHasErrors('pairs');
    }
}
