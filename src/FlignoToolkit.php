<?php

namespace Fligno\FlignoToolkit;

use Fligno\GitlabSdk\GitlabSdk;
use Illuminate\Support\Collection;
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
        $this->setPrivateToken($this->getGitlabTokenFromComposerAuth());
    }

    /***** GETTERS & SETTERS *****/

    /**
     * @param string|null $privateToken
     * @param bool $persistToComposerAuth
     * @param callable|null $callbackWithSteps
     */
    public function setPrivateToken(?string $privateToken, bool $persistToComposerAuth = true, callable $callbackWithSteps = null): void
    {
        $hasCallback = (bool) $callbackWithSteps;
        $step = 0;

        if ($privateToken) {
            $privateToken = trim($privateToken);
        }

        if ($privateToken && $persistToComposerAuth)
        {
            $process = $this->createProcess([
                'composer',
                'global',
                'config',
                'http-basic.'  . config('gitlab-sdk.url'),
                '___token___',
                $privateToken
            ]);

            $process->disableOutput();

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            if ($process->isSuccessful()) {
                $hasCallback && $callbackWithSteps($step);
            }
            else {
                $hasCallback && $callbackWithSteps(-1);
            }
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
    public function getGitlabTokenFromComposerAuth(): ?string
    {
        $process = $this->createProcess([
            'composer',
            'global',
            'config',
            'http-basic.' . config('gitlab-sdk.url') . '.password'
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
     * @param callable|null $callbackWithSteps
     * @return Collection|null
     */
    public function getCurrentUser(callable $callbackWithSteps = null): ?Collection
    {
        $hasCallback = (bool) $callbackWithSteps;
        $step = 0;

        $hasCallback && $callbackWithSteps($step++);

        $req = $this->getGitlabSdk()->users()->current()();

        if ($req->ok()) {

            $hasCallback && $callbackWithSteps($step);

            return $req->collect();
        }

        $hasCallback && $callbackWithSteps(-1);

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
        $req = $this->getGitlabSdk()->packages()->allPackages()($groupId, [
            'package_type' => 'composer',
            'order_by' => 'name',
        ]);

        if ($req->ok()) {
            return $req->collect();
        }

        return null;
    }

    /***** OTHER METHODS *****/

    /**
     * @param Collection|array $arguments
     * @param string|null $workingDirectory
     * @return Process
     */
    public function createProcess(Collection|array $arguments, string $workingDirectory = null): Process
    {
        if (! $workingDirectory) {
            $workingDirectory = base_path();
        }

        if ($arguments instanceof Collection) {
            $arguments = $arguments->toArray();
        }

        return new Process($arguments, $workingDirectory);
    }

    /**
     * @param string $package
     * @param bool $isDevDependency
     * @param int|null $groupId
     * @param string|null $workingDirectory
     * @param bool $shouldUpdate
     * @param callable|null $callbackWithSteps
     * @return bool
     */
    public function requirePackage(string $package, bool $isDevDependency = false, int $groupId = null, string $workingDirectory = null, bool $shouldUpdate = true, callable $callbackWithSteps = null): bool
    {
        $shouldRequire = true;
        $step = 0;
        $hasCallback = (bool) $callbackWithSteps;

        if ($groupId) {
            $repositoryArguments = [
                'composer',
                'config',
                'repositories.' . config('gitlab-sdk.url') . '/' . $groupId,
                "{\"type\": \"composer\", \"url\": \"{$this->getGitlabSdk()->getApiUrl()}/group/$groupId/-/packages/composer/packages.json\"}"
            ];

            $process = $this->createProcess($repositoryArguments, $workingDirectory);

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            $shouldRequire = $process->isSuccessful();

            if ($shouldRequire) {
                $hasCallback && $callbackWithSteps($step++);
            }
            else {
                $hasCallback && $callbackWithSteps(-1);
            }
        }

        if ($shouldRequire) {
            $packageArguments = collect([
                'composer',
                'require',
                $package,
            ])->when(! $shouldUpdate, function (Collection $collection) {
                return $collection->push('--no-update');
            })->when($isDevDependency, function (Collection $collection) {
                return $collection->push('--dev');
            });

            $process = $this->createProcess($packageArguments, $workingDirectory);

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            $success = $process->isSuccessful();

            if ($success) {
                $hasCallback && $callbackWithSteps($step);
            }
            else {
                $hasCallback && $callbackWithSteps(-1);
            }

        }

        return false;
    }
}
