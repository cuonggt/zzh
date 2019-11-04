<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Cuonggt\Zzh\Models\Host;

class HostExportCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('host:export')
            ->setDescription('Export hosts to SSH config file');
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
            Helpers::abort('The hosts list is empty. Please use command <info>zzh host:add <host></info> to add a new host.');
        }

        Helpers::comment('The current SSH config file will be backup and overwritten.');

        if (! Helpers::confirm('Are you sure you want to export hosts to SSH config file?', false)) {
            Helpers::abort('Action cancelled.');
        }

        Helpers::step('Backing up the current SSH config file...');

        $sshConfigFile = Helpers::home().'/.ssh/config';

        if (file_exists($sshConfigFile)) {
            @rename($sshConfigFile, $sshConfigFile.'.bak');
        }

        Helpers::step('Exporting hosts to SSH config file...');

        $fp = fopen($sshConfigFile, 'w');

        $hosts->map(function ($host) use ($fp) {
            fwrite($fp, PHP_EOL);
            fwrite($fp, '# '.$host->name.PHP_EOL);
            fwrite($fp, 'Host '.$host->name.PHP_EOL);
            fwrite($fp, '  HostName '.$host->host.PHP_EOL);
            fwrite($fp, '  User '.$host->user.PHP_EOL);
            fwrite($fp, '  Port '.$host->port.PHP_EOL);
            fwrite($fp, '  IdentityFile '.$host->identityfile.PHP_EOL);

            foreach ($host->advancedEntries as $advancedEntry) {
                if (! empty($host->{$advancedEntry})) {
                    fwrite($fp, '  '.$advancedEntry.' '.$host->{$advancedEntry}.PHP_EOL);
                }
            }
        });

        fclose($fp);

        Helpers::info('Hosts exported to SSH config file successfully.');
    }
}
