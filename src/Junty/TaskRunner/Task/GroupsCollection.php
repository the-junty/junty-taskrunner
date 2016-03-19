<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Task;

use Junty\TaskRunner\Task\{Group, GroupInterface};
use Doctrine\Common\Collections\ArrayCollection;

class GroupsCollection extends ArrayCollection
{
    /**
     * Registres a group
     *
     * @param string|TaskInterface $key
     * @param callable             $value
     */
    public function set($key, $value)
    {
        if ($value !== null && !is_callable($value)) {
            throw new \InvalidArgumentException('Callback must be callable.');
        }

        if (!is_string($key) && !$key instanceof GroupInterface) {
            throw new \InvalidArgumentException('Pass an instance of TaskInterface or a callback');
        }

        if ($key instanceof GroupInterface) {
            parent::set($key->getName(), $key);
            return;
        }

        // Pass the group instance in the callback to register tasks
        $group = new Group($key);
        $cb = \Closure::bind($value, $group);
        $cb(); // Updates group instance

        parent::set($key, $group);
    }

    /**
     * Invalid method for group registration
     *
     * @param mixed $element
     *
     * @throws \BadMethodCallException
     */
    public function add($element)
    {
        throw new \BadMethodCallException('Use GroupsCollection::set to register a group');
    }
}