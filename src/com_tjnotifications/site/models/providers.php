<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

/**
 * TJNotification model.
 *
 * @since  1.6
 */
class TJNotificationsModelProviders extends ListModel
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('DISTINCT(provider)');
		$query->from($db->quoteName('#__tj_notification_providers'));
		$query->where($db->quoteName('state') . '=' . $db->quote('1'));

		// $db->setQuery($query);
		// s$query = $db->loadObjectList();

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */

	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Method to get an array of getProvider
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getProvider()
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('DISTINCT(provider)');
		$query->from($db->quoteName('#__tj_notification_providers'));
		$query->where($db->quoteName('state') . '=' . $db->quote('1'));
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the Table class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for Table object
	 *
	 * @return  Table|boolean Table if found, boolean false on failure
	 */
	public function getTable($type ='Provider', $prefix = 'TJNotificationsTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
}
