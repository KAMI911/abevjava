<?php

/**
 * Package Generator class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Packagegenerator
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.5
 * @link     http://ooo.itc.hu/
 */
require_once dirname(__FILE__) . '/database.php';
require_once dirname(__FILE__) . '/generatereport.php';

class PackageGenerator {

  // structured array of companies provided package data from the database
  private $_data;
  // Package object
  private $_pck;
  // database handler
  private $_db;
  private $_checkdir;
  private $_report;

  public function __construct($host, $port, $username, $password, $database) {
    try {
      $this->_db = new Database($host, $port, $username, $password, $database);
    } catch (Exception $e) {
      echo 'Could not connect to database: ' . $e->getMessage();
      exit(1);
      $this->_report = new GenerateReport;
    }
  }

  public function generate() {
    echo '[+] Generating abevjava packages ...' . PHP_EOL;
    $this->_CreatePackages('all', FALSE);
    echo '[+] Generating abevjava-resource package ...' . PHP_EOL;
    $this->_CreateResourcePackage('all', FALSE);
  }

  private function _CreatePackages($target = "all", $force = FALSE) {
    $_data = $this->_db->GetDownloadArray();
    foreach ($_data as $k => $v) {
      $_pck = new Package;
      $_pck->Open($this->_db, $v);
      $_pck->CreatePackage($target, $force);
      //$_pck -> View();
      $_pck->Close();
      $this->_report->IncreaseNew($v['short_name'], $v['version_major'] . '.' . $v['version_minor'] . '.' . $v['version_micro'] . '.' . $v['version_build'], $v['description'], $v['company_name']);
    }
  }

  private function _CreateResourcePackage($target = "all", $force = FALSE) {
    $this->_checkdir = new SHA1dir(ADDITIONAL_PATH . '/abevjava-resource/eroforrasok/*.jar', ADDITIONAL_PATH . '/abevjava-resource.sha1');
    if ($this->_checkdir->Check() || $force) {
      $_data = $this->_db->GetDownloadFromShortName('abevjava-resource', 2147483647, 1);
      foreach ($_data as $k => $v) {
	$_pck = new Package;
	$_pck->Open($this->_db, $v);
	$_pck->CreatePackage($target, TRUE);
	//$_pck -> View();
	$_pck->Close();
	$this->_report->IncreaseNew($v['short_name'], $v['version_major'] . '.' . $v['version_minor'] . '.' . $v['version_micro'] . '.' . $v['version_build'], $v['description'], $v['company_name']);
	$this->_checkdir->Close();
      }
    }
  }

  public function Close() {
    $this->_report->View;
    $this->__destruct();
  }

  public function __destruct() {
    
  }

}

?>
