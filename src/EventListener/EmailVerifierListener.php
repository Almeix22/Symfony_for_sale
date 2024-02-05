<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserConfirmationEmailNotReceived;
use App\Event\UserRegistered;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EmailVerifierListener implements EventSubscriberInterface
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegistered::class => 'onUserRegistered',
            UserConfirmationEmailNotReceived::class => 'onUserConfirmationEmailNotReceived',
        ];
    }

    public function onUserRegistered(UserRegistered $event)
    {
        // Send email for email verification
        $this->sendEmail($event->getUser());
    }

    public function onUserConfirmationEmailNotReceived(UserConfirmationEmailNotReceived $event)
    {
        // Send email to remind the user for email verification
        $this->sendEmail($event->getUser());
    }

    private function sendEmail(User $user)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
