<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\UserAlreadyExistsException;
use App\Model\SignUpRequest;
use App\Repository\UserRepository;
use App\Service\SignUpService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class SignUpServiceTest extends TestCase
{
    private UserPasswordHasher $hasher;
    private UserRepository $repository;
    private EntityManagerInterface $em;
    private AuthenticationSuccessHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hasher = $this->createMock(UserPasswordHasher::class);
        $this->repository = $this->createMock(UserRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->handler = $this->createMock(AuthenticationSuccessHandler::class);
    }

    protected function createService(): SignUpService
    {
        return new SignUpService($this->hasher, $this->repository, $this->em, $this->handler);
    }

    public function testSignUpUserAlreadyExists(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with('test@test.com')
            ->willReturn(true);

        $this->createService()->signUp((new SignUpRequest())->setEmail('test@test.com'));
    }

    public function testSignUp(): void
    {
        $response = new Response();
        $expectedHasherUser = (new User())
            ->setRoles(['ROLE_USER'])
            ->setFirstName('Vasya')
            ->setLastName('Testov')
            ->setEmail('test@test.com');

        $expectedUser = clone $expectedHasherUser;
        $expectedUser->setPassword('hashed_password');

        $this->repository->expects($this->once())
            ->method('existsByEmail')
            ->with($expectedUser->getEmail())
            ->willReturn(false);

        $this->hasher->expects($this->once())
            ->method('hashPassword')
            ->with($expectedHasherUser, 'testtest')
            ->willReturn('hashed_password');

        $this->em->expects($this->once())->method('persist')->with($expectedUser);
        $this->em->expects($this->once())->method('flush');

        $this->handler->expects($this->once())
            ->method('handleAuthenticationSuccess')
            ->with($expectedUser)
            ->willReturn($response);

        $signUpRequest = (new SignUpRequest())
            ->setFirstName('Vasya')
            ->setLastName('Testov')
            ->setEmail('test@test.com')
            ->setPassword('testtest');

        $this->assertEquals($response, $this->createService()->signUp($signUpRequest));
    }
}
