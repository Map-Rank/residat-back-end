<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use App\Mail\DailyPostsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendDailyPostsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Récupérer les articles créés la veille
        $posts  = Post::whereDate('published_at', Carbon::yesterday())->get();

        // Récupérer tous les utilisateurs
        $users = User::all();

        // Envoyer un email à chaque utilisateur
        foreach ($users as $user) {
            Mail::to($user->email)->send(new DailyPostsMail($posts));
        }
    }
}
