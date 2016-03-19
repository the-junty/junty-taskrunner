<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Console\Command;

use Junty\TaskRunner\Runner\RunnerInterface;
use Junty\TaskRunner\Task\Task;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    private $runner;

    public function __construct(RunnerInterface $runner, $name = null)
    {
        parent::__construct($name);

        $this->runner = $runner;
    }

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Run tasks')
            ->addArgument(
                'task',
                InputArgument::OPTIONAL
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('task') && $task = $input->getArgument('task') !== null) {
            $output->writeln('Executing task: ' . $task = $input->getArgument('task'));

            $this->runner->runTask($task);
        } else {
            $els = $this->runner->getOrder();

            $output->writeln('Executing tasks');
            
            foreach ($els as $el) {
                $data = $this->getFromOrderData($el);

                $output->writeln('Executing ' . $data['type'] . ' \'' . $data['name'] . '\'');

                try {
                    if ($data['type'] == 'group') {
                        $group = $this->runner->getGroups()->toArray()[$data['name']];
                        $tasks = $group->getTasks();

                        foreach ($tasks as $task) {
                            $output->writeln('--Executing task \'' . $task->getName() . '\'');

                            try {
                                $this->runner->runTask($task);
                            } catch (\Exception $e) {
                                $output->writeln('--Error on task \'' . $task->getName() . '\': ' . $e->getMessage());
                            }
                        }
                    } else {
                        $output->writeln('Executing task \'' . $data['name'] . '\'');
                        $task->runTask($data['name']);
                    }
                } catch (\Exception $e) {
                    $output->writeln('Error on ' . $data['type'] . ' \'' . $data['name'] . '\': ' . $e->getMessage());
                }
            }
        }

        $time = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 100);
        $output->writeln('Finished! Time: ' . $time . 'ms');
    }

    private function getFromOrderData($name)
    {
        $parts = explode('::', $name);
        $type = $parts[0];
        unset($parts[0]);

        return ['type' => $type, 'name' => implode('::', $parts)];
    }
}