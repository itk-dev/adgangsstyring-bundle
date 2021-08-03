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

        $this->setupUserHandler();
    }

    public function testStart()
    {
        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockClassName)
            ->willReturn($mockRepository);

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

        $cache = new FilesystemAdapter();
        $systemUsers = $cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        $expected = [];
        $expected[0] = 'someUsername1';
        $expected[1] = 'someUsername2';

        $this->assertEquals($expected, $systemUsersArray);
    }

    public function testRetainUsers()
    {
        $cache = new FilesystemAdapter();
        $systemUsers = $cache->getItem('adgangsstyring.system_users');

        $testCachedUsers = [];
        array_push($testCachedUsers, 'someUsername1');
        array_push($testCachedUsers, 'someUsername2');

        $systemUsers->set($testCachedUsers);
        $cache->save($systemUsers);

        $users = [];
        array_push($users, [
            'userPrincipalName' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->retainUsers($users);

        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

        $systemUsers = $cache->getItem('adgangsstyring.system_users');
        $systemUsersArray = $systemUsers->get();

        $this->assertEquals($expected, $systemUsersArray);
    }

    public function testCommit()
    {
        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatch');

        $handler = new UserHandler($this->mockDispatcher, $this->mockEntityManager, $this->mockClassName, $this->mockUserName);

        $handler->commit();
    }

    private function setupUserHandler()
    {
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockClassName = 'mockClassName';
        $this->mockUserName = 'mockUsername';
    }
}
