CREATE TABLE IF NOT EXISTS `oc_feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `sort_order` int(3) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`feedback_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Table structure for table `oc_feedback_description`
--

CREATE TABLE IF NOT EXISTS `oc_feedback_description` (
  `feedback_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `feedback_author` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`feedback_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `oc_feedback_to_layout`
--

CREATE TABLE IF NOT EXISTS `oc_feedback_to_layout` (
  `feedback_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  PRIMARY KEY (`feedback_id`,`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `oc_feedback_to_store`
--

CREATE TABLE IF NOT EXISTS `oc_feedback_to_store` (
  `feedback_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  PRIMARY KEY (`feedback_id`,`store_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;