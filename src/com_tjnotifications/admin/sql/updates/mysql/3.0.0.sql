-- Adding default values to the column of existing table

ALTER TABLE `#__tj_notification_providers` CHANGE `provider` `provider` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_providers` CHANGE `state` `state` int(1) NOT NULL DEFAULT 0;

ALTER TABLE `#__tj_notification_templates` CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_templates` CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_templates` CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_templates` CHANGE `state` `state` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_templates` CHANGE `created_on` `created_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `updated_on` `updated_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `user_control` `user_control` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_templates` CHANGE `core` `core` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_templates` CHANGE `replacement_tags` `replacement_tags` text DEFAULT NULL;

ALTER TABLE `#__tj_notification_template_configs` CHANGE `template_id` `template_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_template_configs` CHANGE `subject` `subject` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `body` `body` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `params` `params` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `created_on` `created_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `updated_on` `updated_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `state` `state` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `is_override` `is_override` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_template_configs` CHANGE `provider_template_id` `provider_template_id` varchar(255) DEFAULT '';

ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `user_id` `user_id` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `provider` `provider` varchar(100) NOT NULL DEFAULT '';

ALTER TABLE `#__tj_notification_logs` CHANGE `key` `key` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `client` `client` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `subject` `subject` varchar(250) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `title` `title` varchar(100) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `body` `body` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `from` `from` varchar(500) NOT NULL DEFAULT '';
ALTER TABLE `#__tj_notification_logs` CHANGE `to` `to` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `cc` `cc` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `bcc` `bcc` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `date` `date` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `state` `state` tinyint(2) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_logs` CHANGE `params` `params` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `priority` `priority` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tj_notification_logs` CHANGE `message` `message` text DEFAULT NULL;
ALTER TABLE `#__tj_notification_logs` CHANGE `category` `category` text DEFAULT NULL;

ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `title` `title` varchar(255) DEFAULT NULL ;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `backend` `backend` varchar(50) NOT NULL DEFAULT '';
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `state` `state` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `is_confirmed` `is_confirmed` tinyint(1) NOT NULL DEFAULT 0;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `created_by` `created_by` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `modified_by` `modified_by` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `created_on` `created_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `updated_on` `updated_on` datetime NULL DEFAULT NULL;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `checked_out` `checked_out` int(11) NOT NULL DEFAULT 0;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `params` `params` text DEFAULT NULL;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `address` `address` text DEFAULT NULL;
ALTER TABLE `#__tjnotifications_subscriptions` CHANGE `device_id` `device_id` text DEFAULT NULL;
