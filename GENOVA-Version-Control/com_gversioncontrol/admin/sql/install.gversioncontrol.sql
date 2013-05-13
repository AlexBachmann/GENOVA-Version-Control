DROP TABLE IF EXISTS `#__gvc_revisions`;

CREATE TABLE `#__gvc_revisions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL COMMENT 'The previous revision of this item, that this revision follows to. 0 if the item has just been created.',
  `context` varchar(45) NOT NULL COMMENT 'the context of this item. Items of different object types have different contexts. e.g. Joomla Articles have the context \\''com_content.articles\\''',
  `item_id` varchar(45) NOT NULL COMMENT 'The id of the item stored.',
  `time` datetime NOT NULL COMMENT 'The exact time, this revision was made.',
  `user` int(11) NOT NULL COMMENT 'The user that made the revision',
  `payload` blob NOT NULL COMMENT 'The payload object containing all the data that is to be stored in this revision.',
  `comment` tinyblob,
  `length` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `payload_hash` varchar(40) NOT NULL COMMENT 'This hash is used to check, if there are differences between two versions. Generating the hash should therefore only include payload-fields that make up the content of the versions to be compared.',
  PRIMARY KEY (`id`),
  KEY `rev_context_item_time` (`context`,`item_id`,`time`),
  KEY `rev_context_item_id` (`context`,`item_id`,`id`),
  KEY `rev_user_time` (`user`,`time`),
  KEY `rev_time` (`time`),
  KEY `rev_hash_id` (`payload_hash`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;


