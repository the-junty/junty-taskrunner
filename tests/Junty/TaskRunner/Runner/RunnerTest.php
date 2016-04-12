<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Test\Junty\TaskRunner\Runner;

use Junty\TaskRunner\Runner\Runner;
use Junty\TaskRunner\Task\{TaskInterface, AbstractTask, GroupInterface};

/**
 * @coversDefaultClass \Junty\TaskRunner\Runner\Runner
 */
class RunnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::task
     * @covers ::run
     */
    public function testCreatingAndExecutingATask()
    {
        $runner = new Runner();

        $runner->task('task_1', function () {
            $_SERVER['FOO'] = 'bar';
        });

        $runner->run();

        $this->assertEquals($_SERVER['FOO'], 'bar');
    }

    /**
     * @covers ::task
     * @covers ::run
     * @covers ::order
     */
    public function testOrderingTasks()
    {
        $runner = new Runner();

        $runner->task('task_1', function () {
            $_SERVER['BARZ'] = 'bar';
        });

        $runner->task('task_2', function () {
            $_SERVER['BARZ'] = 'show';
        });

        $runner->task('task_3', function () {
            $_SERVER['BARZ'] = 'kbz';
        });

        $runner->order('task_3', 'task_1', 'task_2');

        $runner->run();

        $this->assertEquals($_SERVER['BARZ'], 'show');
    }

    /**
     * @covers ::task
     * @covers ::order
     * @covers ::run
     */
    public function testCreatingTaskByInstanceOfATask()
    {
        $runner = new Runner();

        $runner->task(new class() extends AbstractTask {
            public function getName() : string
            {
                return 'task_1';
            }

            public function getCallback() : callable
            {
                return function () {
                    $_SERVER['CHUBBY'] = 'bunny';
                };
            }
        });

        $runner->order('task_1');

        $runner->run();

        $this->assertEquals($_SERVER['CHUBBY'], 'bunny');
    }

    /**
     * @covers ::task
     * @covers ::getTasks
     */
    public function testIfGetterForTasksReturnsAllTasks()
    {
        $runner = new Runner();

        $task2CallbackReturn = 'hiiii';

        $runner->task('task_1', function () {});
        $runner->task('task_2', function () use ($task2CallbackReturn) {
            return $task2CallbackReturn;
        });

        $tasks = $runner->getTasks();

        $this->assertCount(2, $tasks);

        $this->assertArrayHasKey('task_1', $tasks);
        $this->assertArrayHasKey('task_2', $tasks);
        
        foreach ($tasks as $task) {
            $this->assertTrue($task instanceof TaskInterface);
        }

        $cb2 = $tasks['task_2']->getCallback();

        $this->assertEquals($cb2(), $task2CallbackReturn);
    }

    /**
     * @covers ::group
     * @covers GroupInterface::task
     * @covers ::runGroup
     */
    public function testCreatingAndExecutingAGroup()
    {
        $runner = new Runner();

        $runner->group('group_1', function () {
            $this->task('task_1_for_g1', function () {
                $_SERVER['task_1_executed'] = true;
            });

            $this->task('task_2_for_g1', function () {
                $_SERVER['task_2_executed'] = true;
            });
        });

        $runner->runGroup('group_1');

        $this->assertTrue($_SERVER['task_1_executed']);
        $this->assertTrue($_SERVER['task_2_executed']);
    }

    /**
     * @covers ::group
     * @covers ::task
     * @covers GroupInterface::task
     * @covers ::run
     */
    public function testExecutingGroupsAndTasks()
    {
        $runner = new Runner();

        $runner->group('group_1', function () {
            $this->task('task_1_for_g1', function () {
                $_SERVER['foo'] = 'bar';
            });

            $this->task('task_2_for_g1', function () {
                $_SERVER['tr'] = 'win';
            });
        });

        $runner->task('task_1', function () {
            $_SERVER['tr'] = 'loose';
        });

        $runner->group('group_2', function () {
            $this->task('task_1_for_g2', function () {
                $_SERVER['task_1_executed_2'] = true;
            });

            $this->task('task_2_for_g2', function () {
                $_SERVER['task_2_executed_2'] = true;
            });
        });

        $runner->task('task_2', function () {
            $_SERVER['single_task_1_executed'] = true;
        });

        $runner->run();

        $this->assertTrue($_SERVER['task_1_executed_2']);
        $this->assertTrue($_SERVER['task_2_executed_2']);
        $this->assertEquals($_SERVER['foo'], 'bar');
        $this->assertEquals($_SERVER['tr'], 'loose');
    }

    /**
     * @covers ::group
     * @covers GroupInterface::task
     * @covers ::getGroups
     */
    public function testIfGetterForGroupsReturnsAllGroups()
    {
        $runner = new Runner();

        $runner->group('group_1', function () {
            $this->task('task_1_1', function () {
                $_SERVER['FUDEU'] = 'sim';
            });
        });

        $runner->group('group_2', function () {
            $this->task('task_1_1', function () {
                $_SERVER['TRANQUILO'] = 'favoravel';
            });
        });

        $groups = $runner->getGroups();

        foreach ($groups as $group) {
            $this->assertTrue($group instanceof GroupInterface);

            foreach ($group->getTasks() as $task) {
                $cb = $task->getCallback();
                $cb();
            }
        }

        $this->assertArrayHasKey('group_1', $groups);
        $this->assertArrayHasKey('group_2', $groups);
        $this->assertEquals($_SERVER['FUDEU'], 'sim');
        $this->assertEquals($_SERVER['TRANQUILO'], 'favoravel');
    }

    /**
     * @covers ::__set
     * @covers ::getTasks
     */
    public function testCreatingTaskSettingProperty()
    {
        $runner = new Runner();
        $runner->my_task = function () {};
        $tasks = $runner->getTasks();

        $this->assertArrayHasKey('my_task', $tasks);
    }
}