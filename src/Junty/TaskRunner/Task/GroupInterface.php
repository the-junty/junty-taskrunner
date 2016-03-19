<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Task;

interface GroupInterface
{
    public function getName() : string;

    public function task($name, callable $callback = null);

    public function getTasks() : TasksCollection;
}