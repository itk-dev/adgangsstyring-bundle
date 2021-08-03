<?php

namespace ItkDev\AdgangsstyringBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use ItkDev\AdgangsstyringBundle\Handler\UserHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserHandlerTest extends TestCase
{
    private $mockDispatcher;
    private $mockEntityManager;
    private $mockClassName;
    private $mockUserName;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupUserHandlerArguments();
    }

    public function testStart()
    {
        // Create a UserHandler
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        // Create mock Repository
        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockClassName)
            ->willReturn($mockRepository);

        // Create mock Users
        $mockUsers = [];

        $mockUsers[0] = (object) [
            $this->mockUserName => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ];
        $mockUsers[1] = (object) [
            $this->mockUserName => 'someUsername2',
            'someOtherProperty' => 'someOtherProperty2'
        ];

        $mockRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($mockUsers);

        $handler->start();

        // Get cached system users set during start()
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual= $systemUsersItem->get();

        // Create expected result
        $expected = [];
        $expected[0] = 'someUsername1';
        $expected[1] = 'someUsername2';

        $this->assertEquals($expected, $actual);
    }

    public function testRetainUsers()
    {
        // Cache some usernames for retainUsers() to use
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
            'userPrincipalName' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->retainUsers($users);

        // Get cached list of users after retainUsers()
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual = $systemUsersItem->get();

        // Create expected list
        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

        $this->assertEquals($expected, $actual);
    }

    public function testCommit()
    {
        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->commit();
    }

    private function setupUserHandlerArguments()
    {
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockClassName = 'mockClassName';
        $this->mockUserName = 'mockUsername';
    }
}
