-- Change charset, collation for #__tj_notification_template_configs
ALTER TABLE `#__tj_notification_template_configs` ADD `language` CHAR(7) NOT NULL DEFAULT '*' AFTER `provider`;
