-- Delete column
ALTER TABLE `#__tj_notification_template_configs` DROP `replacement_tags`;
-- Add column
ALTER TABLE `#__tj_notification_templates` ADD `replacement_tags` TEXT NOT NULL AFTER `core`;
