<?php
/**
 * Junty
 *
 * @author Gabriel Jacinto aka. GabrielJMJ <gamjj74@hotmail.com>
 * @license MIT License
 */
 
namespace Junty\TaskRunner\Console\Command;

use Junty\TaskRunner\Runner\RunnerInterface;
use Junty\TaskRunner\Task\{GroupInterface, Task};
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
                'group_or_task',
                InputArgument::OPTIONAL
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('group_or_task') && $task = $input->getArgument('group_or_task') !== null) {
            $el = $input->getArgument('group_or_task');

            if ($this->runner->getGroups()->containsKey($el)) {
                $group = $this->runner->getGroups()->get($el);
                $output->writeln('Executing group \'' . $group->getName() . '\'');

                $this->executeGroup($group, $output);
            } elseif ($this->runner->getTasks()->containsKey($el)) {
                $task = $this->runner->getTasks()->get($el);
                $output->writeln('Executing task \'' . $task->getName() . '\'');

                $this->runner->runTask($task);
            }
        } else {
            $els = $this->runner->getOrder();

            $output->writeln('Executing tasks');
            
            foreach ($els as $el) {
                $data = $this->getFromOrderData($el);

                $output->writeln('Executing ' . $data['type'] . ' \'' . $data['name'] . '\'');

                try {
                    if ($data['type'] == 'group') {
                        $group = $this->runner->getGroups()->toArray()[$data['name']];
                        
                        $this->executeGroup($group, $output);
                    } else {
                        $output->writeln('Executing task \'' . $data['name'] . '\'');
                        $this->runner->runTask($data['name']);
                    }
                } catch (\Exception $e) {
                    $output->writeln('Error on ' . $data['type'] . ' \'' . $data['name'] . '\': ' . $e->getMessage());
                }
            }
        }

        $time = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 10000);
        $output->writeln('Finished! Time: ' . $time . 'ms');
    }

    private function executeGroup(GroupInterface $group, OutputInterface $output)
    {
        foreach ($group->getTasks() as $task) {
            $output->writeln('--Executing task \'' . $task->getName() . '\'');

            try {
                $this->runner->runTask($task);
            } catch (\Exception $e) {
                $output->writeln('--Error on task \'' . $task->getName() . '\': ' . $e->getMessage());
            }
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