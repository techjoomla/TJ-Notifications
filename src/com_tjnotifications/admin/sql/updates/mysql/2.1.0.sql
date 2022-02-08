ALTER TABLE `#__tj_notification_template_configs` ADD COLUMN `webhook_url` text DEFAULT NULL AFTER `body`;
ALTER TABLE `#__tj_notification_logs` ADD COLUMN `webhook_url` TEXT NULL DEFAULT NULL AFTER `body`;
