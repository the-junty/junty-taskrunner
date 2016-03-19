<?php
namespace Test\Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\{Task, TaskInterface, GroupInterface, GroupsCollection};
use Test\Junty\TaskRunner\Task\AbstractCollectionTest;

/**
 * @coversDefaultClass \Junty\TaskRunner\Task\GroupsCollection
 */
class GroupsCollectionTest extends AbstractCollectionTest
{
    /**
     * @covers ::set
     * @covers ::get
     */
    public function testRegisteringCallableGroupItWillBeTransformedToGroupInstance()
    {
        $collection = $this->getCollectionInstance();
        $collection->set('group_1', function() {});

        $this->assertTrue($collection->get('group_1') instanceof GroupInterface);
        $this->assertEquals('group_1', $collection->get('group_1')->getName());
    }

    /**
     * @covers ::set
     * @covers ::get
     */
    public function testRegisteringCallableGroupTasksWillBeRegistred()
    {
        $cb = function () {
            return 'return_of_task_1';
        };
        $collection = $this->getCollectionInstance();
        $collection->set('group_1', function() use ($cb) {
            $this->task('task_1', $cb);
        });
        $tasks = $collection->get('group_1')->getTasks();

        $this->assertArrayHasKey('task_1', $tasks);
        $this->assertEquals($cb, $tasks['task_1']->getCallback());
    }

    protected function getCollectionInstance()
    {
        return new GroupsCollection();
    }
}