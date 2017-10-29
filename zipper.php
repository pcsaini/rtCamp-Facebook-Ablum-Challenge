<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 29/10/17
 * Time: 10:52 AM
 */


$zip_folder = "";

class zipper
{

    public function get_zip($album_download_directory)
    {
        $response = '<span style="color: #ffffff;">Sorry due to some reasons albums is not downloaded.</span>';
        if (isset($album_download_directory)) {
            $zip_folder = $this->make_zip($album_download_directory);
            if (!empty($zip_folder)) {
                $response = '<div class="text-center text-success">
                    <i class="fa fa-check-circle fa-3x"></i>
                    <br>
                    <h3 class="text-success">Zip Complete</h3>
                    <a href="' . $zip_folder . '.zip" id="download-link" class="btn btn-success" >Download Zip Folder</a></div>';
            }
        }
        return $response;
    }

    public function make_zip($album_download_directory)
    {
        $zipfilename = "";
        if (isset($album_download_directory)) {
            //$zipfilename = 'libs/resources'.DIRECTORY_SEPARATOR.'albums'.DIRECTORY_SEPARATOR.'fb-album_'.date("Y-m-d").'_'.date("H-i-s");
            $zipfilename = 'zip/fb-album_' . date("Y-m-d") . '_' . date("H-i-s");

            // name of folder starting from the root of the webserver
            // as in Wordpress /wp-content/themes/ (end on backslash)


            $folder = dirname($_SERVER['PHP_SELF']) . '/' . $album_download_directory;

            // Server Root
            $root = $_SERVER["DOCUMENT_ROOT"];

            // source of the folder to unpack
            $sourcedir = $root . $folder; // target directory

            // Don't use more than half the memory limit
            $memory_limit = $this->getMemoryLimit();
            $maxsize = $memory_limit / 2;

            // Is zipping possible on the server ?
            if (!extension_loaded('zip')) {
                echo 'Zipping not possible on this server';
                exit;
            }

            // Get the files to zip
            $foldercontent = $this->LoadZipFiles($album_download_directory);
            if ($foldercontent === false) {
                echo 'Something went wrong gathering the file entries';
                exit;
            }

            // Process the files to zip
            $zip = $this->ProcessZip($foldercontent, $zipfilename, $maxsize);
            if ($zip === false) {
                echo 'Something went wrong zipping the files';
            }

            // clear the stat cache (created by filesize command)
            clearstatcache();

            $this->remove_directory($album_download_directory);
        }
        return $zipfilename;
    }

    public function getMemoryLimit()
    {
        $memory_limit = ini_get('memory_limit');

        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }

        return $memory_limit;
    }

    public function LoadZipFiles($source)
    {

        if (!file_exists($source)) {
            return false;
        }

        $a = array();

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {

                // Ignore "." and ".." folders
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;


                if (is_dir($file) === true) {
                    $a[] = array(
                        'type' => 'dir',
                        'source' => str_replace($source . '/', '', $file . '/'),
                        'file' => $file,
                        'size' => 0
                    );
                } else if (is_file($file) === true) {
                    $src = str_replace($source . '/', '', $file);
                    $size = filesize($file);

                    $a[] = array(
                        'type' => 'file',
                        'source' => $src,
                        'file' => $file,
                        'size' => false != $size ? $size : 16000 // this is fallback in case no size
                    );
                }
            }
        }

        return $a;
    }

    public function ProcessZip($foldercontent, $folder, $maxsize)
    {

        $split = array();

        $splits = 1;
        $t = 0;

        // Determine how many zip files to create
        if (isset($foldercontent)) {
            foreach ($foldercontent as $entry) {

                $t = $t + $entry['size'];

                if ($entry['type'] == 'dir') {
                    $lastdir = $entry;
                }

                if ($t >= $maxsize) {
                    $splits++;
                    $t = 0;
                    // create lastdir in next archive, in case files still exist
                    // even if the next file is not in this archive it doesn't hurt
                    if ($lastdir !== '') {
                        $split[$splits][] = $lastdir;
                    }
                }

                $split[$splits][] = $entry;
            }


            // delete the $foldercontent array
            unset($foldercontent);

            // Create the folder to put the zip files in
            $date = new DateTime();
            $tS = $date->format('YmdHis');


            // Process the splits
            foreach ($split as $idx => $sp) {

                // create the zip file

                $zip = new ZipArchive();

                $destination = $folder . '.zip';

                if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
                    return false;
                }

                $i = 1;
                $dir = "";
                foreach ($sp as $entry) {
                    if ($entry['type'] === 'dir') {
                        $dir = explode('\\', $entry['file']);
                        $zip->addEmptyDir(end($dir));
                    } else {
                        $zip->addFromString(end($dir) . '/' . $i . '.jpg', file_get_contents($entry['file']));
                        $i++;
                    }
                }

                $zip->close();
            }

            return array(
                'splits' => count($split),
                'foldername' => ''
            );
        }
    }

    public function remove_directory($directory)
    {
        if (isset($directory)) {
            foreach (glob("{$directory}/*") as $file) {
                if (is_dir($file)) {
                    $this->remove_directory($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($directory);
        }
    }

}

?>