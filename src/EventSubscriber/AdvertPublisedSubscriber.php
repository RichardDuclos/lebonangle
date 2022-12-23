<?php

namespace App\EventSubscriber;

use App\Entity\Advert;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class AdvertPublisedSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.advert_publish.transition' => 'onTransition'
        ];
    }

    public function onTransition(Event $event): void
    {
        if ($event->getTransition()->getName() !== 'publish') {
            return;
        }
        /**
         * @var Advert $advert
         */
        $advert = $event->getSubject();
        $advert->setPublishedAt(new DateTimeImmutable());
    }
}