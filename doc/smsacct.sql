CREATE TABLE `notifications` (
      `id` int(11) NOT NULL auto_increment,
      `date` timestamp NOT NULL default '0000-00-00 00:00:00',
      `service` int(11) default NULL,
      `phone` varchar(30) default NULL,
      `type` int(11) default NULL,
      `message` varchar(255) default NULL,
      `status` int(11) default NULL,
      PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=greek


CREATE TABLE `requests` (
      `id` int(11) NOT NULL auto_increment,
      `date` timestamp NOT NULL default '0000-00-00 00:00:00',
      `service` int(11) default NULL,
      `phone` varchar(30) default NULL,
      `smsc` varchar(30) default NULL,
      `req_type` int(11) default NULL,
      `req_msg` varchar(255) default NULL,
      `resp_msg` varchar(255) default NULL,
      `resp_status` int(11) default NULL,
      PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=greek

