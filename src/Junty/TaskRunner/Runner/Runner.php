<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Runner;

use Junty\TaskRunner\Runner\RunnerInterface;
use Junty\TaskRunner\Task\{
    Task,
    TaskInterface,
    Group,
    GroupInterface,
    TasksCollection,
    GroupsCollection
};

class Runner implements RunnerInterface
{
    private $tasks;

    private $groups = [];

    private $order;

    public function __construct()
    {
        $this->tasks = new TasksCollection();
        $this->groups = new GroupsCollection();
    }

    public function group($group, callable $tasks = null)
    {
        $_name = $group instanceof GroupInterface ? $group->getName() : $group;

        if ($this->tasks->containsKey($_name)) {
            throw new \Exception('Is not possible to register a group and a task with the same name.');
        }

        $this->groups->set($group, $tasks);
        $this->order[] = 'group::' . $_name;
    }

    /**
     * Registres a task
     *
     * @param string|TaskInterface $task
     * @param callable             $callback
     */
    public function task($task, callable $callback = null)
    {
        $_name = $task instanceof TaskInterface ? $task->getName() : $task;

        if ($this->groups->containsKey($_name)) {
            throw new \Exception('Is not possible to register a group and a task with the same name.');
        }

        $this->tasks->set($task, $callback);
        $this->order[] = 'task::' . $_name;
    }

    /**
     * Allows create a task setting a property
     *
     * @param string|TaskInterface $task
     * @param callable             $callback
     */
    public function __set(string $task, callable $callback)
    {
        $this->task($task, $callback);
    }

    /**
     * Organize tasks and groups order
     * If this method is executed and a task and a group is not in this list, it won't be executed
     *
     * @param string-variadic $tasks
     */
    public function order(string ...$names)
    {
        foreach ($names as $key => $name) {
            if ($this->tasks->containsKey($name)) {
                $names[$key] = 'task::' . $name;
            } elseif ($this->groups->containsKey($name)) {
                $names[$key] = 'group::' . $name;
            } else {
                throw new \Exception('\'' . $name . '\' is not registred as task or group.');
            }
        }

        $this->order = $names;
    }

    /**
     * Returns all registred tasks
     *
     * @return TasksCollection
     */
    public function getTasks() : TasksCollection
    {
        return $this->tasks;
    }

    public function getGroups() : GroupsCollection
    {
        return $this->groups;
    }

    public function getOrder() : array
    {
        return $this->order;
    }

    /**
     * Runs all tasks
     */
    public function run()
    {
        $all = $this->order;

        foreach ($all as $el) {
            $data = $this->getFromOrderData($el);

            switch ($data['type']) {
                case 'group':
                    $this->runGroup($data['name']);
                    break;
                case 'task':
                    $this->runTask($data['name']);
                    break;
            }
        }
    }

    /**
     * Runs one single task
     *
     * @param string|TaskInterface $task
     */
    public function runTask($task)
    {
        if (!is_string($task) && !$task instanceof TaskInterface) {
            throw new \Exception('Invalid task type: ' + gettype($task));
        }

        if (is_string($task)) {
            if (!isset($this->tasks[$task])) {
                throw new \Exception('\'' . $task . '\' is not a registred task.');
            }
        }

        $task = $task instanceof TaskInterface ? $task : $this->tasks[$task];
        $cb = $task->getCallback();
        $cb();
    }

    /**
     * Runs a group of tasks
     *
     * @param string|GroupInterface $group
     */
    public function runGroup($group)
    {
        $group = $group instanceof GroupInterface ? $group : $this->groups[$group];
        $tasks = $group->getTasks();

        foreach ($tasks as $task) {
            $this->runTask($task);
        }
    }

    private function getFromOrderData($name)
    {
        $parts = explode('::', $name);
        $type = $parts[0];
        unset($parts[0]);

        return ['type' => $type, 'name' => implode('::', $parts)];
    }
}