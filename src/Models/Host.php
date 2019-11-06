<?php

namespace Cuonggt\Zzh\Models;

use Cuonggt\Zzh\Helpers;

class Host
{
    /**
     * The host name.
     *
     * @var string
     */
    public $name;

    /**
     * The host address.
     *
     * @var string
     */
    public $host;

    /**
     * The host user.
     *
     * @var string
     */
    public $user;

    /**
     * The host port.
     *
     * @var string
     */
    public $port;

    /**
     * The host identity file.
     *
     * @var string
     */
    public $identityfile;

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

    /**
     * @param string $name
     */
    public function __construct(
        $name,
        $host,
        $user,
        $port,
        $identityfile,
        $advancedEntries = []
    ) {
        $this->name = $name;
        $this->host = $host;
        $this->user = $user;
        $this->port = $port;
        $this->identityfile = $identityfile;

        foreach ($advancedEntries as $k => $v) {
            $this->{$k} = $v;
        }
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
        $config = parse_ini_file(Helpers::hostFilePath($name));

        return new static(
            $name,
            $config['host'] ?? $name,
            $config['user'] ?? Helpers::DEFAULT_SSH_USER,
            $config['port'] ?? Helpers::DEFAULT_SSH_PORT,
            $config['identityfile'] ?? Helpers::DEFAULT_IDENTITY_FILE,
            [
                'ProxyCommand' => $config['ProxyCommand'] ?? false,
                'LocalForward' => $config['LocalForward'] ?? false,
                'Protocol' => $config['Protocol'] ?? false,
                'ServerAliveInterval' => $config['ServerAliveInterval'] ?? false,
                'ServerAliveCountMax' => $config['ServerAliveCountMax'] ?? false,
            ]
        );
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
                fwrite($fp, $entry.'="'.$this->{$entry}.'"'.PHP_EOL);
            }
        }

        fclose($fp);
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
}
