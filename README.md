JuntyTaskRunner
================
[![Packagist](https://img.shields.io/packagist/v/junty/junty-taskrunner.svg?style=flat-square)](https://packagist.org/packages/junty/junty-taskrunner) [![Travis](https://img.shields.io/travis/the-junty/junty-taskrunner.svg?style=flat-square)](https://travis-ci.org/the-junty/junty-taskrunner) [![Scrutinizer](https://img.shields.io/scrutinizer/g/the-junty/junty-taskrunner.svg?style=flat-square)](https://scrutinizer-ci.com/g/the-junty/junty-taskrunner/?branch=master) [![GitHub license](https://img.shields.io/github/license/the-junty/junty-taskrunner.svg?style=flat-square)](https://github.com/the-junty/junty-taskrunner/blob/master/LICENSE.md)

Junty task runner component.

## Install
```console
$ composer require junty/junty-taskrunner
```

## Usage
### Basig usage
```php
<?php
require 'vendor/autoload.php';

use Junty\TaskRunner\Runner\Runner;

$runner = new Runner();

$runner->task('say_hello', function () {
    echo 'hello!'; 
});

$runner->my_task_2 = function () {
    // ...
};

$runner->group('tests', function () {
    $this->task('tests_for_users', function () {
        // ...
    });

    $this->task('tests_for_admins', function () {
        // ...
    });
});

$runner->run(); // Runs all registred tasks
```

### Methods
#### ```task```
Creates a task with ```Junty\TaskRunner\Task\TaskInterface``` or callable one.
```php
$runner->task('my_task', function () {});

// or

$runner->task(new MyTask());
```

### ```group```
Creates a group of tasks with ```Junty\TaskRunner\Task\GroupInterface``` or callable one.
```php
$runner->group('my_group', function () {
    $this->task('my_task_from_group_1', function () {});

    // Another tasks
});
```

| A group and a task cannot have the same name!

### ```order```
Orders the execution task and groups order.
```php
$runner->order('my_group', 'my_task', 'my_group_2');
```

### ```run```
Runs all tasks and groups.
```php
$runner->run();
```

### ```runTask```
Runs a single registred task or instance of ```TaskInterface```.
```php
$runner->runTask('my_registred_task');

// or

$runner->runTask(new MyTask());
```

### ```runGroup```
Runs a single registred group or instance of ```GroupInterface```.
```php
$runner->runGroup('my_registred_group');

// or

use Junty\TaskRunner\Task\Group;

$runner->runGroup(new class() extends Group
{
    public function __construct()
    {
    }

    public function getName() : string
    {
        return 'my_group';
    }

    public function task($task, callable $task = null)
    {
    }

    public function getTasks() : TaskCollection
    {
        $collection = new TaskCollection();

        $collection->set(new MyTask());
        $collection->set(new MyOtherTask());

        return $collection;
    }
});
```