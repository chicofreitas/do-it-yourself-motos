<?php

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('returns 200 and verifies the authenticated user with a valid email hash', function () {
    $user = User::factory()->unverified()->create();
    Event::fake();
    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/email/verify/{$user->id}/".sha1($user->email));

    $response->assertOk()
        ->assertExactJson(['message' => 'Email verified successfully']);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Event::assertDispatched(Verified::class, ['user' => $user]);
});

it('returns 401 when the request is unauthenticated', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->getJson("/api/v1/email/verify/{$user->id}/".sha1($user->email));

    $response->assertUnauthorized();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('returns 403 when the email hash does not match', function () {
    $user = User::factory()->unverified()->create();
    Sanctum::actingAs($user);

    $response = $this->getJson("/api/v1/email/verify/{$user->id}/invalid-hash");

    $response->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('returns 403 when the verification link belongs to another user', function () {
    $user = User::factory()->unverified()->create();
    $authenticatedUser = User::factory()->create();
    Sanctum::actingAs($authenticatedUser);

    $response = $this->getJson("/api/v1/email/verify/{$user->id}/".sha1($user->email));

    $response->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('returns 200 and resends a verification notification to the authenticated user', function () {
    $user = User::factory()->unverified()->create();
    Sanctum::actingAs($user);
    Notification::fake();

    $response = $this->postJson('/api/v1/email/verification-notification');

    $response->assertOk()
        ->assertExactJson(['message' => 'Verification email resent.']);

    Notification::assertSentTo($user, VerifyEmail::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('returns 401 and does not send a verification notification when unauthenticated', function () {
    Notification::fake();

    $response = $this->postJson('/api/v1/email/verification-notification');

    $response->assertUnauthorized();

    Notification::assertNothingSent();
});
