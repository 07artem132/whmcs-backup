<?php
/**
 * Created by PhpStorm.
 * User: Artem
 * Date: 24.04.2019
 * Time: 15:27
 */

namespace WHMCS\Module\Addon\backupWHMCS\Configs;

use WHMCS\Module\Addon\Setting;

class ModuleConfig
{
    private static $defaultLanguage = 'russian';
    private static $whmcsRootDir = ROOTDIR;
    private static $moduleName = 'backupWHMCS';

    /**
     * @return mixed
     */
    public static function getWhmcsRootDir()
    {
        return self::$whmcsRootDir;
    }

    /**
     * @return string
     */
    public static function getDefaultLanguage()
    {
        return self::$defaultLanguage;
    }

    /**
     * @return string
     */
    public static function getModuleName()
    {
        return self::$moduleName;
    }

    public static function getModuleLink()
    {
        global $customadminpath;

        return '/' . $customadminpath . '/addonmodules.php?module=' . self::getModuleName();
    }

    public static function getModuleSetting($setting)
    {
        return Setting::Module(ModuleConfig::getModuleName())
            ->where('setting', '=', $setting)
            ->first()->value;
    }
}