
--
-- Change default table CHARACTER to utf8mb4 and COLLATE to utf8mb4_unicode_ci;
--

SET FOREIGN_KEY_CHECKS = 0;
-- #__tj_notification_providers columns
ALTER TABLE `#__tj_notification_providers` CHANGE `provider` `provider` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- #__tj_notification_user_exclusions columns
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `client` `client` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `key` `key` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_user_exclusions` CHANGE `provider` `provider` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- #__tj_notification_templates columns
ALTER TABLE `#__tj_notification_templates` CHANGE `client` `client` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `key` `key` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `title` `title` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `email_body` `email_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `sms_body` `sms_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `push_body` `push_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `web_body` `web_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `email_subject` `email_subject` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `sms_subject` `sms_subject` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `push_subject` `push_subject` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `web_subject` `web_subject` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `#__tj_notification_templates` CHANGE `replacement_tags` `replacement_tags` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;


