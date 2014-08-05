<?php

$link = Knot::getDatabase();

$link->query("CREATE TABLE IF NOT EXISTS `objects` (
  `ObjectId` int UNSIGNED NOT NULL,
  `Type` varchar(255) NOT NULL,
  PRIMARY KEY (`ObjectId`)
) ENGINE=InnoDB");

$link->query("CREATE TABLE IF NOT EXISTS `pages` (
  `PageId` int UNSIGNED NOT NULL,
  `ParentId` int UNSIGNED NOT NULL,
  `Date` datetime NOT NULL,
  `Slug` varchar(255) NOT NULL,
  `Order` int UNSIGNED NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` text NOT NULL,
  PRIMARY KEY (`PageId`)
) ENGINE=InnoDB");

$link->query("INSERT IGNORE INTO `objects` (
  `ObjectId`, `Type`
) VALUES (
  1, 'Page'
)");

$link->query("INSERT IGNORE INTO `pages` (
  `PageId`, `ParentId`, `Date`, `Slug`, `Order`, `Title`, `Content`
) VALUES (
  1, 0, FROM_UNIXTIME(0), '(index)', 0, 'Index', '<p>It works!</p>'
)");

?>
