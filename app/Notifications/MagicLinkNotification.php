<?php

namespace App\Notifications;

use App\Services\MagicLinkService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MagicLinkNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $url) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your sign-in link for '.config('app.name'))
            ->greeting('Sign in to '.config('app.name'))
            ->line('Click the button below to sign in to your account.')
            ->action('Sign In', $this->url)
            ->line('This link expires in '.MagicLinkService::EXPIRES_IN_MINUTES.' minutes and can only be used once.')
            ->line('If you did not request this link, no further action is required.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
