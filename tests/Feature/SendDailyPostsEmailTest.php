<?php


namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Mail\DailyPostsMail;
use App\Jobs\SendDailyPostsEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\TestsJobs;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SendDailyPostsEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_is_dispatched()
    {
        // Mock yesterday's date
        Carbon::setTestNow(now()->subDay());

        // Fake the queue
        Queue::fake();

        // Dispatch the job
        SendDailyPostsEmail::dispatch();

        // Assert that the job was dispatched
        Queue::assertPushed(SendDailyPostsEmail::class);
    }

    public function test_email_is_sent_to_correct_recipients()
    {
        // Créer des utilisateurs de test
        User::factory()->create(['email' => 'user1@gmail.com']);
        User::factory()->create(['email' => 'user2@gmail.com']);
        User::factory()->create(['email' => 'user3@gmail.com']);

        $users = User::get();

        $posts = Post::factory(10)->create(['published_at' => now()->subDay()]);

        // Mock email sending
        Mail::fake();

        // Dispatch the job
        SendDailyPostsEmail::dispatch();

        foreach ($users as $user) {
            Mail::assertSent(DailyPostsMail::class, function ($mail) use ($user) {
                // Vérifie que l'e-mail a été envoyé au bon utilisateur
                $correctRecipient = $mail->hasTo($user->email);
    
                // Vérifie que le contenu de l'e-mail correspond au markdown attendu
                $correctContent = $mail->content()->markdown === 'emails.daily_posts';
    
                // Vérifie qu'il n'y a pas de pièces jointes (comme défini dans la méthode attachments)
                $noAttachments = count($mail->attachments()) === 0;
    
                return $correctRecipient && $correctContent && $noAttachments;
            });
        }
    }

    // public function test_job_logs_error_when_mail_fails()
    // {
    //     // Create test users and posts
    //     User::factory()->create(['email' => 'user1@gmail.com']);
    //     User::factory()->create(['email' => 'user2@gmail.com']);
    //     User::factory()->create(['email' => 'user3@gmail.com']);
    //     Post::factory(5)->create(['published_at' => now()->subDay()]);

    //     // Spy on the Log facade
    //     Log::spy();

    //     // Mock email sending to throw an exception
    //     Mail::fake();
    //     Mail::shouldReceive('send')
    //         ->andThrow(new \Exception('Mocked exception message'));

    //     // Dispatch the job
    //     SendDailyPostsEmail::dispatch();

    //     $this->withoutExceptionHandling();

    //     // Assert that an error was logged
    //     Log::assertLogged('error', function ($message, $context) {
    //         return strpos($message, 'Erreur lors de l\'envoi du DailyPostsMail : Mocked exception message') !== false;
    //     });

    //     Log::shouldReceive('error')
    //     ->once()
    //     ->with(\Mockery::on(function ($arg) {
    //         return stripos($arg, 'Erreur lors de l\'envoi du DailyPostsMail : Mocked exception message') !== false;
    //     }));
    // }
}
