<?php

namespace App\Service\ExceptionHandler;

class ExceptionMappingResolver
{
    /**
     * @var ExceptionMapping[]
     */
    private array $mappings = [];

    public function __construct(array $mappings)
    {
        foreach ($mappings as $className => $mapping) {
            if (empty($mapping['code'])) {
                throw new \InvalidArgumentException('[code] is mandatory for class'.$className);
            }

            $this->build(
                $className,
                $mapping['code'],
                $mapping['hidden'] ?? true,
                $mapping['loggable'] ?? false
            );
        }
    }

    private function build(string $className, int $code, bool $hidden, bool $loggable): void
    {
        $this->mappings[$className] = new ExceptionMapping($code, $hidden, $loggable);
    }

    public function resolve(string $throwableClass): ?ExceptionMapping
    {
        $foundMapping = null;

        foreach ($this->mappings as $className => $mapping) {
            if ($throwableClass === $className || is_subclass_of($throwableClass, $className)) {
                $foundMapping = $mapping;
                break;
            }
        }

        return $foundMapping;
    }
}
