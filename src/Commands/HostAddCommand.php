<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;
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
        $host = new Host($this->argument('host'));

        if ($host->exists()) {
            Helpers::abort('Host '.$host->name.' already exists!');
        }

        $host->map(
            $this->getHostEntries($host->name)
        )->saveToConfigFile();

        Helpers::info('Host added successfully.');
    }

    protected function getHostEntries($name)
    {
        $entries = [
            'host' => Helpers::ask('Host', $name),
            'user' => Helpers::ask('User', Helpers::defaultSSHUser()),
            'port' => Helpers::ask('Port', Host::DEFAULT_SSH_PORT),
            'identityfile' => Helpers::ask('Identity File', Host::DEFAULT_IDENTITY_FILE),
        ];

        if (! $this->option('advanced')) {
            return $entries;
        }

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
                'ServerAliveInterval' => Helpers::ask('ServerAliveInterval', Host::DEFAULT_SERVER_ALIVE_INTERVAL),
                'ServerAliveCountMax' => Helpers::ask('ServerAliveCountMax', Host::DEFAULT_SERVER_ALIVE_COUNT_MAX),
            ]);
        }

        return array_merge($entries, $advancedEntries);
    }
}
