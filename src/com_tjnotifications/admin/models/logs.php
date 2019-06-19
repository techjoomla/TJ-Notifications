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
				'client',
				'key',
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
		// Load the filter state
		$published = $app->getUserStateFromRequest($this->context . 'filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

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
		$extension  = JFactory::getApplication()->input->get('extension', '', 'word');
		$parts = explode('.', $extension);

		// Extract the component name
		$this->setState('filter.component', $parts[0]);

		// Extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts[1] : null);

		// Initialize variables.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__tjnotification_logs'));

		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where($db->quoteName('client') . ' LIKE ' . $like . ' OR ' . $db->quoteName('key') . ' LIKE ' . $like);
		}

		if ($extension)
		{
			$query->where($db->quoteName('client') . ' = ' . $db->quote($extension));
		}
		else
		{
			// Filter by client
			$client = $this->getState('filter.client');
			$key = $this->getState('filter.key');

			if (!empty($client) && empty($key))
			{
				$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
			}
		}

		$orderCol  = $this->getState('list.ordering');
		$orderDirn = $this->getState('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->quoteName($orderCol) . ' ' . $db->escape($orderDirn));
		}

		return $query;
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
	 * @return  string  A store id.
	 *
	 * @since   1.1.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		return parent::getStoreId($id);
	}


	/**
	 * Method to get a list of articles.
	 * Overridden to add a check for access levels.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.1.0
	 */
	public function getItems()
	{
		$items = parent::getItems();
		return $items;
	}
}
