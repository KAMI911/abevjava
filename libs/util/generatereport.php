<?php

/**
 * GenerateReport class
 *
 * PHP version 5
 *
 * @category Library
 * @package  GenerateReport
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.6
 * @link     http://ooo.itc.hu/
 */
class GenerateReport {

  private $_statics = array(
      'new' => '0',
      'updated' => '0',
      'rereleased' => '0'
  );
  private $_forms = array(
      'new' => array(),
      'updated' => array(),
      'rereleased' => array()
  );

  public function __construct() {
    
  }

  private function Increase($type, $form) {
    $this->$_statics[$type] = $this->$_statics[$type] + 1;
    $this->$_forms[$type][] = $form;
  }

  public function IncreaseNew($short_name, $version, $description, $company_name) {
    $this->Increase('new', array($short_name, $version, $description, $company_name));
  }

  public function IncreaseUpdated($short_name, $version, $description, $company_name) {
    $this->Increase('updated', array($short_name, $version, $description, $company_name));
  }

  public function IncreaseRereleased($short_name, $version, $description, $company_name) {
    $this->Increase('rereleased', array($short_name, $version, $description, $company_name));
  }

  public function View() {
    print_r($this->$_statics);
    print_r($this->$_forms);
  }

  public function Close() {
    $this->__destruct();
  }

  public function __destruct() {
    
  }

}

?>
