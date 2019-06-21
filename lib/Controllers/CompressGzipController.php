<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 15.06.2019
 * Time: 10:43
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;

use WHMCS\Module\Addon\backupWHMCS\Abstracts\CompressManagerFactoryAbstract;

 class CompressGzipController extends CompressManagerFactoryAbstract
{
    private $fileHandler = null;

    /**
     * CompressGzip constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!function_exists("gzopen")) {
            throw new \Exception("Compression is enabled, but gzip lib is not installed or configured properly");
        }
    }

    /**
     * @param string $filename
     * @throws \Exception
     * @return boolean
     */
    public function open($filename)
    {
        $this->fileHandler = gzopen($filename, "wb");
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
        if (false === ($bytesWritten = gzwrite($this->fileHandler, $str))) {
            throw new \Exception("Writting to file failed! Probably, there is no more free space left?");
        }
        return $bytesWritten;
    }

    public function close()
    {
        return gzclose($this->fileHandler);
    }
}

