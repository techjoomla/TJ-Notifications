<?php
/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
/**
 * notifications model.
 *
 * @since  1.6
 */
class TjnotificationsModelNotifications extends JModelList
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see        JController
	* @since      1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'client',
				'key',
				'state', 'a.state'

			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
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

		$client      = $this->getState('filter.client');
		$provider      = $this->getState('filter.provider');
		$userControl = $this->getState('user_control');

		if (!empty($provider))
		{
			$query->select('client,`key`');
			$query->from($db->quoteName('#__tj_notification_templates'));
			$query->where($db->quoteName($provider) . '=' . $db->quote('1'));
		}
		elseif (!empty($client) && !empty($userControl))
		{
			$query->select('DISTINCT(`key`)')
			->from($db->quoteName('#__tj_notification_templates'));

			$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
			$query->where($db->quoteName('user_control') . ' = ' . $db->quote($userControl));
		}
		else
		{
			$distictClient = $this->getState('distinct.client');
			$search        = $this->getState('filter.search');
			$client        = $this->getState('filter.client');
			$key           = $this->getState('filter.key');

			if (!empty($search))
			{
				// Create the base select statement.
				$query->select('*');
				$query->from($db->quoteName('#__tj_notification_templates'));

				$like = $db->quote('%' . $search . '%');
				$query->where($db->quoteName('client') . ' LIKE ' . $like . ' OR ' . $db->quoteName('key') . ' LIKE ' . $like);
			}

			if ($extension)
			{
				// Create the base select statement.
				$query->select('*');
				$query->from($db->quoteName('#__tj_notification_templates'));
				$query->where($db->quoteName('client') . ' = ' . $db->quote($extension));
			}
			elseif (!empty($client) && empty($key))
			{
				// Create the base select statement.
				$query->select('*');
				$query->from($db->quoteName('#__tj_notification_templates'));
				$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
			}
			elseif($distictClient)
			{
				$query->select('DISTINCT(client)');
				$query->from($db->quoteName('#__tj_notification_templates'));
			}
			else
			{
				$query->select('*');
				$query->from($db->quoteName('#__tj_notification_templates'));
			}

			// Fot getting templates
			if (!empty($client) && !empty($key))
			{
				$query->where($db->quoteName('client') . ' = ' . $db->quote($client) . ' AND ' . $db->quoteName('key') . ' = ' . $db->quote($key));
				$query->order('`key` ASC');
			}
		}

		return $query;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array  $client  An optional array of data for the form to interogate.
	 * @param   array  $key     True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getTemplate($client,$key)
	{
		$this->setState('filter.client', $client);
		$this->setState('filter.key', $key);

		// Explode the key at #. e.g : donate#vendor1#store1
		$key_parts = explode('#', $key);
		$templates = $this->getItems();

		// If $key_parts[1] i.e vendor1 is set means overrided then it will return latest template. i.e donate#vendor1#store1
		if (isset($key_parts[1]))
		{
			// Get index of latest template.
			$latest = sizeof($templates) - 1;
			$template = $templates[$latest];

			return $template;
		}
		else
		{
			// If template is not overrided then return original template e.g: donate

			return $templates[0];
		}
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
	 * @since   1.6
	 */
	protected function populateState($ordering = 'id', $direction = 'asc')
	{
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
}
