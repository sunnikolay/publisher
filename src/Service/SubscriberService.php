<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\EntityManagerInterface;

class SubscriberService
{
    public function __construct(private readonly SubscriberRepository $repo, private readonly EntityManagerInterface $em)
    {
    }

    public function subscribe(SubscriberRequest $request): void
    {
        if ($this->repo->existsByEmail($request->getEmail())) {
            throw new SubscriberAlreadyExistsException();
        }

        $subscriber = new Subscriber();
        $subscriber->setEmail($request->getEmail());

        $this->em->persist($subscriber);
        $this->em->flush();
    }
}
