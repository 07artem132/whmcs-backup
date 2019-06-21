<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:18
 */

namespace WHMCS\Module\Addon\backupWHMCS\Exceptions;

use Throwable;

class ZipArchiveAddFileErrorArrayException extends backupWHMCSBaseException
{
    function __construct($logs)
    {
        parent::__construct('Следушюшие файлы не были добавлены в архив: '.implode(PHP_EOL,$logs),  0, $this);
    }
}