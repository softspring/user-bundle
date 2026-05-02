<?php

namespace Softspring\UserBundle\Tests\Unit\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;
use Softspring\UserBundle\Doctrine\Filter\UserFilter;
use Softspring\UserBundle\Tests\TestApplication\Entity\User;

class UserFilterTest extends TestCase
{
    public function testAddsRegularUserConstraintForAdminAwareEntities(): void
    {
        $filter = (new ReflectionClass(UserFilter::class))->newInstanceWithoutConstructor();
        $metadata = $this->createMetadata(User::class);

        $constraint = $filter->addFilterConstraint($metadata, 'user_alias');

        self::assertSame('user_alias.is_admin = false', $constraint);
    }

    public function testReturnsEmptyConstraintForUnsupportedEntities(): void
    {
        $filter = (new ReflectionClass(UserFilter::class))->newInstanceWithoutConstructor();
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
