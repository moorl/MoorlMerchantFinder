<?php declare(strict_types=1);

namespace Moorl\MerchantFinder\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1584635124Merchant extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1584635124;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `moorl_merchant`
ADD `cms_page_id` binary(16) NULL AFTER `product_manufacturer_id`;

CREATE TABLE IF NOT EXISTS `moorl_merchant_category` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `category_id` binary(16) NOT NULL,
  `category_version_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`category_id`,`category_version_id`),
  KEY `fk.moorl_merchant_category.category_id` (`category_id`,`category_version_id`),
  CONSTRAINT `fk.moorl_merchant_category.category_id` FOREIGN KEY (`category_id`, `category_version_id`) REFERENCES `category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `moorl_merchant_category_ibfk_3` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `moorl_merchant_product_manufacturer` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `product_manufacturer_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`product_manufacturer_id`),
  KEY `fk.moorl_merchant_product_manufacturer.product_manufacturer_id` (`product_manufacturer_id`),
  CONSTRAINT `fk.moorl_merchant_product_manufacturer.product_manufacturer_id` FOREIGN KEY (`product_manufacturer_id`) REFERENCES `product_manufacturer` (`id`),
  CONSTRAINT `moorl_merchant_product_manufacturer_ibfk_1` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `moorl_merchant_tag` (
  `moorl_merchant_id` binary(16) NOT NULL,
  `tag_id` binary(16) NOT NULL,
  PRIMARY KEY (`moorl_merchant_id`,`tag_id`),
  KEY `fk.moorl_merchant_tag.tag_id` (`tag_id`),
  CONSTRAINT `fk.moorl_merchant_tag.tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`),
  CONSTRAINT `moorl_merchant_tag_ibfk_1` FOREIGN KEY (`moorl_merchant_id`) REFERENCES `moorl_merchant` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);

        //$this->addDemoData($connection);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function addDemoData(Connection $connection): void
    {
        $sql = <<<SQL
INSERT INTO `moorl_merchant` (`id`, `sales_channel_id`, `country_id`, `customer_group_id`, `media_id`, `marker_id`, `marker_shadow_id`, `product_manufacturer_id`, `cms_page_id`, `origin_id`, `first_name`, `last_name`, `email`, `title`, `active`, `zipcode`, `city`, `company`, `street`, `street_number`, `country`, `country_code`, `location_lat`, `location_lon`, `department`, `vat_id`, `phone_number`, `data`, `additional_address_line1`, `additional_address_line2`, `shop_url`, `merchant_url`, `description`, `marker_settings`, `opening_hours`, `custom_fields`, `auto_increment`, `created_at`, `updated_at`) VALUES
(UNHEX('0715315D3C384087820286017645CEEF'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'132',	'Ronna',	'Cusiter',	'rcusiter3n@github.com',	NULL,	1,	'22179',	'Hamburg',	'Jabberstorm',	'706 Schmedeman Way',	NULL,	NULL,	'DE',	53.6079,	10.0853,	NULL,	'421-83-3462',	'919-795-5045',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:38.642',	NULL),
(UNHEX('0BA9BEE497E0409C8F2979AA178EB3C8'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'72',	'Immanuel',	'Stanyforth',	'istanyforth1z@oaic.gov.au',	NULL,	1,	'63456',	'Hanau',	'Brainsphere',	'09106 Fairfield Pass',	NULL,	NULL,	'DE',	50.1043,	8.91114,	NULL,	'484-69-6397',	'903-741-3747',	NULL,	NULL,	NULL,	'http://fda.gov',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:33.294',	NULL),
(UNHEX('2D28CED408D1477A87CCFE3C48FF6FDD'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'191',	'Bettine',	'Goadsby',	'bgoadsby5a@liveinternet.ru',	NULL,	1,	'79106',	'Freiburg im Breisgau',	'Jabbertype',	'821 North Court',	NULL,	NULL,	'DE',	48.0045,	7.83747,	NULL,	'731-96-2794',	'844-199-6298',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:43.983',	NULL),
(UNHEX('3827235FE6754C7A9BC8FBF64A5BB06F'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'127',	'Dewitt',	'Shimwell',	'dshimwell3i@pinterest.com',	NULL,	1,	'35687',	'Dillenburg',	'Thoughtworks',	'68144 Hanson Street',	NULL,	NULL,	'DE',	50.7218,	8.30738,	NULL,	'664-47-6059',	'610-206-3205',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:38.186',	NULL),
(UNHEX('42FBA72326E24C12B94941E511F3A7ED'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'151',	'Hazel',	'Blackborn',	'hblackborn46@dmoz.org',	NULL,	1,	'59071',	'Hamm',	'Thoughtbeat',	'691 Dovetail Way',	NULL,	NULL,	'DE',	51.6814,	7.90853,	NULL,	'796-15-3545',	'159-261-5242',	NULL,	NULL,	NULL,	'https://nasa.gov',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:40.371',	NULL),
(UNHEX('43CE2A75080640EBB45AE062672806D6'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'165',	'Tim',	'Weeden',	'tweeden4k@phoca.cz',	NULL,	1,	'01189',	'Dresden',	'Skipstorm',	'698 Holmberg Alley',	NULL,	NULL,	'DE',	51.0145,	13.6972,	NULL,	'537-70-6615',	'638-558-9146',	NULL,	NULL,	NULL,	'http://columbia.edu',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:41.605',	NULL),
(UNHEX('44D0AF9F55ED4153A51BF747528F5BA9'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'121',	'Webb',	'Bradford',	'wbradford3c@ibm.com',	NULL,	1,	'50674',	'Köln',	'Linkbridge',	'8 Grayhawk Center',	NULL,	NULL,	'DE',	50.9346,	6.93314,	NULL,	'693-34-3410',	'345-157-0058',	NULL,	NULL,	NULL,	'http://state.tx.us',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:37.648',	NULL),
(UNHEX('46C142A890CF4CAF8472D487AFA10E50'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'96',	'Alvan',	'Beminster',	'abeminster2n@cnbc.com',	NULL,	1,	'01156',	'Dresden',	'Thoughtworks',	'6448 Bobwhite Court',	NULL,	NULL,	'DE',	51.0855,	13.6277,	NULL,	'277-88-0166',	'545-291-1881',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:35.385',	NULL),
(UNHEX('4A382AA04A9546AC953473AF9A514CB7'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'3',	'Lynda',	'Bartlomieczak',	'lbartlomieczak2@cdbaby.com',	NULL,	1,	'63073',	'Offenbach',	'Shufflebeat',	'480 Walton Pass',	NULL,	NULL,	'DE',	50.0809,	8.81072,	NULL,	'861-71-1062',	'872-355-4233',	NULL,	NULL,	NULL,	'https://homestead.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:27.137',	NULL),
(UNHEX('50B445642D2143F4A9FFF9C07129B10A'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'14',	'Lindy',	'Seymer',	'lseymerd@pbs.org',	NULL,	1,	'90411',	'Nürnberg',	'Skipstorm',	'9289 Moulton Center',	NULL,	NULL,	'DE',	49.4905,	11.0987,	NULL,	'145-14-1750',	'370-545-5486',	NULL,	NULL,	NULL,	'http://apache.org',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:28.113',	NULL),
(UNHEX('55EA949F74B54E8AABAC0835804EFCCE'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'10',	'Amalea',	'Moxley',	'amoxley9@furl.net',	NULL,	1,	'99089',	'Erfurt',	'Babbleopia',	'0 Almo Plaza',	NULL,	NULL,	'DE',	50.9977,	11.0138,	NULL,	'163-95-3297',	'575-780-0897',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:27.765',	NULL),
(UNHEX('5AC75825A9E3465CA1E7825B6C4819A9'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'138',	'Curr',	'Falconer',	'cfalconer3t@hostgator.com',	NULL,	1,	'22111',	'Hamburg',	'Thoughtstorm',	'7 Clemons Hill',	NULL,	NULL,	'DE',	53.5484,	10.0782,	NULL,	'106-63-7600',	'583-882-0284',	NULL,	NULL,	NULL,	'https://indiegogo.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:39.171',	NULL),
(UNHEX('5E7140F1079849C4A40E3B36F2511792'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'95',	'Frederique',	'Sorsbie',	'fsorsbie2m@pinterest.com',	NULL,	1,	'39130',	'Magdeburg',	'Shufflester',	'96491 Springview Trail',	NULL,	NULL,	'DE',	52.1558,	11.5722,	NULL,	'374-30-2192',	'625-979-1740',	NULL,	NULL,	NULL,	'http://example.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:35.298',	NULL),
(UNHEX('604472BF51F0488693B3680777CB52EE'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'106',	'Yancy',	'MacPhail',	'ymacphail2x@reuters.com',	NULL,	1,	'90411',	'Nürnberg',	'Brainlounge',	'45715 Bay Way',	NULL,	NULL,	'DE',	49.4905,	11.0987,	NULL,	'456-09-5736',	'859-832-6963',	NULL,	NULL,	NULL,	'http://princeton.edu',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:36.258',	NULL),
(UNHEX('629C660AF9F041B4B9B68C6326FA1A0E'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'102',	'Beniamino',	'Hansom',	'bhansom2t@nytimes.com',	NULL,	1,	'12559',	'Berlin',	'Thoughtblab',	'473 Stang Parkway',	NULL,	NULL,	'DE',	52.4169,	13.6493,	NULL,	'131-36-9013',	'607-271-0679',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:35.911',	NULL),
(UNHEX('69836F35094D4900AC554DE0FCE23E32'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'184',	'Flor',	'Kluge',	'fkluge53@fda.gov',	NULL,	1,	'47139',	'Duisburg',	'Chatterbridge',	'00778 Brickson Park Pass',	NULL,	NULL,	'DE',	51.4758,	6.71124,	NULL,	'805-74-1279',	'100-829-4264',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:43.351',	NULL),
(UNHEX('6C984991D07947378D6E395D876DF610'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'62',	'Ozzy',	'Caplin',	'ocaplin1p@bbb.org',	NULL,	1,	'12169',	'Berlin',	'Chatterbridge',	'29 Forest Run Point',	NULL,	NULL,	'DE',	52.4555,	13.3398,	NULL,	'649-57-4883',	'766-889-0040',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:32.409',	NULL),
(UNHEX('9213D6397F214DF2AFCDB7B32E150E11'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'41',	'Worth',	'Titchard',	'wtitchard14@nasa.gov',	NULL,	1,	'04109',	'Leipzig',	'Jabbersphere',	'653 Maryland Way',	NULL,	NULL,	'DE',	51.3384,	12.3634,	NULL,	'485-34-9018',	'877-883-3558',	NULL,	NULL,	NULL,	'https://taobao.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:30.545',	NULL),
(UNHEX('99D32A1742C64266BFC01A9E19C1CB0B'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'23',	'Alec',	'Arnal',	'aarnalm@desdev.cn',	NULL,	1,	'12103',	'Berlin',	'Feednation',	'6158 Ohio Crossing',	NULL,	NULL,	'DE',	52.4639,	13.3729,	NULL,	'890-76-5604',	'999-131-8776',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:28.913',	NULL),
(UNHEX('A57CED0EEE214EBB9791B8BFD374072B'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'88',	'Debor',	'Cooksley',	'dcooksley2f@businessweek.com',	NULL,	1,	'23568',	'Lübeck',	'Chatterpoint',	'071 La Follette Alley',	NULL,	NULL,	'DE',	53.9031,	10.7534,	NULL,	'484-76-0328',	'645-524-5215',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:34.690',	NULL),
(UNHEX('A663D1E7B76C4315A6D88E0AE84D1241'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'190',	'Ophelie',	'Blackmore',	'oblackmore59@cafepress.com',	NULL,	1,	'34132',	'Kassel',	'Topicshots',	'5857 Garrison Avenue',	NULL,	NULL,	'DE',	51.2805,	9.42665,	NULL,	'639-09-6261',	'482-153-6165',	NULL,	NULL,	NULL,	'http://ftc.gov',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:43.891',	NULL),
(UNHEX('B3BD48ADBCB748E8A910F9155E0A8D05'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'116',	'Bonni',	'Langtree',	'blangtree37@loc.gov',	NULL,	1,	'13409',	'Berlin',	'Babbleblab',	'307 Macpherson Point',	NULL,	NULL,	'DE',	52.5693,	13.3715,	NULL,	'753-76-6151',	'971-890-6177',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:37.186',	NULL),
(UNHEX('CD44BCDE15CA4CB2AC4489597F72F5A5'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'35',	'Katharina',	'Blomefield',	'kblomefieldy@blog.com',	NULL,	1,	'20459',	'Hamburg Sankt Pauli',	'Linkbridge',	'75 Sachtjen Place',	NULL,	NULL,	'DE',	53.5473,	9.97783,	NULL,	'298-51-0648',	'342-577-0309',	NULL,	NULL,	NULL,	'https://nih.gov',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:30.014',	NULL),
(UNHEX('D6468D8C430541809768AB2FD09DAEDD'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'144',	'Nollie',	'Schorah',	'nschorah3z@fotki.com',	NULL,	1,	'22559',	'Hamburg',	'Realbridge',	'6 1st Terrace',	NULL,	NULL,	'DE',	53.5831,	9.75376,	NULL,	'221-93-8028',	'544-384-6594',	NULL,	NULL,	NULL,	'https://goodreads.com',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:39.751',	NULL),
(UNHEX('E038DD449DBB4AD6BDA51C12AAB2639B'),	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'76',	'Latia',	'Bushe',	'lbushe23@comsenz.com',	NULL,	1,	'28355',	'Bremen',	'Thoughtworks',	'3646 Maple Wood Center',	NULL,	NULL,	'DE',	53.0903,	8.9354,	NULL,	'719-40-9856',	'183-843-3636',	NULL,	NULL,	NULL,	'https://hud.gov',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'2020-03-20 00:40:33.640',	NULL);
SQL;
        $connection->executeStatement($sql);
    }
}
