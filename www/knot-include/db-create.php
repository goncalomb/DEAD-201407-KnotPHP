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
  `Content` text NOT NULL
) ENGINE=InnoDB");

?>
