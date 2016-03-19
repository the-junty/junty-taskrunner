<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Runner;

use Junty\TaskRunner\Task\{
    GroupInterface,
    TaskInterface,
    TasksCollection,
    GroupsCollection
};

interface RunnerInterface
{
    /**
     * Registers a group of tasks
     *
     * @param string   $group
     * @param callable $tasks
     */
    public function group($group, callable $tasks = null);

    /**
     * Registres a task
     *
     * @param string|TaskInterface $task
     * @param callable|null        $callback
     */
    public function task($task, callable $callback = null);

    /**
     * Returns all registred tasks
     *
     * @return TasksCollection
     */
    public function getTasks() : TasksCollection;

    /**
     * Returns all registred groups
     *
     * @return GroupsCollection
     */
    public function getGroups() : GroupsCollection;
    
    /**
     * Runs all tasks
     */
    public function run();

    /**
     * Runs one single task
     *
     * @param string|TaskInterface $task
     */
    public function runTask($task);
}