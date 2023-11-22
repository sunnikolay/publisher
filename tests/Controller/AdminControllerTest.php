<?php

namespace App\Tests\Controller;

use App\Controller\AdminController;
use App\Tests\AbstractControllerTest;
use PHPUnit\Framework\TestCase;

class AdminControllerTest extends AbstractControllerTest
{
    public function testGrantAuthor(): void
    {
        $user = $this->createUser('user@mail.com', '12312312');

        $adminUsername = 'admin@mail.com';
        $adminPassword = '12312312';
        $this->createAdmin($adminUsername, $adminPassword);
        $this->auth($adminUsername, $adminPassword);

        $this->client->request(
            'PUT',
            '/api/v1/admin/grantAuthor/'.$user->getId()
        );

        $this->assertResponseIsSuccessful();
    }
}
