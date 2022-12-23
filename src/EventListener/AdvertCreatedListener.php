<?php

namespace App\EventListener;

use App\Entity\Advert;
use Doctrine\ORM\Event\LifecycleEventArgs;

class AdvertCreatedListener
{
    public function postPersist(Advert $advert, LifecycleEventArgs $event)
    {

    }

    public function prePersist(Advert $advert, LifecycleEventArgs $event)
    {

    }
}