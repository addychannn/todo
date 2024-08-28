<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminPasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newPassword = "";

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newPassword)
    {
        //
        $this->newPassword = $newPassword;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->subject('Password Changed')
                ->greeting('Hi '.$notifiable->username.',')
                ->line('Your password has been changed by one of our admin.')
                ->line('')
                ->line('You can login with your new password.')
                ->line(new HtmlString('<br><b>New Password: </b>'.$this->newPassword));
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
