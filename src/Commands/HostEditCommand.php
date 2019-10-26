<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;
use Symfony\Component\Console\Input\InputArgument;

class HostEditCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:edit')
            ->addArgument('host', InputArgument::REQUIRED, 'The host name')
            ->setDescription('Edit a host');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $host = Host::loadFromConfigFile($this->argument('host'));

        if (! $host) {
            Helpers::abort('Unable to find a host with that name.');
        }

        $host->map([
            'host' => Helpers::ask('Host', $host->host),
            'user' => Helpers::ask('User', $host->user),
            'port' => Helpers::ask('Port', $host->port),
            'identityfile' => Helpers::ask('Identity File', $host->identityfile),
        ]);

        if (! Helpers::confirm('Are you sure you want to update this host', false)) {
            Helpers::abort('Action cancelled.');
        }

        $host->saveToConfigFile();

        Helpers::info('Host updated successfully.');
    }
}
