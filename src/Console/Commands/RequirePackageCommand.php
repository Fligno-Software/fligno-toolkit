<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabFormattedDataTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class RequirePackageCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class RequirePackageCommand extends Command
{
    use UsesGitlabFormattedDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toolkit:install';

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
    public function handle()
    {
        self::setCurrentUserData();

        $tableData = ListGroupsCommand::getFormattedGroupsData()->get('data');
        $groupIds = Arr::pluck($tableData, 'id');

        $choice = $this->choice('Select Group ID', $groupIds);

        $packagesData = ListPackagesCommand::getFormattedPackagesData($choice);

        $this->table($packagesData->get('headers'), $packagesData->get('data'));

        collect($packagesData->get('data'))->map(function ($package){
            //finish this shit
        });

        return 0;

        return Command::SUCCESS;
    }
}
