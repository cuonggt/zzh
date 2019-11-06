<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;

class HostConnectCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:connect')
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

        if (! Helpers::hostFileExists($name = $this->argument('host'))) {
            Helpers::abort('Unable to find a host with that name.');
        }

        $this->zzh->connectHost($name);
    }
}
