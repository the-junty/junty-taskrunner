<?php
namespace Test\Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\{Task, TaskInterface, TasksCollection};

/**
 * @coversDefaultClass \Junty\TaskRunner\Task\TasksCollection
 */
class TasksCollectionTest extends AbstractCollectionTest
{
    /**
     * @covers ::set
     * @covers ::get
     */
    public function testRegisteringACallableTaskWillTransformToTaskInstance()
    {
        $collection = new TasksCollection();
        $collection->set('task_name', function () {});

        $this->assertTrue($collection->get('task_name') instanceof TaskInterface);
    }

    /**
     * @covers ::set
     * @covers ::get
     */
    public function testRegisteringTaskInstanceWillReturnThePassedOne()
    {
        $collection = new TasksCollection();
        $collection->set('task_name', new Task('task_name', function () {}));

        $this->assertTrue($collection->get('task_name') instanceof TaskInterface);
        $this->assertEquals('task_name', $collection->get('task_name')->getName());
    }

    protected function getCollectionInstance()
    {
        return new TasksCollection();
    }
}