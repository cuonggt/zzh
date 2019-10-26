# zzh

- Have too many server credentials to remember
- Feel hard to use SSH config file
- Wanna easier to connect to your servers using your favorite terminal

Don't worry, `zzh` is for you!

## Installation

Before using `zzh`, make sure you have PHP >= 7.1 and [Composer](https://getcomposer.org/) installed on your machine.

Install `zzh` with Composer:

    composer global require cuonggt/zzh

Make sure to place composer's system-wide vendor bin directory in your `$PATH` so the zzh executable can be located by your system. This directory exists in different locations based on your operating system; however, some common locations include:

- macOS and GNU / Linux Distributions: `$HOME/.composer/vendor/bin`
- Windows: `%USERPROFILE%\AppData\Roaming\Composer\vendor\bin`

To view all of the available `zzh` commands, you may use the `list` command:

    zzh list

## Managing Connections

### Add a new host

    zzh host:add <host>
    
### Connect to a host

    zzh host:connect <host>

### Export to SSH config file

    zzh host:export
    
### List the hosts

    zzh host:list
    
### Edit a host
    
    zzh host:edit <host>

### Delete a host

    zzh host:delete <host>

## License

`zzh` is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
