<?php

namespace Tests\Feature\Authentication;

use App\Auth\ResetPassword;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use DatabaseTransactions;

    public function testItShowsResetForm(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->get('/password/reset');

        $this->assertComponent('authentication/PasswordReset', $response);
    }

    public function testItRequiresAnEmailAddress(): void
    {
        $this->postJson('/password/email')->assertJsonValidationErrors(['email' => 'E-Mail Adresse ist erforderlich.']);
    }

    public function testItNeedsAnActiveUser(): void
    {
        $this->postJson('/password/email', [
            'email' => 'test@aa.de',
        ])->assertJsonValidationErrors(['email' => 'Es konnte leider kein Nutzer mit dieser E-Mail-Adresse gefunden werden.']);
    }

    public function testItSendsPasswordResetLink(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@aa.de']);
        $this->postJson('/password/email', [
            'email' => 'test@aa.de',
        ])->assertOk();

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
