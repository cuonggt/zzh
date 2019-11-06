<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('advanced', null, InputOption::VALUE_NONE, 'Add a new host with advanced SSH entries')
            ->setDescription('Add a new host');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if (Helpers::hostFileExists($name = $this->argument('host'))) {
            Helpers::abort('Host '.$name.' already exists!');
        }

        $this->zzh->createHost(
            $name,
            Helpers::ask('Host', $name),
            Helpers::ask('User', Helpers::DEFAULT_SSH_USER),
            Helpers::ask('Port', Helpers::DEFAULT_SSH_PORT),
            Helpers::ask('Identity File', Helpers::DEFAULT_IDENTITY_FILE),
            $this->option('advanced') ? $this->getHostAdvancedEntries() : []
        );

        Helpers::info('Host added successfully.');
    }

    protected function getHostAdvancedEntries()
    {
        $advancedEntries = [
            'ProxyCommand' => Helpers::ask('ProxyCommand (leave blank if ignore)'),
            'LocalForward' => Helpers::ask('LocalForward (leave blank if ignore)'),
        ];

        // Specifies the protocol versions ssh(1) should support in order of preference.
        // The possible values are 1 and 2. Default value is 1.
        $protocol = $this->menu('Protocol', [
            1 => '1',
            2 => '2',
        ], 1);

        // These options apply to protocol version 2 only.
        if ($protocol == 2) {
            $advancedEntries = array_merge($advancedEntries, [
                'Protocol' => $protocol,
                'ServerAliveInterval' => Helpers::ask('ServerAliveInterval', Helpers::DEFAULT_SERVER_ALIVE_INTERVAL),
                'ServerAliveCountMax' => Helpers::ask('ServerAliveCountMax', Helpers::DEFAULT_SERVER_ALIVE_COUNT_MAX),
            ]);
        }

        return $advancedEntries;
    }
}
