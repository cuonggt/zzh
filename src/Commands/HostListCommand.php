<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;
use Symfony\Component\Process\Process;

class HostListCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:list')
            ->setDescription('List the hosts');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $hosts = Host::all();

        if ($hosts->isEmpty()) {
            Helpers::comment('The hosts list is empty. Please use command <info>zzh host:add <host></info> to add a new host.');
        }

        $this->table([
            'Name',
            'Host',
            'User',
            'Port',
            'Identity File',
        ], $hosts->map(function ($host) {
            return [
                $host->name,
                $host->host,
                $host->user,
                $host->port,
                $host->identityfile,
            ];
        })->toArray());
    }
}
