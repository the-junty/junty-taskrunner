<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Task;

interface TaskInterface
{
    public function getName() : string;

    public function getCallback() : callable;

    public function __invoke(array $params = []);
}