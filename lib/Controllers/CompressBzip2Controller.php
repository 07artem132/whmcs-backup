<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:40
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use WHMCS\Module\Addon\backupWHMCS\Abstracts\CompressManagerFactoryAbstract;

class CompressBzip2Controller extends CompressManagerFactoryAbstract
{
    private $fileHandler = null;

    /**
     * CompressBzip2 constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!function_exists("bzopen")) {
            throw new \Exception("Compression is enabled, but bzip2 lib is not installed or configured properly");
        }
    }

    /**
     * @param string $filename
     * @throws \Exception
     * @return boolean
     */
    public function open($filename)
    {
        $this->fileHandler = bzopen($filename, "w");
        if (false === $this->fileHandler) {
            throw new \Exception("Output file is not writable");
        }

        return true;
    }

    /**
     * @param $str
     * @return int
     * @throws \Exception
     */
    public function write($str)
    {
        if (false === ($bytesWritten = bzwrite($this->fileHandler, $str))) {
            throw new \Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    /**
     * @return int
     */
    public function close()
    {
        return bzclose($this->fileHandler);
    }
}
