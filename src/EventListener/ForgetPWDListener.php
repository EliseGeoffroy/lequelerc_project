<?php

namespace App\EventListener;

use App\Entity\UserForgetPassword;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class ForgetPWDListener
{

    function __construct(public MailerInterface $mailer) {}

    function onPostPersistUserForgetPassword(UserForgetPassword $user)
    {
        $email = new TemplatedEmail();
        $email->to(new Address($user->getEmail()))
            ->htmlTemplate('@email_templates/forgetPWDmail.html.twig')
            ->context(['user' => $user]);

        $this->mailer->send($email);
    }
}
