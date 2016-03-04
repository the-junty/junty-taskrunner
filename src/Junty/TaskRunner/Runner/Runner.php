<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Runner;

use Junty\TaskRunner\Runner\RunnerInterface;
use Junty\TaskRunner\Task\{Task, TaskInterface};

class Runner implements RunnerInterface
{
    private $tasks = [];

    private $order;

    /**
     * Registres a task
     *
     * @param string|TaskInterface $task
     * @param callable             $callback
     *
     * @return TaskInterface
     */
    public function task($task, callable $callback = null) : TaskInterface
    {
        if (!is_string($task) && !$task instanceof TaskInterface) {
            throw new \InvalidArgumentException('Pass an instance of TaskInterface or a callback');
        }

        if ($task instanceof TaskInterface) {
            $this->tasks[$task->getName()] = $task;
            return $this->tasks[$task->getName()];
        }

        $this->tasks[$task] = new Task($task, $callback);
        return $this->tasks[$task];
    }

    /**
     * Organize tasks order
     * If this method is executed and a task is not in this list, it won't be executed
     *
     * @param string-variadic $tasks
     */
    public function order(string ...$tasks)
    {
        foreach ($tasks as $task) {
            if (!$this->tasks[$task]) {
                throw new \Exception('\'' . $task . '\' is not a registred task.');
            }
        }

        $this->order = $tasks;
    }

    /**
     * Returns all registred tasks
     *
     * @return array
     */
    public function getTasks() : array
    {
        return $this->tasks;
    }

    /**
     * Runs all tasks
     */
    public function run()
    {
        $tasks = $this->order ?? $this->tasks;

        foreach ($tasks as $task) {
            $this->runTask($task);
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

        if ($task->hasNext()) {
            $this->runTask($this->tasks[$task->getNext()]);
        }
    }
}