<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_contains_livewire_component()
    {
        $this->get('/register')->assertSeeLivewire('auth.register');
    }

    public function test_can_register()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez@gmail.com')
            ->set('password', 'dsanchez')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertRedirect('/');
    
        $this->assertTrue(User::whereEmail('dsanchez@gmail.com')->exists());

        $this->assertEquals('dsanchez@gmail.com', auth()->user()->email);
    }

    public function test_email_is_required()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', '')
            ->set('password', 'dsanchez')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertHasErrors(['email' => 'required']);
    }

    public function test_email_is_valid_email()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez')
            ->set('password', 'dsanchez')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_email_hasnt_been_taken_already()
    {
        User::create([
            'name' => 'Diego Sanchez',
            'email' => 'dsanchez@gmail.com',
            'password' => bcrypt('dsanchez'),
        ]);

        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez@gmail.com')
            ->set('password', 'dsanchez')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_password_is_required()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez@gmail.com')
            ->set('password', '')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertHasErrors(['password' => 'required']);
    }
    
    public function test_password_is_minimum_of_six_characters()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez@gmail.com')
            ->set('password', 'se')
            ->set('passwordConfirmation', 'dsanchez')
            ->call('register')
            ->assertHasErrors(['password' => 'min']);
    }

    public function test_passwords_matches_password_confirmation()
    {
        Livewire::test('auth.register')
            ->set('name', 'Diego Sanchez')
            ->set('email', 'dsanchez@gmail.com')
            ->set('password', 'dsanchez')
            ->set('passwordConfirmation', 'no-dsanchez')
            ->call('register')
            ->assertHasErrors(['password' => 'same']);
    }

    public function test_see_email_hasnt_been_taken_validation_message_as_user_types()
    {
        User::create([
            'name' => 'Diego Sanchez',
            'email' => 'dsanchez@gmail.com',
            'password' => bcrypt('dsanchez'),
        ]);

        Livewire::test('auth.register')
            ->set('email', 'dsanchezh@gmail.com')
            ->assertHasNoErrors()
            ->set('email', 'dsanchez@gmail.com');
    }
}
