<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:48
 */

namespace WHMCS\Module\Addon\backupWHMCS\Abstracts;

/**
 * Enum with all available TypeAdapter implementations
 *
 */
abstract class TypeAdapterAbstract
{
    public static $enums = array(
        "Sqlite",
        "Mysql"
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