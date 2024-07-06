<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    // public $url;
    // public $otp;
    public $token;

    /**
     * Create a new notification instance.
     *
     * @param string $url
     * @param $otp
     */
    public function __construct(string $token)
    {
        // $this->url = $url;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        // $url = $this->url;
        // // $otp = $this->otp;

        // return (new MailMessage)
        //     ->view('emails.resetotp', compact('url'))
        //     ->subject("Réinitialisation de mot de passe");

        $url = env('FRONT_URL') . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject(Lang::get('Reset Password Notification'))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Reset Password'), $url)
            ->line(Lang::get('If you did not request a password reset, no further action is required.'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
