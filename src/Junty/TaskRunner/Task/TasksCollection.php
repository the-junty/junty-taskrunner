<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\{Task, TaskInterface};
use Doctrine\Common\Collections\ArrayCollection;

class TasksCollection extends ArrayCollection
{
    /**
     * Registres a task
     *
     * @param string|TaskInterface $key
     * @param callable             $value
     */
    public function set($key, $value)
    {
        if ($value !== null && !is_callable($value)) {
            throw new \InvalidArgumentException('Callback must be callable.');
        }

        if (!is_string($key) && !$key instanceof TaskInterface) {
            throw new \InvalidArgumentException('Pass an instance of TaskInterface or a callback');
        }

        if ($key instanceof TaskInterface) {
            parent::set($key->getName(), $key);
            return;
        }

        parent::set($key, new Task($key, $value));
    }

    /**
     * Invalid method for task registration
     *
     * @param mixed $element
     *
     * @throws \BadMethodCallException
     */
    public function add($element)
    {
        throw new \BadMethodCallException('Use TaskCollection::set to register a task.');
    }
}