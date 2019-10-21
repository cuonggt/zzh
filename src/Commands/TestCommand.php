<?php

namespace Cuonggt\Zzh\Commands;

use Cuonggt\Zzh\Helpers;

class TestCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Test command');
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        Helpers::info('Test command.');
    }
}
