<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;

/**
 * Class ListGroupsCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ListGroupsCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toolkit:groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all current Gitlab user\'s allowed groups.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        $this->showGroupsTable();

        return 0;
    }
}
