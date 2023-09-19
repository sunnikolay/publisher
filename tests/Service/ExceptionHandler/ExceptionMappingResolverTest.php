<?php

namespace App\Tests\Service\ExceptionHandler;

use App\Service\ExceptionHandler\ExceptionMappingResolver;
use PHPUnit\Framework\TestCase;

class ExceptionMappingResolverTest extends TestCase
{
    public function testThrowsExceptionOnEmptyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ExceptionMappingResolver(['someClass' => ['hidden' => true]]);
    }

    public function testResolvesToNullWhenNotFound(): void
    {
        $resolver = new ExceptionMappingResolver([]);

        $this->assertNull($resolver->resolve(\InvalidArgumentException::class));
    }

    public function testResolvesClassItSelf(): void
    {
        $resolver = new ExceptionMappingResolver([\InvalidArgumentException::class => ['code' => 400]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(400, $mapping->getCode());
        $this->assertTrue($mapping->isHidden());
        $this->assertTrue(!$mapping->isLoggable());
    }

    public function testResolvesSubClass(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(500, $mapping->getCode());
    }

    public function testResolvesHidden(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'hidden' => false]]);
        $mapping = $resolver->resolve(\LogicException::class);

        $this->assertFalse($mapping->isHidden());
    }

    public function testResolvesLoggable(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'loggable' => false]]);
        $mapping = $resolver->resolve(\LogicException::class);

        $this->assertFalse($mapping->isLoggable());
    }
}
