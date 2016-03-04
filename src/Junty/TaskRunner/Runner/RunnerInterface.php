<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Runner;

use Junty\TaslRunner\Task\TaskInterface;

interface RunnerInterface
{
    /**
     * Returns all registred tasks
     *
     * @return array
     */
    public function getTasks() : array;
    
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