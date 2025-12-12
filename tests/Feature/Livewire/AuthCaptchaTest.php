<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Modals\Auth;
use App\Models\User;
use App\Services\RecaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class AuthCaptchaTest extends TestCase
{
	use RefreshDatabase;

	public function test_auth_modal_enables_v2_on_v3_failure()
	{
		// Mock RecaptchaService
		$mockRecaptcha = Mockery::mock(RecaptchaService::class);
		$mockRecaptcha->shouldReceive('verifyV3')
			->once()
			->andReturn(['success' => false]);

		// We need to bind the mock to the container
		$this->app->instance(RecaptchaService::class, $mockRecaptcha);

		// Seed required level
		\Illuminate\Support\Facades\DB::table('levels')->insert([
			'id' => 1,
			'title' => 'Default Level',
			'fee' => 10,
			'space' => 0.3,
			'sales_treshold' => 100,
		]);

		// Create a user
		$user = User::factory()->create([
			'email' => 'test@example.com',
			'password' => bcrypt('password'),
			'active' => true,
		]);

		Livewire::test(Auth::class)
			->set('step', 2)
			->set('user_id', \Illuminate\Support\Facades\Crypt::encrypt($user->id))
			->set('recaptcha_token', 'dummy-token')
			->set('form.email', 'test@example.com')
			->set('form.password', 'password')
			->call('attempt')
			->assertSet('showRecaptchaV2', true)
			->assertHasErrors(['recaptcha_token']); // Check validation error
	}
}
