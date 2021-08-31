<?php

namespace ItkDev\AzureAdDeltaSyncBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use ItkDev\AzureAdDeltaSyncBundle\Event\DeleteUserEvent;
use ItkDev\AzureAdDeltaSyncBundle\Exception\AzureUserException;
use ItkDev\AzureAdDeltaSyncBundle\Handler\UserHandler;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserHandlerTest extends TestCase
{
    private $mockCache;
    private $mockDispatcher;
    private $mockEntityManager;
    private $mockSystemUserClass;
    private $mockSystemUserProperty;
    private $mockAzureUserProperty;
    private $mockDeletionList;


    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpUserHandlerArguments();
    }

    public function testCollectUsersForDeletionList()
    {
        // Create a UserHandler
        $handler = new UserHandler($this->mockCache, $this->mockDispatcher, $this->mockEntityManager, $this->mockSystemUserClass, $this->mockSystemUserProperty, $this->mockAzureUserProperty);

        // Create mock Repository
        $mockRepository = $this->createMock(ObjectRepository::class);

        $this->mockEntityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->mockSystemUserClass)
            ->willReturn($mockRepository);

        // Create mock Users
        $mockUsers = [];

        $mockUsers[0] = (object) [
            $this->mockSystemUserProperty => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ];
        $mockUsers[1] = (object) [
            $this->mockSystemUserProperty => 'someUsername2',
            'someOtherProperty' => 'someOtherProperty2'
        ];

        $mockRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($mockUsers);

        // Create mock properties
        $mockProperties = [];
        $mockProperties[0] = 'someUsername1';
        $mockProperties[1] = 'someUsername2';

        $mockDeletionList = $this->createMock(CacheItemInterface::class);

        $this->mockCache
            ->expects($this->once())
            ->method('getItem')
            ->with('azure_ad_delta_sync.deletion_list')
            ->willReturn($mockDeletionList);

        $mockDeletionList
            ->expects($this->once())
            ->method('set')
            ->with($mockProperties);

        $this->mockCache
            ->expects($this->once())
            ->method('save')
            ->with($mockDeletionList);


        $handler->collectUsersForDeletionList();
    }

    public function testRemoveUsersFromDeletionList()
    {
        // Mock cache some usernames for removeUsersFromDeletionList() to use
        $this->setUpMockCache();

        // Mock users to be removed from cached list
        $mockUsers = [];
        array_push($mockUsers, [
            'mockAzureUserProperty' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        // Create expected list
        $expected = [];
        // Notice the key being 1, as we dont re-index the array
        $expected[1] = 'someUsername2';

        $this->mockDeletionList
            ->expects($this->once())
            ->method('set')
            ->with($expected);

        $this->mockCache
            ->expects($this->once())
            ->method('save')
            ->with($this->mockDeletionList);

        $handler = new UserHandler($this->mockCache, $this->mockDispatcher, $this->mockEntityManager, $this->mockSystemUserClass, $this->mockSystemUserProperty, $this->mockAzureUserProperty);

        $handler->removeUsersFromDeletionList($mockUsers);
    }

    public function testUserClaimExceptionThrown()
    {
        $this->expectException(AzureUserException::class);

        // Mock cache some usernames for removeUsersFromDeletionList() to use
        $this->setUpMockCache();

        // Mock users to be removed from cached list
        $users = [];

        array_push($users, [
            'mockAzureUserPropertyWrong' => 'someUsername1',
            'someOtherProperty' => 'someOtherProperty1',
        ]);

        $handler = new UserHandler($this->mockCache, $this->mockDispatcher, $this->mockEntityManager, $this->mockSystemUserClass, $this->mockSystemUserProperty, $this->mockAzureUserProperty);

        $handler->removeUsersFromDeletionList($users);
    }

    public function testCommitDeletionList()
    {
        // Mock cache some usernames
        $this->setUpMockCache();

        $mockProperties = [];
        $mockProperties[0] = 'someUsername1';
        $mockProperties[1] = 'someUsername2';

        $mockDeleteEvent = $this->createMock(DeleteUserEvent::class);
        $mockDeleteEvent->setData($mockProperties);

        $this->mockDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($mockDeleteEvent);

        // Keep original commitDeletionList method but mock createDeleteUserEvent to allow for test
        $handler = $this->getMockBuilder(UserHandler::class)
            ->setMethodsExcept(['commitDeletionList'])
            ->setConstructorArgs([
                $this->mockCache,
                $this->mockDispatcher,
                $this->mockEntityManager,
                $this->mockSystemUserClass,
                $this->mockSystemUserProperty,
                $this->mockAzureUserProperty,
            ])
            ->getMock();

        $handler->method('createDeleteUserEvent')->willReturn($mockDeleteEvent);

        $handler->commitDeletionList();
    }

    public function testCreateDeleteUserEvent() {
        $handler = new UserHandler($this->mockCache, $this->mockDispatcher, $this->mockEntityManager, $this->mockSystemUserClass, $this->mockSystemUserProperty, $this->mockAzureUserProperty);

        $mockProperties = [];
        $mockProperties[0] = 'someUsername1';
        $mockProperties[1] = 'someUsername2';

        $event = $handler->createDeleteUserEvent($mockProperties);

        $this->assertEquals(DeleteUserEvent::class, get_class($event));
        $this->assertEquals($mockProperties, $event->getData());

    }

    private function setUpUserHandlerArguments()
    {
        $this->mockCache = $this->createMock(AdapterInterface::class);
        $this->mockDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockSystemUserClass = 'mockSystemUserClass';
        $this->mockSystemUserProperty = 'mockSystemUserProperty';
        $this->mockAzureUserProperty = 'mockAzureUserProperty';
    }

    private function setUpMockCache() {
        $mockProperties = [];
        $mockProperties[0] = 'someUsername1';
        $mockProperties[1] = 'someUsername2';

        $this->mockDeletionList = $this->createMock(CacheItemInterface::class);

        $this->mockCache
            ->expects($this->once())
            ->method('getItem')
            ->with('azure_ad_delta_sync.deletion_list')
            ->willReturn($this->mockDeletionList);

        $this->mockDeletionList
            ->expects($this->once())
            ->method('get')
            ->willReturn($mockProperties);
    }
}
