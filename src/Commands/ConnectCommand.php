<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;

class ConnectCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('connect')
            ->addArgument('host', InputArgument::REQUIRED, 'The host name')
            ->setDescription('Connect to a host');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (! Process::isTtySupported()) {
            Helpers::abort('You need a TTY supported terminal to connect to a host.');
        }

        $process = new Process(
            'ssh '.$this->argument('host')
        );

        $process->setTimeout(null)->setTty(true)->run();
    }
}
