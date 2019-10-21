<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;
use Symfony\Component\Console\Input\InputArgument;

class HostDeleteCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:delete')
            ->addArgument('host', InputArgument::REQUIRED, 'The host name')
            ->setDescription('Delete a host');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $host = new Host($this->argument('host'));

        if (! $host->exists()) {
            Helpers::abort('Unable to find a host with that name.');
        }

        if (! Helpers::confirm('Are you sure you want to delete this host', false)) {
            Helpers::abort('Action cancelled.');
        }

        $host->delete();

        Helpers::info('Host deleted successfully.');
    }
}
