<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;
use Symfony\Component\Console\Input\InputArgument;

class HostAddCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:add')
            ->addArgument('host', InputArgument::REQUIRED, 'The host name')
            ->setDescription('Add a new host');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $host = new Host($this->argument('host'));

        if ($host->exists()) {
            Helpers::abort('Host '.$host->name.' already exists!');
        }

        $host->map([
            'host' => Helpers::ask('Host', $host->name),
            'user' => Helpers::ask('User', Helpers::defaultSSHUser()),
            'port' => Helpers::ask('Port', Helpers::defaultSSHPort()),
            'identityfile' => Helpers::ask('Identity File', Helpers::defaultIdentityFile()),
        ])->saveToConfigFile();

        Helpers::info('Host added successfully.');
    }
}
