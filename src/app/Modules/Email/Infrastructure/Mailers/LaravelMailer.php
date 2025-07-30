<?php

namespace App\Modules\Email\Infrastructure\Mailers;

use App\Modules\Email\Application\Mailers\MailerInterface;
use App\Modules\Observability\Presentation\Interface\ObservabilityModuleInterface;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

readonly class LaravelMailer implements MailerInterface
{
    public function __construct(
        private ObservabilityModuleInterface $monitor
    ) {
    }
    /**
     * @param string $email
     * @param Mailable $mailable
     * @return bool
     */
    public function send(string $email, Mailable $mailable): bool
    {
        try {
            Mail::to($email)->send($mailable);
            return true;
        } catch (\Throwable $e) {
            $this->monitor->logger()->logError(
                "Failed to send email to {$email}: " . $e->getMessage(),
                [
                    'module' => 'Email',
                    'email' => $email
                ]
            );
            return false;
        }
    }
}
