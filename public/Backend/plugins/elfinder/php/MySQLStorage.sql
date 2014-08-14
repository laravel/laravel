DROP TABLE IF EXISTS `elfinder_file`;
CREATE TABLE IF NOT EXISTS `elfinder_file` (
  `id`        int(7) unsigned NOT NULL auto_increment,
  `parent_id` int(7) unsigned NOT NULL,
  `name`      varchar(256) NOT NULL,
  `content`   longblob NOT NULL,
  `size`      int(10) unsigned NOT NULL default '0',
  `mtime`     int(10) unsigned NOT NULL,
  `mime`      varchar(256) NOT NULL default 'unknown',
  `read`      enum('1', '0') NOT NULL default '1',
  `write`     enum('1', '0') NOT NULL default '1',
  `locked`    enum('1', '0') NOT NULL default '0',
  `hidden`    enum('1', '0') NOT NULL default '0',
  `width`     int(5) NOT NULL,
  `height`    int(5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY  `parent_name` (`parent_id`, `name`),
  KEY         `parent_id`   (`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `elfinder_file`
(`id`, `parent_id`, `name`,     `content`, `size`, `mtime`, `mime`,      `read`, `write`, `locked`, `hidden`, `width`, `height`) VALUES 
('1',  '0',         'DATABASE', '',        '0',    '0',     'directory', '1',    '1',     '0',      '0',      '0',     '0');
