CREATE TABLE IF NOT EXISTS `carts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(50) NOT NULL DEFAULT '',
  `oid` int(11) NOT NULL,
  `hashkey` VARCHAR(100) NOT NULL,
  `cart`  TEXT  NOT  NULL,
  `Created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY  KEY   (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;