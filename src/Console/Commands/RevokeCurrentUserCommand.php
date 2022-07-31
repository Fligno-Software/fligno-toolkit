<?php

namespace Fligno\FlignoToolkit\Console\Commands;

use Fligno\StarterKit\Traits\UsesCommandCustomMessagesTrait;
use Illuminate\Console\Command;

/**
 * Class RevokeCurrentUserCommand
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
class RevokeCurrentUserCommand extends Command
{
    use UsesCommandCustomMessagesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'toolkit:user:revoke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove current Gitlab user from Composer Auth then revoke the token.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->call('toolkit:user:remove');

        $this->ongoing('Revoking Personal Access Token (PAT)...');

        if (flignoToolkit()->revokeToken()) {
            $this->done('Revoked Personal Access Token (PAT).');
        } else {
            $this->failed('Failed to revoke Personal Access Token (PAT).');
            $this->note('Manually revoke tokens here: '.gitlabSdk()->getUrl('-/profile/personal_access_tokens'));
        }

        return self::SUCCESS;
    }
}
