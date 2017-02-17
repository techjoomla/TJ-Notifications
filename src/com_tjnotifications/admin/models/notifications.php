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

		// Create the base select statement.
		$query->select('*')
			->from($db->quoteName('#__tj_notification_templates'));

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

			// Fot getting templates
			if (!empty($client) && !empty($key))
			{
				$like = $db->quote($key . '%');
				$query->where($db->quoteName('client') . ' = ' . $db->quote($client) . ' AND ' . $db->quoteName('key') . ' LIKE ' . $like);
				$query->order('`key` ASC');
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
	* Method to delete.
	*
	* @param   int  $cid  Id of template.
	*
	* @return  null             Nothing
	*
	* @since    1.6
	*/
	public function delete($cid)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('client,`key`');
		$query->from('#__tj_notification_templates');
		$query->where($db->quoteName('id') . ' IN ( ' . implode(',', $cid) . ' )');
		$db->setQuery($query);
		$clientAndKeys = $db->loadObjectList();
		$db          = JFactory::getDbo();
		$deleteQuery = $db->getQuery(true);

		foreach ($clientAndKeys as $clientAndKey)
		{
			$deleteQuery = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('client') . ' = ' . $db->quote($clientAndKey->client),
				$db->quoteName('key') . ' = ' . $db->quote($clientAndKey->key)
			);
			$deleteQuery->delete($db->quoteName('#__tj_notification_user_exclusions'));
			$deleteQuery->where($conditions);
			$db->setQuery($deleteQuery);
			$result = $db->execute();
		}
	}
}
