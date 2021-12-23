<?php

namespace Fligno\FlignoToolkit;

use Fligno\FlignoToolkit\Exceptions\EmptyPrivateTokenException;
use Fligno\GitlabSdk\GitlabSdk;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Class FlignoToolkit
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 * @since 2021-12-20
 */
class FlignoToolkit
{
    /**
     * @var string|null
     */
    protected string|null $privateToken;

    protected GitlabSdk $gitlabSdk;

    public function __construct()
    {
        $this->setPrivateToken(self::getGitlabTokenFromComposerAuth());
    }

    /***** GETTERS & SETTERS *****/

    /**
     * @param string|null $privateToken
     * @param bool $persistToComposerAuth
     */
    public function setPrivateToken(?string $privateToken, bool $persistToComposerAuth = true): void
    {
        $privateToken = trim($privateToken);

        if ($privateToken && $persistToComposerAuth)
        {
            $process = new Process([
                'composer',
                'global',
                'config',
                'http-basic.'  . config('gitlab-sdk.api_url'),
                '___token___',
                $privateToken
            ]);

            $process->disableOutput();

            $process->run();
        }

        $this->privateToken = $privateToken;
        $this->gitlabSdk = gitlab_sdk($privateToken);
    }

    /**
     * @return string|null
     */
    public function getPrivateToken(): ?string
    {
        return $this->privateToken;
    }

    /**
     * @return string|null
     */
    public static function getGitlabTokenFromComposerAuth(): ?string
    {
        $process = new Process([
            'composer',
            'global',
            'config',
            'http-basic.' . config('gitlab-sdk.api_url') . '.password'
        ]);

        $process->run();

        if ($process->isSuccessful())
        {
            return $process->getOutput();
        }

        return null;
    }

    /**
     * @return GitlabSdk
     */
    public function getGitlabSdk(): GitlabSdk
    {
        return $this->gitlabSdk;
    }

    /**
     * @return Collection|null
     */
    public function getCurrentUser(): ?Collection
    {
        $req = $this->getGitlabSdk()->users()->current()();

        if ($req->ok()) {
            return $req->collect();
        }

        return null;
    }

    /**
     * @return Collection|null
     */
    public function getCurrentUserGroups(): ?Collection
    {
        $req = $this->getGitlabSdk()->groups()->all()();

        if ($req->ok()) {
            return $req->collect();
        }

        return null;
    }

    /**
     * @param int $groupId
     * @return Collection|null
     */
    public function getGroupPackages(int $groupId): ?Collection
    {
        $req = $this->getGitlabSdk()->packages()->allPackages()($groupId);

        if ($req->ok()) {
            return $req->collect();
        }

        return null;
    }
}
