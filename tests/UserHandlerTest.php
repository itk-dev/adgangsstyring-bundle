<?php

namespace ItkDev\AdgangsstyringBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use ItkDev\AdgangsstyringBundle\Exception\UserClaimException;
use ItkDev\AdgangsstyringBundle\Handler\UserHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserHandlerTest extends TestCase
{
    private $mockDispatcher;
    private $mockEntityManager;
    private $mockUserClassName;
    private $mockUserProperty;
    private $mockUserClaimProperty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupUserHandlerArguments();
    }

    public function testCollectUsersForDeletionList()
    {
        // Create a UserHandler
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockUserClassName, $this->mockUserProperty, $this->mockUserClaimProperty);

        // Create mock Repository
        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockUserClassName)
            ->willReturn($mockRepository);

        // Create mock Users
        $mockUsers = [];

        $mockUsers[0] = (object) [
            $this->mockUserProperty => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ];
        $mockUsers[1] = (object) [
            $this->mockUserProperty => 'someUsername2',
            'someOtherProperty' => 'someOtherProperty2'
        ];

        $mockRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($mockUsers);

        $handler->collectUsersForDeletionList();

        // Get cached system users set during collectUsersForDeletionList()
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual= $systemUsersItem->get();

        // Create expected result
        $expected = [];
        $expected[0] = 'someUsername1';
        $expected[1] = 'someUsername2';

        $this->assertEquals($expected, $actual);
    }

    public function testRemoveUsersFromDeletionList()
    {
        // Cache some usernames for removeUsersFromDeletionList() to use
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');

        $testCachedUsers = [];
        array_push($testCachedUsers, 'someUsername1');
        array_push($testCachedUsers, 'someUsername2');

        $systemUsersItem->set($testCachedUsers);
        $cache->save($systemUsersItem);

        // Mock users to be removed from cached list
        $users = [];
        array_push($users, [
            'mockUserClaimProperty' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockUserClassName, $this->mockUserProperty, $this->mockUserClaimProperty);

        $handler->removeUsersFromDeletionList($users);

        // Get cached list of users after removeUsersFromDeletionList()
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual = $systemUsersItem->get();

        // Create expected list
        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

        $this->assertEquals($expected, $actual);
    }

    public function testCommitDeletionList()
    {
        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockUserClassName, $this->mockUserProperty, $this->mockUserClaimProperty);

        $handler->commitDeletionList();
    }

    public function testUserClaimExceptionThrown()
    {
        $this->expectException(UserClaimException::class);

        // Mock users to be removed from cached list
        $users = [];
        array_push($users, [
            'mockUserClaimPropertyWrong' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockUserClassName, $this->mockUserProperty, $this->mockUserClaimProperty);

        $handler->removeUsersFromDeletionList($users);
    }

    private function setupUserHandlerArguments()
    {
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockUserClassName = 'mockUserClassName';
        $this->mockUserProperty = 'mockUserProperty';
        $this->mockUserClaimProperty = 'mockUserClaimProperty';
    }
}
