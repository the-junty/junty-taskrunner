<?php
namespace Test\Junty\TaskRunner\Task;

abstract class AbstractCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::add
     *
     * @expectedException \BadMethodCallException
     */
    public function testTryingToAddValueThrowsAnException()
    {
        $collection = $this->getCollectionInstance();
        $collection->add('task_name');
    }

    abstract protected function getCollectionInstance();
}