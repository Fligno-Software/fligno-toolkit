<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabFormattedDataTrait;
use Illuminate\Console\Command;

/**
 * Class ListGroupsCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ListGroupsCommand extends Command
{
    use UsesGitlabFormattedDataTrait;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        self::setGroupsData();

        $this->showFormattedGroupsDataTable();

        return 0;
    }
}
