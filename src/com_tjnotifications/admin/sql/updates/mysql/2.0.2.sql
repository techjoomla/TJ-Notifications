-- Adding default values to the column of existing table

ALTER TABLE `#__tj_notification_providers`
	CHANGE `provider` `provider` varchar(100) NOT NULL DEFAULT '',
	CHANGE `state` `state` int(1) NOT NULL DEFAULT '0';

ALTER TABLE `#__tj_notification_templates`
	CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '',
	CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '',
	CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '',
	CHANGE `state` `state` int(11) NOT NULL DEFAULT '0',
	CHANGE `user_control` `user_control` int(1) NOT NULL DEFAULT '0',
	CHANGE `core` `core` int(1) NOT NULL DEFAULT '0',
	CHANGE `replacement_tags` `replacement_tags` text NOT NULL DEFAULT '';

ALTER TABLE `#__tj_notification_template_configs`
	CHANGE `template_id` `template_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '',
	CHANGE `subject` `subject` text NOT NULL DEFAULT '',
	CHANGE `body` `body` text NOT NULL DEFAULT '',
	CHANGE `params` `params` text NOT NULL DEFAULT '',
	CHANGE `state` `state` int(11) NOT NULL DEFAULT '0',
	CHANGE `is_override` `is_override` int(1) NOT NULL DEFAULT '0',
	CHANGE `provider_template_id` `provider_template_id` varchar(255) DEFAULT '';

ALTER TABLE `#__tj_notification_user_exclusions`
	CHANGE `user_id` `user_id` int(11) NOT NULL DEFAULT '0',
	CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '',
	CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '',
	CHANGE `provider` `provider` varchar(100) NOT NULL DEFAULT '';

ALTER TABLE `#__tj_notification_logs`
	CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '',
	CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '',
	CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '',
	CHANGE `subject` `subject` varchar(250) NOT NULL DEFAULT '',
	CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '',
	CHANGE `body` `body` text NOT NULL DEFAULT '',
	CHANGE `from` `from` varchar(500) NOT NULL DEFAULT '',
	CHANGE `to` `to` text NOT NULL DEFAULT '',
	CHANGE `cc` `cc` text NOT NULL DEFAULT '',
	CHANGE `bcc` `bcc` text NOT NULL DEFAULT '',
	CHANGE `date` `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
	CHANGE `state` `state` tinyint(2) NOT NULL DEFAULT '0',
	CHANGE `params` `params` text NOT NULL DEFAULT '',
	CHANGE `priority` `priority`int(11) NOT NULL DEFAULT '0',
	CHANGE `message` `message` text NOT NULL DEFAULT '',
	CHANGE `category` `category` text NOT NULL DEFAULT '';

ALTER TABLE `#__tjnotifications_subscriptions`
	CHANGE `title` `title` varchar(255) DEFAULT NULL ,
	CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '',
	CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT '0',
	CHANGE `is_confirmed` `is_confirmed` tinyint(1) NOT NULL DEFAULT '0',
	CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT '0',
	CHANGE `modified_by` `modified_by` int(11) NOT NULL DEFAULT '0',
	CHANGE `checked_out` `checked_out` int(11) NOT NULL DEFAULT '0',
	CHANGE `params` `params` text DEFAULT NULL DEFAULT '';
