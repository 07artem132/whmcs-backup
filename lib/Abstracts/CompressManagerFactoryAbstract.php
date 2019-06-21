<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:49
 */

namespace WHMCS\Module\Addon\backupWHMCS\Abstracts;

use WHMCS\Module\Addon\backupWHMCS\Controllers\CompressBzip2Controller;
use WHMCS\Module\Addon\backupWHMCS\Controllers\CompressGzipController;
use WHMCS\Module\Addon\backupWHMCS\Controllers\CompressNoneController;

abstract class CompressManagerFactoryAbstract
{
    /**
     * @param string $c
     * @throws  \Exception
     * @return CompressBzip2Controller|CompressGzipController|CompressNoneController
     */
    public static function create($c)
    {
        $c = ucfirst(strtolower($c));
        if (!CompressMethodAbstract::isValid($c)) {
            throw new \Exception("Compression method ($c) is not defined yet");
        }

        $method = "WHMCS\\Module\\Addon\\backupWHMCS\\Controllers\\Compress" . $c . 'Controller';

        return new $method;
    }
}