ALTER TABLE `#__tj_notification_template_configs` ADD COLUMN `webhook_url` text DEFAULT NULL AFTER `body`;
ALTER TABLE `#__tj_notification_template_configs` ADD COLUMN `use_global_webhook_url` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Use Global Config Webhook URLs' AFTER `webhook_url`;
ALTER TABLE `#__tj_notification_logs` ADD COLUMN `webhook_url` TEXT NULL DEFAULT NULL AFTER `body`;
