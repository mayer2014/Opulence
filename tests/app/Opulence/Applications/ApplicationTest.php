<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Applications;

use InvalidArgumentException;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Tasks\Dispatchers\IDispatcher;
use Opulence\Applications\Tasks\TaskTypes;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use ReflectionClass;

/**
 * Tests the application class
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application The application to use in the tests */
    private $application = null;
    /** @var IDispatcher|\PHPUnit_Framework_MockObject_MockObject The task dispatcher */
    private $dispatcher = null;
    /** @var Environment The environment used by the application */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dispatcher = $this->getMock(IDispatcher::class);
        $this->environment = new Environment("testing");
        $this->application = new Application(
            new Paths(["foo" => "bar"]),
            $this->dispatcher,
            $this->environment,
            new Container()
        );
    }

    /**
     * Tests that our application only attempts to shutdown once when it's already shutdown
     */
    public function testApplicationIsNotShutdownTwice()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutdown();
        $this->application->shutdown();
    }

    /**
     * Tests that our application only attempts to start up once when it's already running
     */
    public function testApplicationIsNotStartedTwice()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->application->start();
        $this->application->start();
    }

    /**
     * Tests registering a bad post-shutdown task
     */
    public function testBadPostShutdownTask()
    {
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN)
            ->will($this->throwException(new InvalidArgumentException("foo")));
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad post-start task
     */
    public function testBadPostStartTask()
    {
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->will($this->throwException(new InvalidArgumentException("foo")));
        $this->assertNull($this->application->start());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad pre-shutdown task
     */
    public function testBadPreShutdownTask()
    {
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN)
            ->will($this->throwException(new InvalidArgumentException("foo")));
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown());
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad shutdown task
     */
    public function testBadShutdownTask()
    {
        $this->assertNull($this->application->start());
        $this->assertNull($this->application->shutdown(function () {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException("foobar");
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests registering a bad start task
     */
    public function testBadStartTask()
    {
        $this->assertNull($this->application->start(function () {
            // Throw anything other than a runtime exception
            throw new InvalidArgumentException("foobar");
        }));
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a shutdown application is no longer running
     */
    public function testCheckingIfAShutdownApplicationIsNotRunning()
    {
        $this->application->start();
        $this->application->shutdown();
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests checking if a started application is running
     */
    public function testCheckingIfAStartedApplicationIsRunning()
    {
        $this->application->start();
        $this->assertTrue($this->application->isRunning());
    }

    /**
     * Tests checking if an application that wasn't ever started is running
     */
    public function testCheckingIfUnstartedApplicationIsRunning()
    {
        $this->assertFalse($this->application->isRunning());
    }

    /**
     * Tests getting the environment
     */
    public function testGettingEnvironment()
    {
        $expectedEnvironment = new Environment("testing");
        $this->assertEquals($expectedEnvironment, $this->application->getEnvironment());
    }

    /**
     * Tests getting the dependency injection container
     */
    public function testGettingIocContainer()
    {
        $this->assertInstanceOf(IContainer::class, $this->application->getIocContainer());
    }

    /**
     * Tests getting paths
     */
    public function testGettingPaths()
    {
        $paths = new Paths(["foo" => "bar"]);
        $this->assertEquals($paths, $this->application->getPaths());
    }

    /**
     * Tests getting the application version
     */
    public function testGettingVersion()
    {
        $reflectionClass = new ReflectionClass($this->application);
        $property = $reflectionClass->getProperty("version");
        $property->setAccessible(true);
        $this->assertEquals($property->getValue(), Application::getVersion());
    }

    /**
     * Tests registering post-shutdown tasks
     */
    public function testRegisteringPostShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutdown();
    }

    /**
     * Tests registering post-start tasks
     */
    public function testRegisteringPostStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering pre- and post-shutdown tasks
     */
    public function testRegisteringPreAndPostShutdownTasks()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutdown();
    }

    /**
     * Tests registering pre- and post-start tasks
     */
    public function testRegisteringPreAndPostStartTasks()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering pre-shutdown tasks
     */
    public function testRegisteringPreShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $this->application->shutdown();
    }

    /**
     * Tests registering pre-start tasks
     */
    public function testRegisteringPreStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->application->start();
    }

    /**
     * Tests registering a shutdown task
     */
    public function testRegisteringShutdownTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $this->dispatcher->expects($this->at(2))
            ->method("dispatch")
            ->with(TaskTypes::PRE_SHUTDOWN);
        $this->dispatcher->expects($this->at(3))
            ->method("dispatch")
            ->with(TaskTypes::POST_SHUTDOWN);
        $this->application->start();
        $shutdownValue = null;
        $this->assertNull($this->application->shutdown(function () use (&$shutdownValue) {
            $shutdownValue = "baz";
        }));
        $this->assertEquals("baz", $shutdownValue);
    }

    /**
     * Tests registering a start task
     */
    public function testRegisteringStartTask()
    {
        $this->dispatcher->expects($this->at(0))
            ->method("dispatch")
            ->with(TaskTypes::PRE_START);
        $this->dispatcher->expects($this->at(1))
            ->method("dispatch")
            ->with(TaskTypes::POST_START);
        $startValue = "";
        $this->assertNull($this->application->start(function () use (&$startValue) {
            $startValue = "baz";
        }));
        $this->assertEquals("baz", $startValue);
    }

    /**
     * Tests setting the environment
     */
    public function testSettingEnvironment()
    {
        $environment = new Environment("foo");
        $this->application->setEnvironment($environment);
        $this->assertEquals($environment, $this->application->getEnvironment());
    }

    /**
     * Tests setting paths
     */
    public function testSettingPaths()
    {
        $paths = new Paths(["baz" => "blah"]);
        $this->application->setPaths($paths);
        $this->assertSame($paths, $this->application->getPaths());
    }

    /**
     * Tests a shutdown task that returns something
     */
    public function testShutdownTaskThatReturnsSomething()
    {
        $this->application->start();
        $this->assertEquals("foo", $this->application->shutdown(function () {
            return "foo";
        }));
    }

    /**
     * Tests a start task that returns something
     */
    public function testStartTaskThatReturnsSomething()
    {
        $this->assertEquals("foo", $this->application->start(function () {
            return "foo";
        }));
    }
} 