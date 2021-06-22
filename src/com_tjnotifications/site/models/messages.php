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

use Joomla\CMS\MVC\Model\ListModel;

/**
 * List model for notification
 *
 * @package  Tjnotifications
 *
 * @since    2.1.0
 */
class TjnotificationsModelMessages extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelLegacy
	 * @since   2.1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'recepient', 'a.recepient',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState("a.id", "desc");

		$context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $context);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    2.1.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		// $query->select($this->getState('list.select, DISTINCT a.*'));
		$query->select('a.id, a.title, a.body, a.icon, a.link, a.created_on, a.read');
		$query->from('`#__tjnotifications_notifications` AS a');

		// Filter by published state
		$recepient = $this->getState('filter.recepient');

		if (is_numeric($recepient))
		{
			$query->where('a.recepient = ' . (int) $recepient);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "desc");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		// Do processing as needed

		return $items;
	}

	/**
	 * Get undelivered notifications
	 *
	 * @param   string  $userid  Userid
	 *
	 * @return void|array
	 */
	public function getUndeliveredNotifications($userid)
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('a.id, a.title, a.body, a.icon, a.link, a.created_on');
		$query->from('`#__tjnotifications_notifications` AS a');

		// Filter by userid
		if (!is_numeric($userid))
		{
			return;
		}
		else
		{
			$query->where('a.recepient = ' . (int) $userid);
		}

		// Filter by delivered = 0
		$query->where('a.delivered = 0');

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', "a.id");
		$orderDirn = $this->state->get('list.direction', "desc");

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$undeliveredNotifications = $db->setQuery($query)->loadObjectList();

		if (empty($undeliveredNotifications))
		{
			return;
		}

		return $undeliveredNotifications;
	}
}
