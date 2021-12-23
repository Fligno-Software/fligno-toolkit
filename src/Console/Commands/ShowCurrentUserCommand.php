<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\FlignoToolkit\Traits\UsesGitlabFormattedDataTrait;
use Illuminate\Console\Command;

/**
 * Class ShowCurrentUserCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class ShowCurrentUserCommand extends Command
{
    use UsesGitlabFormattedDataTrait;

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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $user = fligno_toolkit()->getCurrentUser();

        if (! $user)
        {
            do{
                $this->error('Gitlab Personal Access Token (PAT) is EMPTY.');
                $this->info('Create a PAT here: ' . fligno_toolkit()->getGitlabSdk()->getUrl() . '/-/profile/personal_access_tokens');
                $token = $this->secret('Enter Personal Access Token');
            }
            while(! $token);

            fligno_toolkit()->setPrivateToken($token);
        }

        return $this->call('toolkit:user');
    }
}
