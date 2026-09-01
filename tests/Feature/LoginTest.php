<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

it('renders the inertia login page', function () {
    $this->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

it('logs an admin in and redirects to the admin dashboard', function () {
    $user = User::factory()->admin()->create([
        'password' => Hash::make('secret123'),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('secret123'),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});
