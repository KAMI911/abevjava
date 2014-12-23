<?php

/**
 * Temaplate class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Template
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */
class Template {

  private $template_dir = 'template/';
  private $file_ext = '.tpl';
  private $buffer;
  private $output;

  public function __construct($file) {
    if (file_exists($this->template_dir . $file . $this->file_ext)) {
      $this->buffer = file_get_contents($this->template_dir . $file . $this->file_ext);
    } else {
      echo $this->template_dir . $file . $this->file_ext . ' does not exist';
    }
  }

  public function ParseVariables($FindArray, $ReplaceArray) {

    $this->output = str_replace($FindArray, $ReplaceArray, $this->buffer);
  }

  public function RenderToConsole() {
    echo $this->output;
  }

  public function RenderToFile($OutputFile) {
    //File::mkdir_recursive(trim(dirname($OutputFile)), 770);
    system('mkdir -p ' . trim(dirname($OutputFile)), $output);
    // Write the contents to the file
    file_put_contents($OutputFile, $this->output);
  }

  public function __destruct() {
    
  }

}
?>

