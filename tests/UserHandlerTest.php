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
<<<<<<< HEAD
=======
    /**
     * @var UserHandler
     */
    private $userHandler;
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
>>>>>>> 9410433 (Applied coding standards)

    protected function setUp(): void
    {
        parent::setUp();

<<<<<<< HEAD
<<<<<<< HEAD
        $this->setupUserHandlerArguments();
=======
        $this->setupUserHandler();
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        $this->setupUserHandlerArguments();
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
    }

    public function testStart()
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        // Create a UserHandler
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        // Create mock Repository
<<<<<<< HEAD
=======
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockClassName)
            ->willReturn($mockRepository);

<<<<<<< HEAD
<<<<<<< HEAD
        // Create mock Users
=======
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        // Create mock Users
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
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
<<<<<<< HEAD
        // Get cached system users set during start()
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual= $systemUsersItem->get();

        // Create expected result
=======
=======
        // Get cached system users set during start()
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual= $systemUsersItem->get();

<<<<<<< HEAD
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        // Create expected result
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        $expected = [];
        $expected[0] = 'someUsername1';
        $expected[1] = 'someUsername2';

<<<<<<< HEAD
<<<<<<< HEAD
        $this->assertEquals($expected, $actual);
=======
        $this->assertEquals($expected, $systemUsersArray);
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        $this->assertEquals($expected, $actual);
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
    }

    public function testRetainUsers()
    {
<<<<<<< HEAD
<<<<<<< HEAD
        // Cache some usernames for retainUsers() to use
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
=======
        $cache = new FilesystemAdapter();
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        // Cache some usernames for retainUsers() to use
        $cache = new FilesystemAdapter();
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)

        $testCachedUsers = [];
        array_push($testCachedUsers, 'someUsername1');
        array_push($testCachedUsers, 'someUsername2');

<<<<<<< HEAD
<<<<<<< HEAD
        $systemUsersItem->set($testCachedUsers);
        $cache->save($systemUsersItem);

        // Mock users to be removed from cached list
=======
        $systemUsers->set($testCachedUsers);
        $cache->save($systemUsers);

>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        $systemUsersItem->set($testCachedUsers);
        $cache->save($systemUsersItem);

        // Mock users to be removed from cached list
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        $users = [];
        array_push($users, [
            'userPrincipalName' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->retainUsers($users);

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        // Get cached list of users after retainUsers()
        $systemUsersItem = $cache->getItem('adgangsstyring.system_users');
        $actual = $systemUsersItem->get();

        // Create expected list
<<<<<<< HEAD
=======
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

<<<<<<< HEAD
<<<<<<< HEAD
        $this->assertEquals($expected, $actual);
=======
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        $this->assertEquals($expected, $systemUsersArray);
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
        $this->assertEquals($expected, $actual);
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
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
<<<<<<< HEAD
    private function setupUserHandlerArguments()
=======
    private function setupUserHandler()
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
    private function setupUserHandlerArguments()
>>>>>>> 10985e5 (Cleaned up UserHandlerTest)
    {
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockClassName = 'mockClassName';
        $this->mockUserName = 'mockUsername';
    }
<<<<<<< HEAD
<<<<<<< HEAD
}
=======
}
>>>>>>> 6d45b85 (Added Unit tests for UserHandler)
=======
}
>>>>>>> 9410433 (Applied coding standards)
