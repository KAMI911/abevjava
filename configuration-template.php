<?php
/**
 * AbavJava configuration file template
 *
 * PHP version 5
 *
 * @category ConfigurationTemplate
 * @package  Abevjavaconfigurationtemplate
 * @author   Kálmán „KAMI” Szalai <kami911@gmail.com>
 * @license  http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 * @version  3.0.6
 * @link     http://ooo.itc.hu/
 */
define('DB_HOST',                'localhost');
define('DB_PORT',                '3306');
define('DB_USERNAME',            'abevjavabuilder');
define('DB_PASSWORD',            'pass');
define('DB_DATABASE',            'abevjavabuilder');
define('XML_PATH',               dirname(__FILE__) . '/xml/');
define('DOWNLOAD_PATH',          dirname(__FILE__) . '/download/');
define('OUTPUT_PATH',            dirname(__FILE__) . '/output/');
define('ADDITIONAL_PATH',        dirname(__FILE__) . '/additional/');
define('TEMP_PATH',              '/tmp/abevjavabuilder/');
define('FORCE_DOWNLOAD',         false);
define('FORCE_XML_DOWNLOAD',     false);
define('MAINTAINER_NAME',        'Package maintainer');
define('MAINTAINER_EMAIL',       'email@package_maintainer.com');
define('BUFFERAPP_ENABLED',      true);
define('BUFFERAPP_EMAIL',        'something-d7437939fhdd8@to.bufferapp.com');

?>

