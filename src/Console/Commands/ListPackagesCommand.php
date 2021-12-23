<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabFormattedDataTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Class ListPackagesCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ListPackagesCommand extends Command
{
    use UsesGitlabFormattedDataTrait;

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
    protected $description = 'Command description';

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

//        $this->getDefinition()->addArgument(new InputArgument(
//            'group_id',
//            InputArgument::OPTIONAL,
//            'Gitlab Group ID where the package is located.'
//        ));
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        self::setGroupsData();

        $this->showFormattedGroupsDataTable();

        $choice = $this->choice('Select Group ID', self::getGroupsData()?->pluck('id')->toArray());

        self::setPackagesData($choice);

        $this->showFormattedPackagesDataTable();

        return 0;
    }
}
