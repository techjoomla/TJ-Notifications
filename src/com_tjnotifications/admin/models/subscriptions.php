<?php
/**
 * @package     Tjnotifications
 * @subpackage  com_tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

/**
 * List model for subscription
 *
 * @package  Tjnotifications
 *
 * @since    2.0.0
 */
class TjnotificationsModelSubscriptions extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelLegacy
	 * @since   2.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'backend', 'a.backend',
				'address', 'a.address',
				'platform', 'a.platform',
				'state', 'a.state',
				'user_id', 'a.user_id',
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

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    2.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    2.0.0
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);

		$query->from('`#__tjnotifications_subscriptions` AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Filter by backend
		$backend = $this->getState('filter.backend');

		if (!empty($backend))
		{
			$query->where('a.backend = ' . $db->quote($db->escape($backend, true)));
		}

		// Filter by platform
		$platform = $this->getState('filter.platform');

		if (!empty($platform))
		{
			$query->where('a.platform = ' . $db->quote($db->escape($platform, true)));
		}

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by usesr Id
		$userId = $this->getState('filter.user_id');

		if (!empty($userId))
		{
			$query->where('a.user_id = ' . (int) $userId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search, true) . '%');
				$query->where('
					a.title LIKE ' . $search . '
					OR a.address LIKE ' . $search . '
					OR a.device_id LIKE ' . $search
				);
			}
		}

		// Filter by addreess
		$search = $this->getState('filter.address');

		if (!empty($search))
		{
			$search = $db->quote($search, true);
			$query->where('a.address = ' . $search);
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
	 *
	 * @since    2.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		// Do processing as needed

		return $items;
	}

	/**
	 * Method to get the user $subscriptionss
	 *
	 * @param   int  $userId  User id
	 *
	 * @return  array
	 *
	 * @since    2.0.0
	 */
	public function getUserSubscriptions($userId)
	{
		// Use union to get template for language needed, if not found get for language = *
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('s.*')
			->from($db->quoteName('#__tjnotifications_subscriptions', 's'))
			->where($db->quoteName('s.user_id') . ' = ' . (int) $userId)
			->where($db->quoteName('s.state') . ' = ' . 1)
			->where($db->quoteName('s.is_confirmed') . ' = ' . 1);

		return $db->setQuery($query)->loadObjectList();
	}
}
