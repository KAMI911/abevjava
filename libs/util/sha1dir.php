<?php

/**
 * SHA1 class
 *
 * PHP version 5
 *
 * @category Library
 * @package  Sha1
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */
class SHA1dir {

  private $_SHA1directory;
  private $_SHA1file;
  private $_oldSHA1;
  private $_newSHA1;

  /**
   * Compare SHA1 sum of dir with the last one (stored in file)
   *
   * @param string $SHA1directory   - The directory path with filename mask.
   *        string $SHA1file        - Where to store the SHA1 sum
   * @return boolean                - FALSE if the SHA1 sums are different, TRUE if they are same
   */
  public function __construct($SHA1directory, $SHA1file) {
    $this->_SHA1directory = $SHA1directory;
    $this->_SHA1file = $SHA1file;
  }

  public function Check() {
    if (is_file($this->_SHA1file)) {
      $this->_oldSHA1 = file_get_contents($this->_SHA1file, NULL, NULL, NULL, 40);
    } else {
      $this->_oldSHA1 = '';
    }
    $this->_newSHA1 = File::SHA1sumDirectory($this->_SHA1directory);
    if ($this->_oldSHA1 != $this->_newSHA1) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function Save() {
    if ($this->_oldSHA1 != $this->_newSHA1) {
      file_put_contents($this->_SHA1file, $this->_newSHA1);
    }
  }

  public function Close() {
    $this->Save();
    $this->__destruct();
  }

  public function __destruct() {
    
  }

}
?>

