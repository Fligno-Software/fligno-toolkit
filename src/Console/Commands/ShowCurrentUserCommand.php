<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;

/**
 * Class ShowCurrentUserCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ShowCurrentUserCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'toolkit:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show current user information from Gitlab Personal Access Token (PAT).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        if ($this->getUserData()) {
            $this->note('Welcome to Fligno Toolkit, '.$this->getUserData()->name.' ('.$this->getUserData()->email.')!');
        }

        return 0;
    }
}
