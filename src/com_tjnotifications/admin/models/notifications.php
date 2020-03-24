<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Table\Table;

jimport('joomla.application.component.model');
/**
 * notifications model.
 *
 * @since  1.6
 */
class TjnotificationsModelNotifications extends Joomla\CMS\MVC\Model\ListModel
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
				'state',
				'title'
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
		$extension = Factory::getApplication()->input->get('extension', '', 'word');
		$parts     = explode('.', $extension);

		// Extract the component name
		$this->setState('filter.component', $parts[0]);

		// Extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts[1] : null);

		// Initialize variables.
		$db    = Factory::getDbo();
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

		// For getting templates
		if (!empty($client) && !empty($key))
		{
			$query->where($db->quoteName('client') . ' = ' . $db->quote($client) . ' AND ' . $db->quoteName('key') . ' = ' . $db->quote($key));
			$query->order($db->quoteName('key'), 'ASC');
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
	 * Method to get the record form.
	 *
	 * @param   String  $client  An optional array of data for the form to interogate.
	 * @param   String  $key     True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getTemplate($client, $key)
	{
		$object = clone $this;

		$this->setState('filter.key', $key);
		$this->setState('filter.client', $client);

		// Return exact template according key and client
		$templates = $this->getItems();

		// If templates object is empty and key contain # then check for default (fallback) template.
		if (empty($templates) && strpos($key, '#'))
		{
			// Regex for removing last part of the string
			// Eg if input string is global#vendor#course then the output is global#vendor

			$key = preg_replace('/#[^#]*$/', '', $key);

			// Call function recursively with modified key
			return $object->getTemplate($client, $key);
		}

		return $templates[0];
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
		$app       = Factory::getApplication();
		$ordering  = $app->input->get('filter_order', 'id', 'STRING');
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

		parent::populateState($ordering, $direction);

		$mainframe = Factory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('list.limit', $limit);
		$this->setState('list.start', $limitstart);
	}

	/**
	 * get items
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $key => $item)
		{
			if (empty($item->id))
			{
				return false;
			}

			if (!empty($item->id))
			{
				$db    = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->select('ntc.*');
				$query->from($db->qn('#__tj_notification_template_configs', 'ntc'));
				$query->where($db->qn('ntc.template_id') . '=' . (int) $item->id);

				$db->setQuery($query);
				$templateConfigs = $db->loadObjectlist();

				$providerConfigs = array();

				foreach ($templateConfigs as $keytemplate => $tConfig)
				{
					$providerConfigs['state'] = $tConfig->state;
					$json = json_decode($tConfig->params);

					if (!empty($json->cc))
					{
						$providerConfigs['cc'] = $json->cc;
					}

					if (!empty($json->bcc))
					{
						$providerConfigs['bcc'] = $json->bcc;
					}

					if (!empty($json->from_name))
					{
						$providerConfigs['from_name'] = $json->from_name;
					}

					if (!empty($json->from_email))
					{
						$providerConfigs['from_email'] = $json->from_email;
					}

					$providerConfigs['subject'] = $tConfig->subject;
					$providerConfigs['body'] = $tConfig->body;
					$providerConfigs['is_override'] = $tConfig->is_override;
					$providerConfigs['replacement_tags'] = $tConfig->replacement_tags;
					$provider = $tConfig->provider;

					$item->$provider = $providerConfigs;
				}
			}
		}

		return $items;
	}

	/**
	 * Delete Template Config
	 *
	 * @param   array  $templateId  template id
	 *
	 * @return  boolean
	 *
	 * @since   1.0.0
	 */
	public function  deleteTemplateConfig($templateId)
	{
		$db          = Factory::getDbo();

		foreach ($templateId as $id)
		{
			$deleteQuery = $db->getQuery(true);
			$conditions = array(
				$db->qn('template_id') . '=' . (int) $id
			);
			$deleteQuery->delete($db->quoteName('#__tj_notification_template_configs'));
			$deleteQuery->where($conditions);
			$db->setQuery($deleteQuery);
			$db->execute();
		}
	}
}
