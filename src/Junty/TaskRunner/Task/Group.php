<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\{GroupInterface, TasksCollection};

class Group implements GroupInterface
{
    private $name;

    private $tasks = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->tasks = new TasksCollection();
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function task($name, callable $callback = null)
    {
        $this->tasks->set($name, $callback);
    }

    public function getTasks() : TasksCollection
    {
        return $this->tasks;
    }
}