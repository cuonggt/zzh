<?php

namespace Cuonggt\Zzh\Models;

use Cuonggt\Zzh\Helpers;

class Host
{
    const DEFAULT_SSH_PORT = 22;

    const DEFAULT_IDENTITY_FILE = '~/.ssh/id_rsa.pub';

    const DEFAULT_SERVER_ALIVE_INTERVAL = 60;

    const DEFAULT_SERVER_ALIVE_COUNT_MAX = 10;

    /**
     * The host name.
     *
     * @var string
     */
    public $name;

    /**
     * The host's allow entries.
     *
     * @var array
     */
    public $allowEntries = [
        'host',
        'user',
        'port',
        'identityfile',
        'ProxyCommand',
        'LocalForward',
        'Protocol',
        'ServerAliveInterval',
        'ServerAliveCountMax',
    ];

    public $advancedEntries = [
        'ProxyCommand',
        'LocalForward',
        'Protocol',
        'ServerAliveInterval',
        'ServerAliveCountMax',
    ];

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Determine if a host config file exists.
     *
     * @return bool
     */
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

    /**
     * Load host from a config file.
     *
     * @param  string  $name
     * @return $this
     */
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
            'port' => $config['port'] ?? self::DEFAULT_SSH_PORT,
            'identityfile' => $config['identityfile'] ?? self::DEFAULT_IDENTITY_FILE,
            'ProxyCommand' => isset($config['ProxyCommand']) ? $config['ProxyCommand'] : false,
            'LocalForward' => isset($config['LocalForward']) ? $config['LocalForward'] : false,
            'Protocol' => isset($config['Protocol']) ? $config['Protocol'] : false,
            'ServerAliveInterval' => isset($config['ServerAliveInterval']) ? $config['ServerAliveInterval'] : false,
            'ServerAliveCountMax' => isset($config['ServerAliveCountMax']) ? $config['ServerAliveCountMax'] : false,
        ]);
    }

    /**
     * Save the host to a config file.
     *
     * @return void
     */
    public function saveToConfigFile()
    {
        Helpers::ensureHostsDirectoryExists();

        $fp = fopen(Helpers::hostFilePath($this->name), 'w');

        foreach ($this->allowEntries as $entry) {
            if (property_exists($this, $entry)) {
                fwrite($fp, $entry . '="' . $this->{$entry} . '"' . PHP_EOL);
            }
        }

        fclose($fp);
    }

    /**
     * Delete the host.
     *
     * @return void
     */
    public function delete()
    {
        @unlink(Helpers::hostFilePath($this->name));
    }

    /**
     * Genrate the SSH connect command.
     *
     * @return string
     */
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

    /**
     * Get all of the hosts.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function all()
    {
        return collect(glob(Helpers::hostsPath().'/*'))->map(function ($filename) {
            return static::loadFromConfigFile(basename($filename));
        });
    }
}
