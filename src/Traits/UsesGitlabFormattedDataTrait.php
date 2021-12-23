<?php

namespace Fligno\FlignoToolkit\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Trait UsesGitlabFormattedDataTrait
 *
 * @author James Carlo Luchavez <jamescarlo.luchavez@fligno.com>
 */
trait UsesGitlabFormattedDataTrait
{
    /**
     * @var array|string[]
     */
    protected static array $groupsHeader = ['id', 'web_url', 'name', 'description'];

    /**
     * @var array|string[]
     */
    protected static array $packagesHeader = ['id', 'name', 'version'];

    /**
     * @var Collection|null
     */
    protected static ?Collection $groupsData;

    /**
     * @var Collection|null
     */
    protected static ?Collection $packagesData;

    /**
     * @var Collection|null
     */
    protected static ?Collection $currentUserData;

    /***** SETTERS & GETTERS *****/

    /**
     */
    public static function setCurrentUserData(): void
    {
        $user = flignoToolkit()->getCurrentUser();

        if (! $user)
        {
            throw new RuntimeException("Failed to get current user from Personal Access Token.");
        }

        self::$currentUserData = $user;
    }

    /**
     * @return Collection|null
     */
    public static function getCurrentUserData(): ?Collection
    {
        return self::$currentUserData;
    }

    /**
     * @return void
     */
    public static function setGroupsData(): void
    {
        $groups = flignoToolkit()->getCurrentUserGroups();

        if (! $groups)
        {
            throw new RuntimeException("Failed to get current user's groups.");
        }

        self::$groupsData = $groups->map(function ($group) {
            return Arr::only($group, self::$groupsHeader);
        });
    }

    /**
     * @return Collection|null
     */
    public static function getGroupsData(): ?Collection
    {
        return self::$groupsData;
    }

    /**
     * @param int $groupId
     */
    public static function setPackagesData(int $groupId): void
    {
        $packages = flignoToolkit()->getGroupPackages($groupId);

        if (! $packages)
        {
            throw new RuntimeException('Failed to get current group packages.');
        }

        self::$packagesData = $packages->map(function ($group) {
            return collect($group)->only(self::$packagesHeader);
        });
    }

    /**
     * @return Collection|null
     */
    public static function getPackagesData(): ?Collection
    {
        return self::$packagesData;
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @return void
     */
    public function showFormattedGroupsDataTable(): void
    {
        $this->table(self::$groupsHeader, self::getGroupsData()?->toArray());
    }

    /**
     * @return void
     */
    public function showFormattedPackagesDataTable(): void
    {
        $this->table(self::$packagesHeader, self::getPackagesData()?->toArray());
    }
}
