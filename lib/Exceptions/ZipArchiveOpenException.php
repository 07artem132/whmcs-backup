<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:18
 */

namespace WHMCS\Module\Addon\backupWHMCS\Exceptions;

class ZipArchiveOpenException extends backupWHMCSBaseException
{
    function __construct($path)
    {
        parent::__construct($message = 'Не удалось создать архив по пути: ' . $path, $code = 0, $previous = null);
    }
}