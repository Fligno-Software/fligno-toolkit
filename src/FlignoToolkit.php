<?php

namespace Fligno\FlignoToolkit;

use Fligno\GitlabSdk\Data\Groups\GetAllGroupsAttributes;
use Fligno\GitlabSdk\Data\Packages\GetAllPackagesAttributes;
use Fligno\GitlabSdk\GitlabSdk;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

/**
 * Class FlignoToolkit
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 * @since  2021-12-20
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

    /*****
     * GETTERS & SETTERS
     *****/

    /**
     * @param string|null   $privateToken
     * @param bool          $persistToComposerAuth
     * @param callable|null $callbackWithSteps
     */
    public function setPrivateToken(
        ?string $privateToken,
        bool $persistToComposerAuth = true,
        callable $callbackWithSteps = null
    ): void {
        $hasCallback = (bool) $callbackWithSteps;
        $step = 0;

        if ($privateToken) {
            $privateToken = trim($privateToken);
        }

        if ($privateToken && $persistToComposerAuth) {
            $process = make_process(
                [
                'composer',
                'global',
                'config',
                'http-basic.'  . config('gitlab-sdk.url'),
                '___token___',
                $privateToken
                ]
            );

            $process->disableOutput();

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            if ($process->isSuccessful()) {
                $hasCallback && $callbackWithSteps($step);
            } else {
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
        $process = make_process(
            [
            'composer',
            'global',
            'config',
            'http-basic.' . config('gitlab-sdk.url') . '.password'
            ]
        );

        $process->run();

        if ($process->isSuccessful()) {
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
     * @param  callable|null $callbackWithSteps
     * @return Collection|null
     */
    public function getCurrentUser(callable $callbackWithSteps = null): ?Collection
    {
        $hasCallback = (bool) $callbackWithSteps;
        $step = 0;

        $hasCallback && $callbackWithSteps($step++);

        $req = $this->getGitlabSdk()->getHealthCheck();

        if ($req->ok()) {
            $hasCallback && $callbackWithSteps($step);
            return $req->data;
        }

        $hasCallback && $callbackWithSteps(-1);

        return null;
    }

    /**
     * @return Collection|null
     */
    public function getCurrentUserGroups(): ?Collection
    {
        $data = new GetAllGroupsAttributes;

        $result = collect();

        $i = 1;

        do {
            $data->page = $i++;
            $req = $this->getGitlabSdk()->groups()->all()($data);

            if ($req->ok()) {
                $output = $req->collect();
                $result = $result->merge($output->toArray());
            } else {
                break;
            }
        } while ($output->count() > 0);

        return $result->count() > 0 ? $result : null;
    }

    /**
     * @param  int $groupId
     * @return Collection|null
     */
    public function getGroupPackages(int $groupId): ?Collection
    {
        $data = new GetAllPackagesAttributes;

        $data->order_by = 'name';
        $data->package_type = 'composer';

        $result = collect();

        $i = 1;

        do {
            $data->page = $i++;
            $req = $this->getGitlabSdk()->packages()->allPackages()($groupId, $data);

            if ($req->ok()) {
                $output = $req->collect();
                $result = $result->merge($output->toArray());
            } else {
                break;
            }
        } while ($output->count() > 0);

        return $result->count() > 0 ? $result : null;
    }

    /*****
     * OTHER METHODS
     *****/

    /**
     * @param  string        $package
     * @param  bool          $isDevDependency
     * @param  int|null      $groupId
     * @param  string|null   $workingDirectory
     * @param  bool          $shouldUpdate
     * @param  callable|null $callbackWithSteps
     * @return bool
     */
    public function requirePackage(
        string $package,
        bool $isDevDependency = false,
        int $groupId = null,
        string $workingDirectory = null,
        bool $shouldUpdate = true,
        callable $callbackWithSteps = null
    ): bool {
        $shouldRequire = true;
        $step = 0;
        $hasCallback = (bool) $callbackWithSteps;

        if ($groupId) {
            $repositoryArguments = [
                'composer',
                'config',
                'repositories.' . config('gitlab-sdk.url') . '/' . $groupId,
                "{\"type\": \"composer\", \"url\": \"" .
                $this->getGitlabSdk()->getBaseUrl() .
                "/group/$groupId/-/packages/composer/packages.json\"}"
            ];

            $process = make_process($repositoryArguments, $workingDirectory);

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            $shouldRequire = $process->isSuccessful();

            if ($shouldRequire) {
                $hasCallback && $callbackWithSteps($step++);
            } else {
                $hasCallback && $callbackWithSteps(-1);
            }
        }

        if ($shouldRequire) {
            $packageArguments = collect(
                [
                'composer',
                'require',
                $package,
                ]
            )->when(! $shouldUpdate, function (Collection $collection) {
                return $collection->push('--no-update');
            })->when($isDevDependency, function (Collection $collection) {
                return $collection->push('--dev');
            });

            $process = make_process($packageArguments, $workingDirectory);

            $process->start();

            $hasCallback && $callbackWithSteps($step++);

            $process->wait();

            $success = $process->isSuccessful();

            if ($success) {
                $hasCallback && $callbackWithSteps($step);
            } else {
                $hasCallback && $callbackWithSteps(-1);
            }
        }

        return false;
    }
}
