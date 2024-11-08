<?php

namespace App\Jobs;

use App\Models\User;
use App\Service\UtilService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    /**
     * Create a new job instance.
     */
    public function __construct($post)
    {
        $this->post = $post;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users_token = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

        $users = User::whereNotNull('fcm_token')->get();

        foreach ($users as $user) {
            $customMessage = "Salut {$user->first_name}, regarde ce post sur residat publié le {$this->post->published_at}. Zone: {$this->post->zone->name}.";

            try {
                // UtilService::sendWebNotification($post->published_at, $customMessage, $user->fcm_token);
                $notificationService = app(UtilService::class);
                $notificationService->sendNewNotification($this->post->published_at, $customMessage, [$user->fcm_token]);
            } catch (Exception $ex) {
                Log::warning(sprintf('%s: The error is : %s', __METHOD__, $ex->getMessage()));
            }
        }
    }
}
