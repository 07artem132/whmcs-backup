<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:18
 */

namespace WHMCS\Module\Addon\backupWHMCS\Exceptions;

use Throwable;

class ZipArchiveCloseException extends backupWHMCSBaseException
{
    function __construct()
    {
        parent::__construct( 'Не удалось записать(сохранить) архив',  0, $this);
    }
}