<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:39
 */

namespace WHMCS\Module\Addon\backupWHMCS\Abstracts;

use WHMCS\Module\Addon\backupWHMCS\Controllers\MysqlDumpController;

/**
 * Enum with all available compression methods
 *
 */
abstract class CompressMethodAbstract
{
    public static $enums = array(
        MysqlDumpController::NONE,
        MysqlDumpController::GZIP,
        MysqlDumpController::BZIP2,
    );

    /**
     * @param string $c
     * @return boolean
     */
    public static function isValid($c)
    {
        return in_array($c, self::$enums);
    }
}