<?php

namespace Suth\Merits\Commands;

use Illuminate\Console\Command;

class MeritsCommand extends Command
{
    public $signature = 'laravel-merits';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
