SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `abevjavabuilder`
--


INSERT INTO `company` (`coid`, `state`, `company_name`, `company_short_name`, `url`) VALUES
(20,	'enable',	'Országos Bírósági Hivatal',														'obhgepi',		'http://csc.birosag.hu/ANYKpub/anykverziok.xml'),
(21,	'enable',	'Füzesabony Önkormányzat Polgármesteri Hivatala',									'fabonyph',		'http://www.fuzesabony.hu/anyk/verzio.xml'),
(22,	'enable',	'Országos Egészségbiztosítási Pénztár',												'oep',			'http://hkp.oep.hu/nyomtatvanyok'),
(23,	'enable',	'Mezőgazdasági Szakigazgatási Hivatal',												'mgszh',		'http://www.nebih.gov.hu/data/cms/130/239/abev.xml'),
(24,	'enable',	'Magyar Államkincstár',																'mak',		'http://www.allamkincstar.gov.hu/letoltesek/4578');

UPDATE `company` SET `state`='enable' where `company_short_name`='kvvm';

ALTER TABLE `package`
ADD COLUMN `state` enum('enable','disable') COLLATE utf8_general_ci NOT NULL DEFAULT 'enable'
AFTER `paid`;


