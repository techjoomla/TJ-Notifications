<?php
/**
 * @package    Com_Tjnotifications
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
use \Joomla\CMS\MVC\Model\ListModel;

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
				'id',
				'to',
				'key',
				'from',
				'cc',
				'provider',
				'state',
				'subject',
				'search',
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
	protected function populateState($ordering = 'id', $direction = 'asc')
	{
		$app    = JFactory::getApplication('administrator');

		$ordering = $app->input->get('filter_order', 'id', 'STRING');
		$direction = $app->input->get('filter_order_Dir', 'desc', 'STRING');

		if (!empty($ordering) && in_array($ordering, $this->filter_fields))
		{
			$this->setState('list.ordering', $ordering);
		}

		if (!empty($direction))
		{
			if (!in_array(strtolower($direction), array('asc', 'desc')))
			{
				$direction = 'desc';
			}

			$this->setState('list.direction', $direction);
		}

		// Load the filter search
		$search = $app->getUserStateFromRequest($this->context . 'filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState($ordering, $direction);

		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

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
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__tjnotification_logs', 'cs'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where($db->quoteName('subject') . ' LIKE ' . $like . ' OR ' . $db->quoteName('from') . ' LIKE ' . $like . ' OR ' . $db->quoteName('to') . ' LIKE ' . $like);
		}

		// Filter by client
		$client = $this->getState('filter.client');
		if ($client)
		{
			$client = $db->quote($client);
			$query->where('client = ' . $client);
		}

		// Filter by provider
		$provider = $this->getState('filter.provider');

		if ($provider)
		{
			$provider = $db->quote($provider);
			$query->where('provider = ' . $provider);
		}

		// Filter by key
		$key = $this->getState('filter.key');
		if ($key)
		{
			$key = $db->quote($key);
			$query->where('cs.key = ' . $key);
		}

		// Filter by client
		$state = $this->getState('filter.state');

		$state = $db->quote($state);
		$query->where('cs.state = ' . $state);


		$orderCol  = $this->getState('list.ordering');
		$orderDirn = $this->getState('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->quoteName($orderCol) . ' ' . $db->escape($orderDirn));
		}

		return $query;
	}
}
