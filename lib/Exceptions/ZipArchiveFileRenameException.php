<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:18
 */

namespace WHMCS\Module\Addon\backupWHMCS\Exceptions;

class ZipArchiveFileRenameException extends backupWHMCSBaseException
{
    function __construct($name, $newName)
    {
        parent::__construct('Не удалось изменить файла с ' . $name . ' на ' . $newName, 0, $this);
    }
}