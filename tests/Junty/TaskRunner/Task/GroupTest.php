<?php
namespace Test\Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\Group;
use Junty\TaskRunner\Task\TaskInterface;

/**
 * @coversDefaultClass \Junty\TaskRunner\Task\Group
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getName
     */
    public function testGetterForName()
    {
        $name = 'group_1';
        $group = new Group($name);

        $this->assertEquals($group->getName(), $name);
    }

    /**
     * @covers ::getTasks
     */
    public function testAddingAndGettingTask()
    {
        $group = $this->getGenericGroup();
        $tasks = $group->getTasks();

        $this->assertArrayHasKey('task_1', $tasks);
        $this->assertArrayHasKey('task_2', $tasks);
    }

    /**
     * @covers ::getTasks
     */
    public function testReturnedTasksAreInstanceOfTaskInterface()
    {
        $group = $this->getGenericGroup();
        $tasks = $group->getTasks();

        foreach ($tasks as $task) {
            $this->assertTrue($task instanceof TaskInterface);
        }
    }

    /**
     * @covers ::getTasks
     */
    public function testReturnedTasksHaveCorrectCallback()
    {
        $group = $this->getGenericGroup();
        $tasks = $group->getTasks();

        foreach ($tasks as $key => $task) {
            $cb = $task->getCallback();
            $cb();
        }

        $this->assertEquals('<3', $_SERVER['git']);
        $this->assertEquals('also', $_SERVER['php']);
    }

    /**
     * @return Group
     */
    private function getGenericGroup()
    {
        $group = new Group('generic_group');
        $group->task('task_1', function () {
            $_SERVER['git'] = '<3';
        });
        $group->task('task_2', function () {
            $_SERVER['php'] = 'also';
        });

        return $group;
    }
}