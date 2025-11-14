<?php

namespace TS_DependencyInjection\Classes;

use TS_Exception\Classes\DIException;

/**
 * Implements the Service Locator pattern.
 * A static wrapper around the main DI Application container.
 */
class ServiceLocator
{
    private static ?Application $application = null;

    /**
     * Sets the main application container.
     * This should be called exactly once at application bootstrap.
     */
    public static function setApplication(Application $application): void
    {
        self::$application = $application;
    }

    /**
     * Gets a service from the container.
     *
     * @template T
     * @param class-string<T> $id The class or interface ID to resolve.
     * @return mixed
     * @throws DIException
     */
    public static function get(string $id): mixed
    {
        if (self::$application === null) {
            throw new DIException('service_locator_not_set', [':id' => $id]);
        }

        try {
            return self::$application->get($id);
        } catch (\Exception $e) {
            throw new DIException('service_resolution_failed', [':id' => $id], previous: $e);
        }
    }
}