CREATE TABLE IF NOT EXISTS `{#prefix#}categories` (
`ID` int(10) unsigned NOT NULL auto_increment,
`Name` varchar(255) collate utf8_unicode_ci NOT NULL DEFAULT '',
`Link` varchar(255) collate utf8_unicode_ci NOT NULL DEFAULT '',
`OrderIndex` tinyint(4) NOT NULL,
`Parent` tinyint(4) NOT NULL,
PRIMARY KEY  (`ID`),
KEY `OrderIndex` (`OrderIndex`),
KEY `Parent` (`Parent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `{#prefix#}jobs` (
`ID` int(10) unsigned NOT NULL auto_increment,
`CategoryID` int(11) NOT NULL default '0',
`Title` varchar(200) collate utf8_unicode_ci NOT NULL,
`Description` varchar(4000) collate utf8_unicode_ci NOT NULL,
`ToApply` varchar(250) collate utf8_unicode_ci NOT NULL,
`Company` varchar(120) collate utf8_unicode_ci NOT NULL,
`Location` varchar(120) collate utf8_unicode_ci NOT NULL,
`Public` tinyint(1) NOT NULL default '0',
`ExpirationDate` int unsigned NOT NULL,
`ChangedBy` varchar(100) collate utf8_unicode_ci NOT NULL,
`ChangedDate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
`STATUS` tinyint(1) unsigned NOT NULL default '0',
`CodeStamp` timestamp NOT NULL default '0000-00-00 00:00:00',
`PostedAt` datetime NOT NULL,
PRIMARY KEY  (`ID`),
KEY `CategoryID` (`CategoryID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `{#prefix#}plugin_data` (
`id` int(10) unsigned NOT NULL auto_increment,
`plugin_name` varchar(255) collate utf8_unicode_ci NOT NULL,
`option_name` varchar(255) collate utf8_unicode_ci NOT NULL,
`option_value` varchar(4096) collate utf8_unicode_ci NOT NULL,
PRIMARY KEY  (`id`),
KEY `option_name` (`option_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `{#prefix#}users` (
`ID` int(10) unsigned NOT NULL auto_increment,
`username` varchar(45) collate utf8_unicode_ci NOT NULL,
`realname` varchar(100) collate utf8_unicode_ci NOT NULL,
`password` varchar(256) collate utf8_unicode_ci NOT NULL,
`password_salt` varchar(51) collate utf8_unicode_ci NOT NULL,
`email` varchar(320) collate utf8_unicode_ci NULL,
PRIMARY KEY  (`ID`),
UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
