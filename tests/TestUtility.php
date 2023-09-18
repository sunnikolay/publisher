<?php

namespace App\Tests;

trait TestUtility
{
    protected function setField(object $object, int $val, $field = 'id'): void
    {
        $clazz = new \ReflectionClass($object);
        $prop = $clazz->getProperty($field);
        $prop->setAccessible(true);
        $prop->setValue($object, $val);
        $prop->setAccessible(false);
    }
}
