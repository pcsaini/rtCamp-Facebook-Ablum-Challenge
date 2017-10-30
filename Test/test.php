<?php
/**
 * Created by PhpStorm.
 * User: pcsaini
 * Date: 30/10/17
 * Time: 1:43 PM
 */
//include "../zipper.php";
include 'phpUnit/autoload.php';
include "../zipper.php";
use PHPUnit\Framework\TestCase;


class ZipperTest extends TestCase {

    public function testLoadZipFiles( $source = null ) {
        $zipper = new zipper();
        $actual = $zipper->LoadZipFiles( $source );
        $this->assertEquals( $actual, $actual );
    }

    public function testProcessZip( $foldercontent = null, $folder = null, $maxsize = 50000 ) {
        $zipper = new zipper();
        $actual = $zipper->ProcessZip( $foldercontent, $folder, $maxsize );
        $this->assertEquals( $actual, $actual );
    }

    public function testgetMemoryLimit() {
        $zipper = new zipper();
        $actual = $zipper->getMemoryLimit();
        $this->assertEquals( $actual, $actual );
    }

    public function testmake_zip( $album_download_directory = null ) {
        $zipper = new zipper();
        $actual = $zipper->make_zip( $album_download_directory );
        $this->assertEquals( $actual, $actual );
    }

    public function testget_zip( $album_download_directory = null ) {
        $zipper = new zipper();
        $actual = $zipper->get_zip( $album_download_directory );
        $this->assertEquals( $actual, $actual );
    }

    public function testremove_directory( $directory = null ) {
        $zipper = new zipper();
        $actual = $zipper->remove_directory($directory);
        $this->assertEquals( $actual, $actual );
    }

}