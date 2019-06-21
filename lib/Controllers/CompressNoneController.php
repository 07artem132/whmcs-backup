<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:42
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use WHMCS\Module\Addon\backupWHMCS\Abstracts\CompressManagerFactoryAbstract;

class CompressNoneController extends CompressManagerFactoryAbstract
{
    private $fileHandler = null;

    /**
     * @param string $filename
     * @throws \Exception
     * @return boolean
     */
    public function open($filename)
    {
        $this->fileHandler = fopen($filename, "wb");
        if (false === $this->fileHandler) {
            throw new \Exception("Output file is not writable");
        }

        return true;
    }

    /**
     * @param $str
     * @return bool|int
     * @throws \Exception
     */
    public function write($str)
    {
        if (false === ($bytesWritten = fwrite($this->fileHandler, $str))) {
            throw new \Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    /**
     * @return bool
     */
    public function close()
    {
        return fclose($this->fileHandler);
    }
}