-- Fix datetime columns to allow NULL values
-- This fixes the "Incorrect datetime value" error

ALTER TABLE `#__tj_notification_template_configs` 
MODIFY `created_on` datetime NULL DEFAULT NULL,
MODIFY `updated_on` datetime NULL DEFAULT NULL;

-- Also ensure the main templates table has proper datetime handling
ALTER TABLE `#__tj_notification_templates` 
MODIFY `created_on` datetime NULL DEFAULT NULL,
MODIFY `updated_on` datetime NULL DEFAULT NULL;
