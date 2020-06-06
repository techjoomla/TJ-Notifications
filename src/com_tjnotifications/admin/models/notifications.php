<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

/**
 * Notifications model
 *
 * @since  1.6
 */
class TjnotificationsModelNotifications extends ListModel
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
				'id','t.id',
				'client','t.client',
				'key','t.key',
				'state','t.state',
				'title','t.title',
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
	 * @since   1.6
	 */
	protected function populateState($ordering = 'id', $direction = 'asc')
	{
		$app       = Factory::getApplication();
		$ordering  = $app->input->get('filter_order', 'id', 'string');
		$direction = $app->input->get('filter_order_Dir', 'desc', 'string');

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

		$language = $app->getUserStateFromRequest($this->context . '.filter.language', 'filter_language', '', 'string');
		$this->setState('filter.language', $language);

		parent::populateState($ordering, $direction);

		// Get pagination request variables
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->input->get('limitstart', 0, 'int');

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
		$query->select('t.*')
			->from('`#__tj_notification_templates` AS t');

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

		// Filter by language
		$language = $this->getState('filter.language');

		if ($language !== '')
		{
			$query->select('ntc.language');
			$query->join('LEFT', '#__tj_notification_template_configs AS ntc ON ntc.template_id = t.id');
			$query->where($db->qn('ntc.language') . '=' . $db->quote($language));
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
	 * Get items
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

			// Processing to get language specific template data
			$db    = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('ntc.*');
			$query->from($db->qn('#__tj_notification_template_configs', 'ntc'));
			$query->where($db->qn('ntc.template_id') . '=' . (int) $item->id);
			$query->order($db->q('ntc.language'));

			$db->setQuery($query);
			$templateConfigs = $db->loadObjectlist();

			foreach (TJNOTIFICATIONS_CONST_PROVIDERS_ARRAY as $keyProvider => $provider)
			{
				$item->$provider['state']     = $templateConfigs[0]->state;
				$item->$provider['languages'] = array();

				foreach ($templateConfigs as $keytemplate => $tConfig)
				{
					if ($tConfig->provider == $provider && !in_array($tConfig->language, $item->$provider['languages']))
					{
						$item->$provider['languages'][] = $tConfig->language;
					}
				}
			}
		}

		return $items;
	}

	/**
	 * Get matching templates
	 *
	 * @param   string  $provider  Notification provider
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.0
	 */
	public function getMatchingTemplates($provider)
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = $this->getListQuery();
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		foreach ($items as $key => $item)
		{
			if (empty($item->id))
			{
				return false;
			}

			$db    = Factory::getDBO();
			$query = $db->getQuery(true);
			$query->select('ntc.*');
			$query->from($db->qn('#__tj_notification_template_configs', 'ntc'));
			$query->where($db->qn('ntc.template_id') . '=' . (int) $item->id);
			$query->where($db->qn('ntc.provider') . '=' . $db->quote($provider));
			$query->where($db->qn('ntc.language') . '=' . $db->quote($this->getState('filter.language')));
			$query->order($db->q('ntc.language'));

			$db->setQuery($query);
			$templateConfigs = $db->loadObjectlist();

			foreach ($templateConfigs as $keytemplate => $tConfig)
			{
				foreach ($tConfig as $configKey => $configValue)
				{
					$item->$provider[$configKey] = $configValue;
				}
			}
		}

		return $items;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   string  $client    An optional array of data for the form to interogate.
	 * @param   string  $key       True if the form is to load its own data (default case), false if not.
	 * @param   string  $language  Template language
	 * @param   string  $provider  Notification provider
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getTemplate($client, $key, $language, $provider = 'email')
	{
		$object = clone $this;

		$this->setState('filter.key', $key);
		$this->setState('filter.client', $client);
		$this->setState('filter.language', $language);

		// Find matching template for current language of user
		$templates = $this->getMatchingTemplates($provider);

		// If no matching template found, look for template with lang = *
		if (empty($templates))
		{
			// Setting $this vars don't work
			$object->setState('filter.language', '*');
			$templates = $object->getMatchingTemplates($provider);
		}

		// If templates object is empty and key contain # then check for default (fallback) template.
		if (empty($templates) && strpos($key, '#'))
		{
			// Regex for removing last part of the string
			// Eg if input string is global#vendor#course then the output is global#vendor

			$key = preg_replace('/#[^#]*$/', '', $key);

			// Call function recursively with modified key
			return $object->getTemplate($client, $key, $language, $provider);
		}

		return $templates[0];
	}

	/**
	 * Get a list of the current content languages
	 *
	 * @return  array
	 *
	 * @since   1.2.0
	 */
	public function getLanguages()
	{
		return LanguageHelper::getContentLanguages(array(0,1));
	}
}
