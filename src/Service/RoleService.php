<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(private readonly UserRepository $repo, private readonly EntityManagerInterface $em)
    {
    }

    public function grantAdmin(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_ADMIN');
    }

    public function grantAuthor(int $userId): void
    {
        $this->grantRole($userId, 'ROLE_AUTHOR');
    }

    private function grantRole(int $userId, string $role): void
    {
        $user = $this->repo->getUser($userId);
        $user->setRoles([$role]);

        $this->em->flush();
    }
}
