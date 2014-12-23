<?php

/**
 * Downloading JAR files
 *
 * PHP version 5
 *
 * @category Program
 * @package  Downloadjar
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.4
 * @link     http://ooo.itc.hu/
 */
require_once dirname(__FILE__) . '/configuration.php';
require_once dirname(__FILE__) . '/libs/util/file.php';
require_once dirname(__FILE__) . '/libs/util/xmltodatabase.php';

$default['version_major'] = 1;
$default['version_minor'] = 0;
$default['version_micro'] = 0;
$default['version_build'] = 0;

$urls[0]['url'] = 'http://www.nyilvantarto.hu/hu/evig_megkezdes_bejelentes';
$urls[0]['company'] = 'kekkh';
$urls[0]['company_id'] = '10';
$urls[0]['category_id'] = 2;
$urls[0]['url_prefix'] = 'http://www.nyilvantarto.hu/';

$urls[1]['url'] = 'http://www.nyilvantarto.hu/hu/evig_adatvaltozas_bejelentes';
$urls[1]['company'] = 'kekkh';
$urls[1]['company_id'] = '12';
$urls[1]['category_id'] = 2;
$urls[1]['url_prefix'] = 'http://www.nyilvantarto.hu/';

$doc = new DOMDocument();

unset($requested_links);
$i = 0;

foreach ($urls as $index => $data) {
  $doc->loadHTMLFile($data['url']);
  $links = $doc->getElementsByTagName('a');

  foreach ($links as $link) {
    $mylink = $link->getAttribute('href');
    if (stristr($mylink, '.jar')) {
      $requested_link[$i]['url'] = $data['url_prefix'] . $mylink;
      $requested_link[$i]['description'] = File::CorrectDescription($link->nodeValue);
      $tmp_name = explode('.jar', basename($mylink));
      $requested_link[$i]['short_name'] = File::ShortnameNormalizer($tmp_name[0]);

      $requested_link[$i]['company'] = $data['company'];
      $requested_link[$i]['category_id'] = $data['category_id'];

      $requested_link[$i]['version_major'] = $default['version_major'];
      $requested_link[$i]['version_minor'] = $default['version_minor'];
      $requested_link[$i]['version_micro'] = $default['version_micro'];
      $requested_link[$i]['version_build'] = $default['version_build'];

      $i++;
    }
  }
}

try {
  /**
   * Parse the ABEVJAVA datasources and put them into the database
   *
   */
  $html2db = new XMLtoDataBase(DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
  foreach ($requested_link as $index => $record) {
    $html2db->AddRecord($record);
  }
  $html2db->ViewFormData();
  $html2db->put();
  $html2db->close();
} catch (Exception $error) {
  echo '[!] Problems occured during xml to database import phase.' . $error->getMessage() . PHP_EOL;
  exit(1);
}

print_r($requested_link);
?>
