<?php

namespace TS_DependencyInjection\Classes;

use Exception;
use LogicException;

class ServiceLocator
{
    private static ?Application $application = null;

    public static function SetApplication(Application $app): void
    {
        self::$application = $app;
    }

    /**
     * @throws Exception
     */
    public static function GetController(string $controller): object
    {
        if (is_null(self::$application))
            throw new LogicException('Application not initialized');

        return self::$application->GetController($controller);
    }

    /**
     * @throws Exception
     */
    public static function GetService(string $service): object
    {
        if (is_null(self::$application))
            throw new LogicException('Application not initialized');

        return self::$application->GetService($service);
    }
}