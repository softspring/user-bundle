<?php

namespace Softspring\UserBundle\Tests\Unit\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Softspring\UserBundle\Doctrine\Filter\AdminFilter;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class AdminFilterTest extends TestCase
{
    public function testAddsAdminConstraintForAdminAwareEntities(): void
    {
        $filter = (new ReflectionClass(AdminFilter::class))->newInstanceWithoutConstructor();
        $metadata = $this->createMetadata(User::class);

        $constraint = $filter->addFilterConstraint($metadata, 'user_alias');

        self::assertSame('user_alias.is_admin = 1', $constraint);
    }

    public function testReturnsEmptyConstraintForUnsupportedEntities(): void
    {
        $filter = (new ReflectionClass(AdminFilter::class))->newInstanceWithoutConstructor();
        $metadata = $this->createMetadata(stdClass::class);

        $constraint = $filter->addFilterConstraint($metadata, 'user_alias');

        self::assertSame('', $constraint);
    }

    private function createMetadata(string $class): ClassMetadata
    {
        return new class($class) extends ClassMetadata {
            private ReflectionClass $reflectionClass;

            public function __construct(string $class)
            {
                parent::__construct($class);
                $this->reflectionClass = new ReflectionClass($class);
            }

            public function getReflectionClass(): ReflectionClass
            {
                return $this->reflectionClass;
            }
        };
    }
}
