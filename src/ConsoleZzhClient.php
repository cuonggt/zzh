<?php

namespace Cuonggt\Zzh;

use Cuonggt\Zzh\Models\Host;
use Symfony\Component\Process\Process;

class ConsoleZzhClient
{
    /**
     * Create a new host.
     *
     * @param  string  $name
     * @param  string  $host
     * @param  string  $user
     * @param  string  $port
     * @param  string  $identityfile
     * @param  array  $advancedEntries
     * @return \Cuonggt\Zzh\Models\Host
     */
    public function createHost(
        $name,
        $host,
        $user,
        $port,
        $identityfile,
        $advancedEntries = []
    ) {
        return (new Host(
            $name, $host, $user, $port, $identityfile, $advancedEntries
        ))->saveToConfigFile();
    }

    /**
     * Get the hosts.
     *
     * @return \Illuminate\Support\Collection
     */
    public function hosts()
    {
        return collect(glob(Helpers::hostsPath().'/*'))->map(function ($filename) {
            return Host::loadFromConfigFile(basename($filename));
        });
    }

    /**
     * Connect to a host.
     *
     * @param  string  $name
     * @return void
     */
    public function connectHost($name)
    {
        (new Process(
            (Host::loadFromConfigFile($name))->connectCommand()
        ))->setTimeout(null)->setTty(true)->run();
    }

    /**
     * Delete a host.
     *
     * @param  string  $name
     * @return void
     */
    public function deleteHost($name)
    {
        @unlink(Helpers::hostFilePath($name));
    }

    /**
     * Update a host.
     *
     * @param  \Cuonggt\Zzh\Host  $host
     * @param  array  $entries
     * @return \Cuonggt\Zzh\Host
     */
    public function updateHost(Host $host, array $entries)
    {
        return $host->map($entries)->saveToConfigFile();
    }
}
