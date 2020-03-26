<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

/**
 * Migration file for templates
 *
 * @since  3.2.0
 */
class TjHouseKeepingTemplateData extends TjModelHouseKeeping
{
	public $title       = "Migrate Template Data";

	public $description = "Migrate the Template entries in config table.s";

	/**
	 * This function migrate templates
	 *
	 * @return  mixed    Array on success, false on failure.
	 *
	 * @since   3.2.0
	 */
	public function migrate()
	{
		$limit  = 200;
		$result = array();
		$db     = Factory::getDbo();

		try
		{
			// Get first 50 rows from last processed id
			$query = $db->getQuery(true)
				->select('*')
				->from('#__tj_notification_templates')
				->order($db->quoteName('id') . ' ASC');
			$db->setQuery($query, 0, $limit);
			$rows = $db->loadObjectList();

			foreach ($rows as $row)
			{
				$db    = Factory::getDBO();

				if (empty($row->id))
				{
					return false;
				}

				Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
				$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));

				$templateConfigTable->load(array('template_id' => $row->id));

				$templateConfigTable->template_id = $row->id;
				$templateConfigTable->provider = "email";
				$templateConfigTable->subject = $row->email_subject;
				$templateConfigTable->body = $row->email_body;

				if (!empty($row->replacement_tags))
				{
					$templateConfigTable->replacement_tags = $row->replacement_tags;
				}

				$templateConfigTable->state = $row->email_status;
				$templateConfigTable->created_on = $row->created_on;
				$templateConfigTable->updated_on = $row->updated_on;
				$templateConfigTable->is_override = $row->is_override;

				// If failed to update return error
				if (!$templateConfigTable->save($templateConfigTable))
				{
					$result['err_code'] = '';
					$result['status']   = false;

					return;
				}
			}

			$result['status']   = true;
			$result['message']  = "Migration successful";

			if ($result['status'])
			{
				$query = "ALTER TABLE `#__tj_notification_templates` DROP `email_status`, DROP `sms_status`, DROP `push_status`, DROP `web_status`,  
				DROP `email_body`, DROP `sms_body`, DROP `push_body`, DROP `web_body`, DROP `email_subject`, DROP `sms_subject`, DROP `push_subject`, 
				DROP `web_subject`, DROP `is_override`, DROP `replacement_tags`";
				$db->setQuery($query);
				$db->execute();
			}
		}
		catch (Exception $e)
		{
			$result['err_code'] = '';
			$result['status']   = false;
			$result['message']  = $e->getMessage();
		}

		return $result;
	}
}
