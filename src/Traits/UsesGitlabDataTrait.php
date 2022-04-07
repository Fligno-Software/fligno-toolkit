<?php

namespace Fligno\FlignoToolkit\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Trait UsesGitlabFormattedDataTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait UsesGitlabDataTrait
{
    use UsesCustomCommandMessagesTrait;

    /**
     * @var array|string[]
     */
    protected array $groupsHeader = ['id', 'web_url', 'name', 'description'];

    /**
     * @var array|string[]
     */
    protected array $packagesHeader = ['id', 'name', 'version'];

    /**
     * @var Collection|null
     */
    protected ?Collection $userData;

    /**
     * @var Collection|null
     */
    protected ?Collection $groupsData;

    /**
     * @var Collection|null
     */
    protected ?Collection $packagesData;

    /**
     * @var int
     */
    protected int $groupChoice;

    /**
     * @var string
     */
    protected string $packageChoice;

    /*****
     * SETTERS & GETTERS
     *****/

    /**
     * @return void
     */
    public function fetchUserData(): void
    {
        $getCurrentUserCallbackWithSteps = function (int $step) {
            switch ($step) {
                case -1:
                    $this->failed('Failed to fetch user information using Gitlab Personal Access Token (PAT)....');
                    break;
                case 0:
                    $this->ongoing('Fetching user information using Gitlab Personal Access Token (PAT)...');
                    break;
                case 1:
                    $this->done('Fetched user information using Gitlab Personal Access Token (PAT)...');
                    break;
            }
        };

        $setPrivateTokenCallbackWithSteps = function (int $step) {
            switch ($step) {
                case -1:
                    $this->failed('Failed to persist token to COMPOSER_AUTH.');
                    break;
                case 0:
                    $this->ongoing('Saving PAT to COMPOSER_AUTH...');
                    break;
                case 1:
                    $this->done('Saved PAT to COMPOSER_AUTH...');
                    break;
            }
        };

        while (!($this->userData = fligno_toolkit()->getCurrentUser($getCurrentUserCallbackWithSteps))) {
            do {
                $this->note('Create a PAT here: ' .
                    fligno_toolkit()->getGitlabSdk()->getUrl() . '/-/profile/personal_access_tokens.');
                $this->note('When creating a PAT, only choose "read_api" from scopes.');
                $token = $this->secret('Enter Personal Access Token (PAT)');

                fligno_toolkit()->setPrivateToken($token, true, $setPrivateTokenCallbackWithSteps);
            } while (! $token);
        }
    }

    /**
     * @return Collection|null
     */
    public function getUserData(): ?Collection
    {
        return $this->userData;
    }

    /**
     * @return void
     */
    public function fetchGroupsData(): void
    {
        $groups = flignoToolkit()->getCurrentUserGroups();

        if (! $groups) {
            throw new RuntimeException("Failed to get current user's groups.");
        }

        $this->groupsData = $groups->mapWithKeys(
            function ($group) {
                return [ $group['id'] => Arr::only($group, $this->groupsHeader) ];
            }
        );
    }

    /**
     * @return Collection|null
     */
    public function getGroupsData(): ?Collection
    {
        return $this->groupsData;
    }

    /**
     * @param int $groupId
     */
    public function fetchPackagesData(int $groupId): void
    {
        $packages = flignoToolkit()->getGroupPackages($groupId);

        if (! $packages) {
            throw new RuntimeException('Failed to get current group packages.');
        }

        $this->packagesData = $packages->mapWithKeys(
            function ($package) {
                return [ $package['id'] => Arr::only($package, $this->packagesHeader) ];
            }
        );
    }

    /**
     * @return Collection|null
     */
    public function getPackagesData(): ?Collection
    {
        return $this->packagesData;
    }

    /*****
     * OTHER FUNCTIONS
     *****/

    /**
     * @return void
     */
    public function getGroupsTable(): void
    {
        $this->table($this->groupsHeader, $this->getGroupsData()?->toArray());
    }

    public function showGroupsTable(): void
    {
        $this->ongoing('Fetching current user\'s Gitlab groups...');

        $this->fetchGroupsData();

        $this->done('Fetched current user\'s Gitlab groups...');

        $this->getGroupsTable();
    }

    /**
     * @return void
     */
    public function getPackagesTable(): void
    {
        $this->table($this->packagesHeader, $this->getPackagesData()?->toArray());
    }

    /**
     * @return void
     */
    public function showPackagesTable(): void
    {
        $this->showGroupsTable();

        $groupChoices = $this->getGroupsData()?->map(
            function ($group) {
                return $group['id'];
            }
        );

        $this->groupChoice = $this->choice('Select Group ID', $groupChoices->toArray());

        $this->ongoing('Fetching group\'s Gitlab packages...');

        $this->fetchPackagesData($this->groupChoice);

        $this->done('Fetched group\'s Gitlab packages...');

        $this->getPackagesTable();
    }

    /**
     * @return void
     */
    public function choosePackageFromTable(): void
    {
        $this->showPackagesTable();

        // Prepare Packages for Selection
        $packageChoices = $this->getPackagesData()
            ?->filter(
                function ($package) {
                    return ! Str::of($package['version'])->contains('dev');
                }
            )
            ->map(
                function ($package) {
                    [ 'name' => $name, 'version' => $version ] = $package;
                    return $name . ':^' . $version;
                }
            );

        $this->packageChoice = $this->choice('Select Package', $packageChoices->toArray());
    }
}
