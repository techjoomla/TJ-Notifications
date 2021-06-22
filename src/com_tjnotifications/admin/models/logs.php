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
 * notifications model.
 *
 * @since  1.1.0
 */
class TjnotificationsModelLogs extends ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.1.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'tjl.id',
				'to', 'tjl.to',
				'key', 'tjl.key',
				'from', 'tjl.from',
				'cc', 'tjl.cc',
				'state', 'tjl.state',
				'subject', 'tjl.subject',
				'search'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	protected function populateState($ordering = 'tjl.id', $direction = 'desc')
	{
		parent::populateState($ordering, $direction);

		$app = Factory::getApplication();

		// Load the filter search
		$search = $app->getUserStateFromRequest($this->context . 'filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// Get pagination request variables
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = Factory::getApplication()->input->post->get('limitstart');

		// In case limit has been changed, adjust it
		$limitstart = ($limit !== 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('list.limit', $limit);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.1.0
	 */
	protected function getListQuery()
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__tj_notification_logs', 'tjl'));

		$search = $this->getState('filter.search');

		// Filter by client
		$client = $this->getState('filter.client');

		if ($client)
		{
			$query->where($db->quoteName('tjl.client') . ' = ' . $db->quote($client));
		}

		// Filter by backend
		$backend = $this->getState('filter.backend');

		if ($backend)
		{
			$query->where($db->quoteName('tjl.backend') . ' = ' . $db->quote($backend));
		}

		// Filter by key
		$key = $this->getState('filter.key');

		if ($key)
		{
			$query->where($db->quoteName('tjl.key') . ' = ' . $db->quote($key));
		}

		// Filter by client
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where($db->quoteName('tjl.state') . ' = ' . $db->quote($state));
		}

		if (!empty($search))
		{
			$like = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
			$query->where(
				$db->quoteName('tjl.subject') . ' LIKE ' . $like . '
				OR ' . $db->quoteName('tjl.from') . ' LIKE ' . $like . '
				OR ' . $db->quoteName('tjl.to') . ' LIKE ' . $like
			);
		}

		$orderCol  = $this->state->get('list.ordering', 'tjl.id');
		$orderDirn = $this->state->get('list.direction', 'desc');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->quoteName($orderCol) . ' ' . $db->escape($orderDirn));
		}

		return $query;
	}
}
