<?php

namespace Cuonggt\Zzh;

use Cuonggt\Zzh\KeyChoiceQuestion;
use Illuminate\Container\Container;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Style\SymfonyStyle;

class Helpers
{
    /**
     * Display a danger message and exit.
     *
     * @param  string  $text
     * @return void
     */
    public static function abort($text)
    {
        static::danger($text);

        exit(1);
    }

    /**
     * Resolve a service from the container.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public static function app($name = null)
    {
        return $name ? Container::getInstance()->make($name) : Container::getInstance();
    }

    /**
     * Ask the user a question.
     *
     * @param  string  $question
     * @param  mixed  $default
     * @return mixed
     */
    public static function ask($question, $default = null)
    {
        $style = new SymfonyStyle(static::app('input'), static::app('output'));

        return $style->ask($question, $default);
    }

    /**
     * Display a comment message.
     *
     * @param  string  $text
     * @return void
     */
    public static function comment($text)
    {
        static::app('output')->writeln('<comment>'.$text.'</comment>');
    }

    /**
     * Ask the user a confirmation question.
     *
     * @param  string  $question
     * @param  mixed  $default
     * @return mixed
     */
    public static function confirm($question, $default = true)
    {
        $style = new SymfonyStyle(static::app('input'), static::app('output'));

        return $style->confirm($question, $default);
    }

    /**
     * Display a danger message.
     *
     * @param  string  $text
     * @return void
     */
    public static function danger($text)
    {
        static::app('output')->writeln('<fg=red>'.$text.'</>');
    }

    /**
     * Get the home directory for the user.
     *
     * @return string
     */
    public static function home()
    {
        return $_SERVER['HOME'] ?? $_SERVER['USERPROFILE'];
    }

    /**
     * Display an informational message.
     *
     * @param  string  $text
     * @return void
     */
    public static function info($text)
    {
        static::app('output')->writeln('<info>'.$text.'</info>');
    }

    /**
     * Display a message.
     *
     * @param  string  $text
     * @return void
     */
    public static function line($text = '')
    {
        static::app('output')->writeln($text);
    }

    /**
     * Ask the user to select from the given choices.
     *
     * @param  string  $question
     * @param  mixed  $default
     * @return mixed
     */
    public static function menu($title, $choices)
    {
        $style = new SymfonyStyle(static::app('input'), static::app('output'));

        return $style->askQuestion(new KeyChoiceQuestion($title, $choices));
    }

    /**
     * Ask the user a secret question.
     *
     * @param  string  $question
     * @return mixed
     */
    public static function secret($question)
    {
        $style = new SymfonyStyle(static::app('input'), static::app('output'));

        return $style->askHidden($question);
    }

    /**
     * Display a "step" message.
     *
     * @param  string  $text
     * @return void
     */
    public static function step($text)
    {
        static::line('<fg=blue>==></> '.$text);
    }

    /**
     * Format input into a textual table.
     *
     * @param  array  $headers
     * @param  array  $rows
     * @param  string  $style
     * @return void
     */
    public static function table(array $headers, array $rows, $style = 'borderless')
    {
        if (empty($rows)) {
            return;
        }

        $table = new Table(static::app('output'));

        $table->setHeaders($headers)->setRows($rows)->setStyle($style)->render();
    }

    /**
     * Write text to the console.
     *
     * @param  string  $text
     * @return void
     */
    public static function write($text)
    {
        static::app('output')->write($text);
    }

    /**
     * Get the config directory.
     *
     * @return string
     */
    public static function configPath()
    {
        return static::home().'/.zzh';
    }

    /**
     * Get the hosts directory.
     *
     * @return string
     */
    public static function hostsPath()
    {
        return static::configPath().'/hosts';
    }

    /**
     * Get the host file path.
     *
     * @param  string  $name
     * @return string
     */
    public static function hostFilePath($name)
    {
        return static::hostsPath().'/'.$name;
    }

    /**
     * Determine if a host config file exists.
     *
     * @param  string  $name
     * @return boolean
     */
    public static function hostFileExists($name)
    {
        return file_exists(static::hostFilePath($name));
    }

    /**
     * Ensure the hosts directory exists.
     *
     * @return void
     */
    public static function ensureHostsDirectoryExists()
    {
        $hostsPath = static::hostsPath();

        if (! is_dir($hostsPath)) {
            mkdir($hostsPath, 0755, true);
        }
    }

    /**
     * Get the default identity file path.
     *
     * @return string
     */
    public static function defaultIdentityFile()
    {
        return '~/.ssh/id_rsa.pub';
    }

    /**
     * Get the default SSH user.
     *
     * @return string
     */
    public static function defaultSSHUser()
    {
        return get_current_user();
    }

    /**
     * Get the default SSH port.
     *
     * @return integer
     */
    public static function defaultSSHPort()
    {
        return 22;
    }
}
