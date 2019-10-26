<?php

namespace Cuonggt\Zzh\Models;

use Cuonggt\Zzh\Helpers;

class Host
{
    public $name;

    public $allowEntries = ['host', 'user', 'port', 'identityfile'];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function exists()
    {
        return file_exists(Helpers::hostFilePath($this->name));
    }

    /**
     * Map the given array onto the host's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->allowEntries)) {
                $this->{$key} = $value;
            }
        }

        return $this;
    }

    public static function loadFromConfigFile($name)
    {
        $host = new static($name);

        if (! $host->exists()) {
            return false;
        }

        $config = parse_ini_file(Helpers::hostFilePath($host->name));

        return $host->map([
            'host' => $config['host'] ?? $host->name,
            'user' => $config['user'] ?? Helpers::defaultSSHUser(),
            'port' => $config['port'] ?? Helpers::defaultSSHPort(),
            'identityfile' => $config['identityfile'] ?? Helpers::defaultIdentityFile(),
        ]);
    }

    public function saveToConfigFile()
    {
        Helpers::ensureHostsDirectoryExists();

        $fp = fopen(Helpers::hostFilePath($this->name), 'w');

        foreach ($this->allowEntries as $entry) {
            fwrite($fp, $entry.'="'.$this->{$entry}.'"'.PHP_EOL);
        }

        fclose($fp);
    }

    public function delete()
    {
        @unlink(Helpers::hostFilePath($this->name));
    }

    public function connectCommand()
    {
        $command = 'ssh';

        if ($this->identityfile) {
            $command .= ' -i '.$this->identityfile;
        }

        if ($this->port) {
            $command .= ' -p '.$this->port;
        }

        return $command.' '.$this->user.'@'.$this->host;
    }

    public static function all()
    {
        return collect(glob(Helpers::hostsPath().'/*'))->map(function ($filename) {
            return static::loadFromConfigFile(basename($filename));
        });
    }
}
