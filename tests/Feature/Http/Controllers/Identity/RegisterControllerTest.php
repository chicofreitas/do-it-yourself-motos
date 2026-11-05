<?php

use App\Actions\RegisterUser;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Mockery;

uses(RefreshDatabase::class);

it('returns 201 and creates a hashed user through the registration action', function () {
    Notification::fake();

    $password = 'StrongPass1!';
    $registerUser = Mockery::mock(RegisterUser::class)->makePartial();
    $registerUser->shouldReceive('handle')->once()->passthru();
    $this->app->instance(RegisterUser::class, $registerUser);

    $response = $this->postJson('/api/v1/register', [
        'email' => 'rider@example.com',
        'password' => $password,
        'password_confirmation' => $password,
    ]);

    $response->assertCreated()
        ->assertJsonPath('email', 'rider@example.com')
        ->assertJsonMissingPath('password')
        ->assertJsonMissingPath('password_confirmation');

    $this->assertDatabaseHas('users', ['email' => 'rider@example.com']);

    $user = User::where('email', 'rider@example.com')->firstOrFail();

    expect($user->password)
        ->not->toBe($password);
    expect(Hash::check($password, $user->password))->toBeTrue();

    Notification::assertSentTo($user, UserRegistered::class);
});

it('returns 422 when the email is already registered', function () {
    User::factory()->create(['email' => 'rider@example.com']);
    Notification::fake();

    $response = $this->postJson('/api/v1/register', [
        'email' => 'rider@example.com',
        'password' => 'StrongPass1!',
        'password_confirmation' => 'StrongPass1!',
    ]);

    $response->assertInvalid([
        'email' => 'already been taken',
    ]);
    $this->assertDatabaseCount('users', 1);
    Notification::assertNothingSent();
});

it('returns 422 when the password confirmation does not match', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/register', [
        'email' => 'rider@example.com',
        'password' => 'StrongPass1!',
        'password_confirmation' => 'DifferentPass2!',
    ]);

    $response->assertInvalid([
        'password' => 'confirmation does not match',
    ]);
    $this->assertDatabaseCount('users', 0);
    Notification::assertNothingSent();
});

it('returns 422 when required registration fields are missing', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/register', []);

    $response->assertInvalid([
        'email' => 'The email field is required.',
        'password' => 'The password field is required.',
    ]);
    $this->assertDatabaseCount('users', 0);
    Notification::assertNothingSent();
});

it('returns 422 when the email address is invalid', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/register', [
        'email' => 'not-an-email',
        'password' => 'StrongPass1!',
        'password_confirmation' => 'StrongPass1!',
    ]);

    $response->assertInvalid([
        'email' => 'valid email address',
    ]);
    $this->assertDatabaseCount('users', 0);
    Notification::assertNothingSent();
});

it('returns 422 when the password does not meet the complexity requirements', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/register', [
        'email' => 'rider@example.com',
        'password' => 'weakpass1!',
        'password_confirmation' => 'weakpass1!',
    ]);

    $response->assertInvalid([
        'password' => 'uppercase',
    ]);
    $this->assertDatabaseCount('users', 0);
    Notification::assertNothingSent();
});
