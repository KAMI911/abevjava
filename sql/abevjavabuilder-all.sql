SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `abevjavabuilder`
--

CREATE DATABASE `abevjavabuilder` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `caid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Category identifier',
  `category_name` varchar(32) COLLATE utf8_general_ci NOT NULL COMMENT 'Name of Category',
  PRIMARY KEY (`caid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`caid`, `category_name`) VALUES
(1, 'framework'),
(2, 'template'),
(3, 'help'),
(4, 'orgresource');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `coid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Company identifier',
  `state` enum('enable','disable') COLLATE utf8_general_ci NOT NULL DEFAULT 'enable',
  `company_name` varchar(128) COLLATE utf8_general_ci NOT NULL COMMENT 'Name of Company',
  `company_short_name` varchar(32) COLLATE utf8_general_ci NOT NULL COMMENT 'Short name of Company',
  `url` varchar(128) COLLATE utf8_general_ci NOT NULL COMMENT 'Web URL of Company',
  PRIMARY KEY (`coid`),
  KEY `idx_company_short_name` (`company_short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

INSERT INTO `company` (`coid`, `state`, `company_name`, `company_short_name`, `url`) VALUES
(1, 'disable','KAMI',                                                                            'kami',      'http://ooop.itc.hu/'),
(2, 'disable','Nemzeti Adó- és Vámhivatal',                                                      'apeh',      'http://www.nav.gov.hu/abev/abev_new'),
(3, 'disable','Vám- és Pénzügyőrség',                                                            'vpop',      'http://vam.gov.hu/ado/automata/update.xml'),
(4, 'disable', 'Közigazgatási és Igazságügyi Minisztérium',                                      'ceginfo',   'http://e-beszamolo.kim.gov.hu/download/urlapok/verzio.xml'),
(5, 'disable','Pénzügyi Szervezetek Állami Felügyelete',                                         'pszaf',     'http://apps.pszaf.hu/nyomtatvanyok/verziok.xml'),
(6, 'disable','Országos Rendőr-főkapitányság ',                                                  'orfk',      'http://www.police.hu/abev/ORFK_nyomt.xml'),
(7, 'disable','Környezetvédelmi és Vízügyi Minisztérium',                                        'kvvm',      'http://support.kvvm.hu/kvvm_nykinfo.xml'),
(8, 'enable', 'Nemzeti Adó- és Vámhivatal',                                                      'nav',       'http://www.nav.gov.hu/abev/abev_new'),
(9, 'enable', 'Gyomaendrőd Önkormányzat Polgármesteri Hivatala',                                 'gyeonkphiv','http://www.gyomaendrod.hu/onkormanyzati_rendeletek/ugyintezes/verzio.xml'),
(10,'enable', 'Kaposvár Megyei Jogú Város Polgármesteri Hivatal',                                'sbaty',     'http://w3.kaposvar.hu/adobevallas/verzio.xml'),
(11,'enable', 'Kadarkút Város Polgármesteri Hivatala',                                           'kkut',      'http://www.kadarkutph.hu/eugyint/KKUTverzio.xml'),
(12,'enable', 'Országos Atomenergia Hivatal',                                                    'oah',       'http://www.haea.gov.hu/nyomtatvany/verziok.xml'),
(13,'disable','Budapest Főváros XXIII. kerület Soroksár Önkormányzatának Polgármesteri Hivatala','bpxxiii',   'http://www.ph.soroksar.hu/urlapok/verzio.xml'),
(14,'enable', 'Komárom Önkormányzat Polgármesteri Hivatala',                                     'komarom',   'http://www.komarom.hu/anyk/nyomtatvany_verzio.xml'),
(15,'disable','Magyar Posta Zártkörűen Működő Részvénytársaság',                                 'mpzrt',     'http://posta.hu/ugyfelszolgalat/nyomtatvanyok_urlapok/elektronikus_urlap_adatfrissites/MPZRTurlapfrissites.xml'),
(16,'enable', 'Polgármesteri Hivatal Martfű',                                                    'pmhmartfu', 'http://www.martfu.hu/eugyintezes/update.xml'),
(17,'enable', 'Miniszterelnöki Hivatal',                                                         'krtar',     'http://nevjegyzek.magyarorszag.hu/resources/verzio.xml'),
(18,'enable', 'Mórahalom Önkormányzat Polgármesteri Hivatala',                                   'morahalom', 'http://web.morahalom.hu/anyk/verzio.xml'),
(19,'disable','Közigazgatási és Elektronikus Közszolgálatások Központi Hivatala',                'kekkh',     'http://www.nyilvantarto.hu/'),
(20,'enable', 'Országos Bírósági Hivatal',                                                       'obhgepi',   'http://csc.birosag.hu/ANYKpub/anykverziok.xml'),
(21,'enable', 'Füzesabony Önkormányzat Polgármesteri Hivatala',                                  'fabonyph',  'http://www.fuzesabony.hu/anyk/verzio.xml'),
(22,'enable', 'Országos Egészségbiztosítási Pénztár',                                            'oep',       'http://hkp.oep.hu/nyomtatvanyok'),
(23,'enable', 'Mezőgazdasági Szakigazgatási Hivatal',                                            'mgszh',     'http://www.nebih.gov.hu/data/cms/130/239/abev.xml'),
(24,'enable', 'Magyar Államkincstár',                                                            'mak',       'http://www.allamkincstar.gov.hu/letoltesek/4578'),
(25,'enable', 'Magyar Nemzeti Bank',                                                             'mnb',       'https://apps.mnb.hu/nyomtatvanyok/verziok.xml'),
(26,'enable', 'Magyar Energetikai és Közmű-szabályozási Hivatal',                                'mekh',      'http://www.mekh.hu/gcpdocs/szarm_gar/update.xml'),
(27,'enable', 'Hódmezovásárhely Önkormányzat Polgármesteri Hivatala',                            'hmvpmh',    'http://lgtnet.dyndns.hu/Letoltes/Verzio.xmls'),
(28,'enable', 'Országos Nyugdíjbiztosítási Főigazgatóság',                                       'onyfebpenz','http://egbiztpenzbeli.tcs.allamkincstar.gov.hu/images/epel/nyomtatvanyok/update.xml');


-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE IF NOT EXISTS `package` (
  `paid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Package identifier',
  `state` enum('enable','disable') COLLATE utf8_general_ci NOT NULL DEFAULT 'enable',
  `short_name` varchar(64) COLLATE utf8_general_ci NOT NULL COMMENT 'Short name of package',
  `company_id` int unsigned NOT NULL COMMENT 'Company identifier',
  `category_id` int unsigned NOT NULL COMMENT 'Package category idetntifier',
  `description` mediumtext COLLATE utf8_general_ci COMMENT 'Package description',
  PRIMARY KEY (`paid`),
  KEY `idx_short_name` (`short_name`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

INSERT INTO `package` (`paid`, `short_name`, `company_id`, `category_id`, `description`) VALUES
(1, 'abevjava-resource', '1', '2147483647', 'Erőforrásfájl-gyűjtemény az Általános Nyomtatványkitöltő (ÁNYK - AbevJava) programhoz.');
-- --------------------------------------------------------

--
-- Table structure for table `download`
--

CREATE TABLE IF NOT EXISTS `download` (
  `doid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Download identifier',
  `company_id` int unsigned NOT NULL COMMENT 'Company identifier',
  `package_id` int unsigned NOT NULL COMMENT 'Package idetntifier',
  `category_id` int unsigned NOT NULL COMMENT 'Package category idetntifier',
  `version_major` smallint unsigned NOT NULL COMMENT 'Major version number of major.minor.micro.build-packing',
  `version_minor` smallint unsigned NOT NULL COMMENT 'Minor version number of major.minor.micro.build-packing',
  `version_micro` smallint unsigned NOT NULL COMMENT 'Micro version number of major.minor.micro.build-packing',
  `version_build` smallint unsigned NOT NULL COMMENT 'Build number of major.minor.micro.build-packing',
  `url_1` varchar(256) COLLATE utf8_general_ci NOT NULL COMMENT 'Download URL 1',
  `filename_1` varchar(256) COLLATE utf8_general_ci NOT NULL COMMENT 'Download Filename 1',
  `url_2` varchar(256) COLLATE utf8_general_ci NULL COMMENT 'Download URL 2',
  `filename_2` varchar(256) COLLATE utf8_general_ci NULL COMMENT 'Download Filename 2',
  `url_3` varchar(256) COLLATE utf8_general_ci NULL COMMENT 'Download URL 3',
  `filename_3` varchar(256) COLLATE utf8_general_ci NULL COMMENT 'Download Filename 3',
  PRIMARY KEY (`doid`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_package_id` (`package_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_version` (`version_major`, `version_minor`, `version_micro`),
  KEY `idx_versionbuild` (`version_major`, `version_minor`, `version_micro`,`version_build`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
INSERT INTO `download` (`doid`, `company_id`, `package_id`, `category_id`, `version_major`, `version_minor`, `version_micro`, `version_build`, `url_1`, `filename_1`, `url_2`, `filename_2`, `url_3`, `filename_3`) VALUES
(NULL, '1', '1', '2147483647', '1', '0', '0', '0', 'http://ooop.itc.hu/', 'abevjava_resource.jar', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `package_release`
--

CREATE TABLE IF NOT EXISTS `package_release` (
  `reid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Release identifier',
  `company_id` int unsigned NOT NULL COMMENT 'Company identifier -> coid',
  `package_id` int unsigned NOT NULL COMMENT 'Package idetntifier -> paid',
  `category_id` int unsigned NOT NULL COMMENT 'Package category idetntifier -> caid',
  `download_id` int unsigned NOT NULL COMMENT 'Download idetntifier -> doid',
  `version_major` smallint unsigned NOT NULL COMMENT 'Major version number of major.minor.micro.build-packing',
  `version_minor` smallint unsigned NOT NULL COMMENT 'Minor version number of major.minor.micro.build-packing',
  `version_micro` smallint unsigned NOT NULL COMMENT 'Micro version number of major.minor.micro.build-packing',
  `version_build` smallint unsigned NOT NULL COMMENT 'Build number of major.minor.micro.build-packing',
  `version_packing` smallint unsigned NOT NULL COMMENT 'Package release number of major.minor.micro.build-packing',

  PRIMARY KEY (`reid`),
  KEY `idx_company_id` (`company_id`),
  KEY `idx_package_id` (`package_id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_version` (`version_major`, `version_minor`, `version_micro`),
  KEY `idx_versionbuild` (`version_major`, `version_minor`, `version_micro`,`version_build`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `taid` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'Task identifier',
  `short_name` varchar(64) COLLATE utf8_general_ci NOT NULL COMMENT 'Short name of package',
  `company_id` int unsigned NOT NULL COMMENT 'Company identifier',
  `category_id` int unsigned NOT NULL COMMENT 'Package category idetntifier',
  `processed` enum('true','false') COLLATE utf8_general_ci NOT NULL DEFAULT 'false',

  PRIMARY KEY (`taid`),
  KEY `idx_search` (`short_name`, `company_id`, `category_id`),
  KEY `idx_short_name` (`short_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
