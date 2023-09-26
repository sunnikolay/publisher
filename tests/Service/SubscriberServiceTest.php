<?php

namespace App\Tests\Service;

use App\Entity\Subscriber;
use App\Exception\SubscriberAlreadyExistsException;
use App\Model\SubscriberRequest;
use App\Repository\SubscriberRepository;
use App\Service\SubscriberService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SubscriberServiceTest extends TestCase
{
    private readonly SubscriberRepository $repo;
    private readonly EntityManagerInterface $em;

    private const EMAIL = 'test@mail.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->createMock(SubscriberRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    public function testSubscribeEmailAlreadyExists()
    {
        $this->expectException(SubscriberAlreadyExistsException::class);

        $this->repo->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(true);

        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        (new SubscriberService($this->repo, $this->em))->subscribe($request);
    }

    public function testSubscribe()
    {
        $this->repo->expects($this->once())
            ->method('existsByEmail')
            ->with(self::EMAIL)
            ->willReturn(false);

        $expectedSubscriber = new Subscriber();
        $expectedSubscriber->setEmail(self::EMAIL);

        $this->em->expects($this->once())
            ->method('persist')
            ->with($expectedSubscriber);

        $this->em->expects($this->once())
            ->method('flush');

        $request = new SubscriberRequest();
        $request->setEmail(self::EMAIL);

        (new SubscriberService($this->repo, $this->em))->subscribe($request);
    }
}
