<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SignUpListener
{

    function __construct(public MailerInterface $mailer) {}

    function onPostPersistUser(User $user)
    {
        $email = new TemplatedEmail();
        $email->to(new Address($user->getEmail(), $user->getUsername()))
            ->htmlTemplate('@email_templates/welcomeEmail.html.twig');

        $this->mailer->send($email);
    }
}
