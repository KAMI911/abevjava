<?php

/**
 * Processing XML file and put data into database
 *
 * PHP version 5
 *
 * @category Program
 * @package  Abevjavaupgrade
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.5
 * @link     http://ooo.itc.hu/
 */
require_once dirname(__FILE__) . '/configuration.php';
require_once dirname(__FILE__) . '/libs/util/xmltodatabase.php';
require_once dirname(__FILE__) . '/libs/util/file.php';
require_once dirname(__FILE__) . '/libs/util/xml.php';
require_once dirname(__FILE__) . '/libs/util/database.php';
require_once dirname(__FILE__) . '/libs/util/template.php';
require_once dirname(__FILE__) . '/libs/util/packagegenerator.php';
require_once dirname(__FILE__) . '/libs/util/package.php';
require_once dirname(__FILE__) . '/libs/util/sha1dir.php';


try {
  /**
   * Parse the ABEVJAVA datasources and put them into the database
   *
   */
  $xml2db = new XMLtoDataBase(
	  DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_DATABASE
  );
  $xml2db->import();
  $xml2db->close();
} catch (Exception $error) {
  echo '[!] Problems occured during xml to database import phase.' . $error->getMessage() . PHP_EOL;
  exit(1);
}

try {
  /**
   * Read the database and genererate packages from it
   *
   */
  $xml2db = new PackageGenerator(
	  DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_DATABASE
  );
  $xml2db->generate();
  $xml2db->close();
} catch (Exception $error) {
  echo '[!] Problems occured during package generation phase.' . $error->getMessage() . PHP_EOL;
  exit(1);
}
system('rm -fr ' . TEMP_PATH, $output);
echo '[.] Finished, exiting, and go home.' . PHP_EOL;
?>

