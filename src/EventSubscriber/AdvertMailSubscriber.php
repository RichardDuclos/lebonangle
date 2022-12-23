<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Advert;
use App\Repository\AdminUserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Mailer\MailerInterface;

class AdvertMailSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer, private readonly AdminUserRepository $adminUserRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['sendMail', EventPriorities::POST_WRITE],
        ];
    }

    public function sendMail(ViewEvent $event): void
    {
        $advert = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$advert instanceof Advert || Request::METHOD_POST !== $method) {
            return;
        }
        $admins = $this->adminUserRepository->findAll();
        $adminEmails = [];
        foreach ($admins as $admin) {
            $adminEmails[] = $admin->getEmail();
        }
        $message = (new TemplatedEmail())
            ->from('noreply@lebonangle.com')
            ->subject('Une nouvelle annonce lebonangle a été posté')
            ->htmlTemplate('email/new_advert.html.twig')
            ->context([
                'advert' => $advert
            ]);
        foreach ($adminEmails as $adminEmail) {
            $message
                ->to($adminEmail);
            $this->mailer->send($message);
        }

    }
}