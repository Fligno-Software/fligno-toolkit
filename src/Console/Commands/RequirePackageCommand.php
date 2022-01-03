<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class RequirePackageCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class RequirePackageCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'toolkit:require';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Require a package from current Gitlab user\'s allowed packages.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $isDevDependency = $this->option('dev');

        $this->fetchUserData();

        $this->choosePackageFromTable();

        $callbackWithSteps = function (int $step) use ($isDevDependency) {
            switch ($step) {
                case -1:
                    $this->failed('A step encountered an error.');
                    break;
                case 0:
                    $this->ongoing('Adding Gitlab Group #' . $this->groupChoice . ' to Composer repositories...');
                    break;
                case 1:
                    $this->done('Added Gitlab Group #' . $this->groupChoice . ' to Composer repositories...');
                    break;
                case 2:
                    $this->ongoing('Requiring ' . $this->packageChoice . ($isDevDependency ? ' as dev dependency' : '') . '...');
                    break;
                case 3:
                    $this->done('Required ' . $this->packageChoice . '...');
                    break;
            }
        };

        $hasUpdated = flignoToolkit()->requirePackage($this->packageChoice, $isDevDependency, $this->groupChoice, null, true, $callbackWithSteps);

        return (int) ! $hasUpdated;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'dev', 'd', InputOption::VALUE_NONE, 'Require as dev dependency.'
            ]
        ];
    }
}
