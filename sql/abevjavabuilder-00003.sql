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
(25,'enable', 'Magyar Nemzeti Bank',                                                             'mnb',       'https://apps.mnb.hu/nyomtatvanyok/verziok.xml'),
(26,'enable', 'Magyar Energetikai és Közmű-szabályozási Hivatal',                                'mekh',      'http://www.mekh.hu/gcpdocs/szarm_gar/update.xml');
	
UPDATE `company` SET `state`='disable' where `company_short_name`='vpop';
UPDATE `company` SET `state`='disable' where `company_short_name`='pszaf';
UPDATE `company` SET `state`='disable' where `company_short_name`='orfk';
UPDATE `company` SET `state`='disable' where `company_short_name`='kvvm';

UPDATE `package` SET `state`='disable' where `short_name`='ceginfo-14eb-03';
