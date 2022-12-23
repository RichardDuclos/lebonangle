<?php

namespace App\EventListener;

use App\Entity\AdminUser;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserChangedListener
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function prePersist(AdminUser $adminUser, LifecycleEventArgs $event): void
    {
        $this->setHashedPassword($event);
    }

    public function setHashedPassword(LifecycleEventArgs $event): void
    {
        $user = $event->getObject();
        if (!$user instanceof AdminUser) {
            return;
        }

        if (!empty($user->getPlainPassword())) {

            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
            );
            $user->eraseCredentials();
        }
    }

    public function preUpdate(AdminUser $adminUser, LifecycleEventArgs $event): void
    {
        $this->setHashedPassword($event);
    }


}