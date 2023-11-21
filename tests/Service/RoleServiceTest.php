<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\RoleService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RoleServiceTest extends TestCase
{
    private UserRepository $repo;
    private EntityManagerInterface $em;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->repo = $this->createMock(UserRepository::class);
        $this->repo->expects($this->once())
            ->method('getUser')
            ->with(1)
            ->willReturn($this->user);

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->expects($this->once())
            ->method('flush');
    }

    private function createService(): RoleService
    {
        return new RoleService($this->repo, $this->em);
    }

    public function testGrantAuthor()
    {
        $this->createService()->grantAuthor(1);
        $this->assertEquals(['ROLE_AUTHOR'], $this->user->getRoles());
    }

    public function testGrantAdmin()
    {
        $this->createService()->grantAdmin(1);
        $this->assertEquals(['ROLE_ADMIN'], $this->user->getRoles());
    }
}
