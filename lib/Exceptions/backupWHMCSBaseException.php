<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 16.06.2019
 * Time: 10:39
 */

namespace WHMCS\Module\Addon\backupWHMCS\Exceptions;


class backupWHMCSBaseException extends \Exception
{
    function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    function getFormattedMessage()
    {
        return $this->getMessage();
    }
}