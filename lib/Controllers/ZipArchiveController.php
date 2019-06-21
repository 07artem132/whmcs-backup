<?php
/**
 * Created by PhpStorm.
 * User: Артём
 * Date: 13.06.2019
 * Time: 15:08
 */

namespace WHMCS\Module\Addon\backupWHMCS\Controllers;


class ZipArchiveController extends \ZipArchive
{
    private $logs = [];

    function addDir($source, $ignorePath = [])
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                continue;

            $file = realpath($file);

            $path = substr($file, strlen($source), (strrpos($file, '/') - strlen($source)) + 1);

            if (is_dir($file) === true) {
                foreach ($ignorePath as $ignore) {
                    $pattern = '/^' . str_replace('/', '\/', $ignore) . '/';
                    $count = preg_match($pattern, $path, $matches, PREG_OFFSET_CAPTURE, 0);

                    if ($count > 0) {
                        continue 2;
                    }
                }

                if (!$this->addEmptyDir(str_replace($source . '/', '', $file . '/'))) {
                    $this->logs[] = 'error add empty dir  ' . $source;
                }
            } else if (is_file($file) === true) {
                foreach ($ignorePath as $ignore) {
                    $pattern = '/^' . str_replace('/', '\/', $ignore) . '/';
                    $count = preg_match($pattern, $path, $matches, PREG_OFFSET_CAPTURE, 0);

                    if ($count > 0) {
                        continue 2;
                    }
                }

                $localName = str_replace($source . '/', '', $file);

                if (!$this->addFile($file, $localName)) {
                    $this->logs[] = 'error add file  ' . $file;
                }

                if (!$this->setCompressionName($localName, self::CM_DEFAULT)) {
                    $this->logs[] = 'error set compression name ' . $localName;
                }
            }
        }
    }

    public function getLog()
    {
        return $this->logs;
    }
}