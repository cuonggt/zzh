<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand
{
    /**
     * The input implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    public $input;

    /**
     * The output implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    public $output;

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Helpers::app()->instance('input', $this->input = $input);
        Helpers::app()->instance('output', $this->output = $output);

        return Helpers::app()->call([$this, 'handle']);
    }

    /**
     * Get an argument from the input list.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function argument($key)
    {
        return $this->input->getArgument($key);
    }

    /**
     * Get an option from the input list.
     *
     * @param  string  $key
     * @return mixed
     */
    protected function option($key)
    {
        return $this->input->getOption($key);
    }

    /**
     * Format input into a textual table.
     *
     * @param  array  $headers
     * @param  array  $rows
     * @param  string  $style
     * @return void
     */
    public function table(array $headers, array $rows, $style = 'borderless')
    {
        Helpers::table($headers, $rows, $style);
    }

    /**
     * Format input to textual table, remove the prior table.
     *
     * @param  array   $headers
     * @param  array  $rows
     * @return void
     */
    protected function refreshTable(array $headers, array $rows)
    {
        if ($this->rowCount > 0) {
            Helpers::write(str_repeat("\x1B[1A\x1B[2K", $this->rowCount + 4));
        }

        $this->rowCount = count($rows);

        $this->table($headers, $rows);
    }

    /**
     * Create a selection menu with the given choices.
     *
     * @param  string  $title
     * @param  array  $choices
     * @return mixed
     */
    public function menu($title, $choices)
    {
        return Helpers::menu($title, $choices);
    }
}
