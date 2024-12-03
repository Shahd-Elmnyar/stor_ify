<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Log;

class ResetPasswordVerificationNotification extends Notification
{
    use Queueable;
    public $message;
    public $subject;
    public $fromEmail;
    public $mailer;
    private $otp;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message = 'use the below code for resetting password';
        $this->subject = 'Password resetting';
        $this->fromEmail = 'test@storeify.com';
        $this->mailer = 'smtp';
        $this->otp = new Otp;
    }

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
        // Add debug logging
        Log::info('Attempting to generate OTP for email: ' . $notifiable->email);
        
        // Generate OTP
        $otp = $this->otp->generate($notifiable->email, 'numeric', 4, 60);
        
        // Log the OTP response
        Log::info('OTP generation response:', ['otp' => $otp]);
        
        // Check if OTP generation failed
        if (!$otp) {
            Log::error('OTP generation returned null for email: ' . $notifiable->email);
            throw new \Exception('OTP generation failed - null response');
        }
        
        if (!isset($otp->token)) {
            Log::error('OTP generation returned invalid format for email: ' . $notifiable->email, [
                'otp_object' => json_encode($otp)
            ]);
            throw new \Exception('OTP generation failed - invalid format');
        }
        
        return (new MailMessage)
                ->mailer('smtp')
                ->subject($this->subject)
                ->view('emails.reset_password', [
                    'otp' => $otp->token,
                    'username' => $notifiable->username
                ]);
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
