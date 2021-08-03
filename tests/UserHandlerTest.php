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
<<<<<<< HEAD
=======
    /**
     * @var UserHandler
     */
    private $userHandler;
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)

    protected function setUp(): void
    {
        parent::setUp();

<<<<<<< HEAD
        $this->setupUserHandlerArguments();
=======
        $this->setupUserHandler();
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
    }

    public function testStart()
    {
<<<<<<< HEAD
        // Create a UserHandler
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        // Create mock Repository
=======
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockClassName)
            ->willReturn($mockRepository);

<<<<<<< HEAD
        // Create mock Users
=======
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
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

<<<<<<< HEAD
        // Get cached system users set during start()
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual= $systemUsersItem->get();

        // Create expected result
=======
        $cache = new FilesystemAdapter();
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
        $expected = [];
        $expected[0] = 'someUsername1';
        $expected[1] = 'someUsername2';

<<<<<<< HEAD
        $this->assertEquals($expected, $actual);
=======
        $this->assertEquals($expected, $systemUsersArray);
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
    }

    public function testRetainUsers()
    {
<<<<<<< HEAD
        // Cache some usernames for retainUsers() to use
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
=======
        $cache = new FilesystemAdapter();
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)

        $testCachedUsers = [];
        array_push($testCachedUsers, 'someUsername1');
        array_push($testCachedUsers, 'someUsername2');

<<<<<<< HEAD
        $systemUsersItem->set($testCachedUsers);
        $cache->save($systemUsersItem);

        // Mock users to be removed from cached list
=======
        $systemUsers->set($testCachedUsers);
        $cache->save($systemUsers);

>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
        $users = [];
        array_push($users, [
            'userPrincipalName' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->retainUsers($users);

<<<<<<< HEAD
        // Get cached list of users after retainUsers()
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual = $systemUsersItem->get();

        // Create expected list
=======
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

<<<<<<< HEAD
        $this->assertEquals($expected, $actual);
=======
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        $this->assertEquals($expected, $systemUsersArray);
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
    }

    public function testCommit()
    {
        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->commit();
    }

<<<<<<< HEAD
    private function setupUserHandlerArguments()
=======
    private function setupUserHandler()
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
    {
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockClassName = 'mockClassName';
        $this->mockUserName = 'mockUsername';
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
