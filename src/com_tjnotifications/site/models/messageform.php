<?php
/**
 * @package     Tjnotifications
 * @subpackage  tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Model for form for message
 *
 * @package  Tjnotifications
 *
 * @since    2.1.0
 */
class TjnotificationsModelMessageform extends BaseDatabaseModel
{
	/**
	 * Method to update notifications status columns for delievered / read
	 *
	 * @param   string     $statusType       Status delievered / read
	 * @param   int|array  $notificationPks  Notification id or array of ids
	 * @param   int        $status           Status 0 or 1
	 * @param   int        $userId           User id
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    2.1.0
	 */
	public function updateNotificationStatus($statusType, $notificationPks, $status = 1, $userId = 0)
	{
		if (empty($statusType) || empty($notificationPks))
		{
			return false;
		}

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Fields to update.
		if ($statusType == 'delivered')
		{
			$fields = array($db->quoteName('delivered') . ' = ' . (int) $status);
		}

		if ($statusType == 'read')
		{
			$fields = array($db->quoteName('read') . ' = ' . (int) $status);
		}

		// Conditions
		$whereConditions = array();

		if (is_array($notificationPks) && count($notificationPks))
		{
			$whereConditions[] = $db->quoteName('id') . ' IN (' . implode(', ', $notificationPks) . ')';
		}
		else
		{
			$whereConditions[] = $db->quoteName('id') . ' = ' . $notificationPks;
		}

		if ($userId)
		{
			$whereConditions[] = $db->quoteName('recepient') . ' = ' . (int) $userId;
		}
		else
		{
			$whereConditions[] = $db->quoteName('recepient') . ' = ' . Factory::getUser()->id;
		}

		$query
			->update($db->quoteName('#__tjnotifications_notifications'))
			->set($fields)
			->where($whereConditions);

		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method to mark notifications as read
	 *
	 * @param   int  $pk  The id of the row to mark as read
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    2.1.0
	 */
	public function markAsRead($pk)
	{
		return $this->updateNotificationStatus('read', $pk, 1);
	}
}
