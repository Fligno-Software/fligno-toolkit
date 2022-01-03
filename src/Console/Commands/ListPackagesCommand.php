<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;

/**
 * Class ListPackagesCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ListPackagesCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'toolkit:packages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all current Gitlab user\'s allowed packages.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        $this->showPackagesTable();

        return 0;
    }
}
