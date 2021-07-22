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
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart = $app->input->get('limitstart', 0, 'int');

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
			$query->order($db->quoteName('key') . ' ASC');
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

			if (version_compare(phpversion(), '7.4.0', '<'))
			{
				// Set status here
				foreach ($templateConfigs as $keytemplate => $tConfig)
				{
					$backend                   = $tConfig->backend;
					$item->{$backend}['state'] = $tConfig->state;
				}

				// Set langauges here
				$backendsArray = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

				foreach ($backendsArray as $keyBackend => $backend)
				{
					$item->{$backend}['languages'] = array();

					foreach ($templateConfigs as $keytemplate => $tConfig)
					{
						if ($tConfig->backend == $backend && !in_array($tConfig->language, $item->{$backend}['languages']))
						{
							// $item->{$backend}['languages'][] = $tConfig->language;

							array_push($item->{$backend}['languages'], $tConfig->language);
						}
					}
				}
			}
			else
			{
				// Set status here
				foreach ($templateConfigs as $keytemplate => $tConfig)
				{
					$backend                 = $tConfig->backend;
					$item->$backend['state'] = $tConfig->state;
				}

				// Set langauges here
				$backendsArray = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

				foreach ($backendsArray as $keyBackend => $backend)
				{
					$item->$backend['languages'] = array();

					foreach ($templateConfigs as $keytemplate => $tConfig)
					{
						if ($tConfig->backend == $backend && !in_array($tConfig->language, $item->$backend['languages']))
						{
							// $item->$backend['languages'][] = $tConfig->language;

							array_push($item->$backend['languages'], $tConfig->language);
						}
					}
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
	 * @param   string  $backend   Notification backend
	 *
	 * @return  object|boolean
	 *
	 * @since    1.6
	 */
	public function getTemplate($client, $key, $language, $backend = 'email')
	{
		$object = clone $this;

		/*
		SELECT
			`t`.`id`,`t`.`client`,`t`.`key`,`t`.`title`,`t`.`replacement_tags`,
			`ntc`.`backend`,`ntc`.`language`,`ntc`.`subject`,`ntc`.`body`,`ntc`.`params`,`ntc`.`state`
		FROM `tjn_tj_notification_templates` AS `t`
		LEFT JOIN `tjn_tj_notification_template_configs` AS `ntc` ON `ntc`.`template_id` = `t`.`id`
		WHERE
		`t`.`key` = 'checkin'
		AND `t`.`client` = 'com_jticketing'
		AND `ntc`.`backend` = 'push'
		AND `ntc`.`language` = 'hi-IN'
		UNION (
			SELECT
				`t`.`id`,`t`.`client`,`t`.`key`,`t`.`title`,`t`.`replacement_tags`,
				`ntc`.`backend`,`ntc`.`language`,`ntc`.`subject`,`ntc`.`body`,`ntc`.`params`,`ntc`.`state`
			FROM `tjn_tj_notification_templates` AS `t`
			LEFT JOIN `tjn_tj_notification_template_configs` AS `ntc` ON `ntc`.`template_id` = `t`.`id`
			WHERE
				`t`.`key` = 'checkin'
				AND `t`.`client` = 'com_jticketing'
				AND `ntc`.`backend` = 'push'
				AND `ntc`.`language` = '*'
				AND NOT EXISTS (
					SELECT 1
					FROM `tjn_tj_notification_templates` AS `t`
					LEFT JOIN `tjn_tj_notification_template_configs` AS `ntc` ON `ntc`.`template_id` = `t`.`id`
					WHERE
						`t`.`key` = 'checkin'
						AND `t`.`client` = 'com_jticketing'
						AND `ntc`.`backend` = 'push'
						AND `ntc`.`language` = 'hi-IN'
				)
		)
		*/

		// Use union to get template for language needed, if not found get for language = *
		$db     = JFactory::getDbo();
		$query1 = $db->getQuery(true);
		$query2 = $db->getQuery(true);
		$query3 = $db->getQuery(true);

		$query1
			->select(
				$db->quoteName(
					array(
						't.id', 't.client', 't.key', 't.title', 't.replacement_tags',
						'ntc.backend', 'ntc.language', 'ntc.subject', 'ntc.body', 'ntc.params', 'ntc.state', 'ntc.provider_template_id'
					)
				)
			)
			->from($db->quoteName('#__tj_notification_templates', 't'))
			->join(
				'LEFT',
				$db->quoteName('#__tj_notification_template_configs', 'ntc') . ' ON ' . $db->quoteName('ntc.template_id') . ' = ' . $db->quoteName('t.id')
			)
			->where($db->quoteName('t.key') . ' = ' . $db->quote($key))
			->where($db->quoteName('t.client') . ' = ' . $db->quote($client))
			->where($db->quoteName('ntc.backend') . ' = ' . $db->quote($backend))
			->where($db->quoteName('ntc.language') . ' = ' . $db->quote($language));

		$query2
			->select(
				$db->quoteName(
					array(
						't.id', 't.client', 't.key', 't.title', 't.replacement_tags',
						'ntc.backend', 'ntc.language', 'ntc.subject', 'ntc.body', 'ntc.params', 'ntc.state', 'ntc.provider_template_id'
					)
				)
			)
			->from($db->quoteName('#__tj_notification_templates', 't'))
			->join(
				'LEFT',
				$db->quoteName('#__tj_notification_template_configs', 'ntc') . ' ON ' . $db->quoteName('ntc.template_id') . ' = ' . $db->quoteName('t.id')
			)
			->where($db->quoteName('t.key') . ' = ' . $db->quote($key))
			->where($db->quoteName('t.client') . ' = ' . $db->quote($client))
			->where($db->quoteName('ntc.backend') . ' = ' . $db->quote($backend))
			->where($db->quoteName('ntc.language') . ' = ' . $db->quote('*'))
			->where(
				'NOT EXISTS ( ' .
					$query3->select('1')
					->from($db->quoteName('#__tj_notification_templates', 't'))
					->join(
						'LEFT',
						$db->quoteName('#__tj_notification_template_configs', 'ntc') . ' ON ' . $db->quoteName('ntc.template_id') . ' = ' . $db->quoteName('t.id')
					)
					->where($db->quoteName('t.key') . ' = ' . $db->quote($key))
					->where($db->quoteName('t.client') . ' = ' . $db->quote($client))
					->where($db->quoteName('ntc.backend') . ' = ' . $db->quote($backend))
					->where($db->quoteName('ntc.language') . ' = ' . $db->quote($language) . ')')
			);

		$query1->union($query2);

		$templates = $db->setQuery($query1)->loadObjectList();

		// If templates object is empty and key contain # then check for default (fallback) template.
		if (empty($templates) && strpos($key, '#'))
		{
			// Regex for removing last part of the string
			// Eg if input string is global#vendor#course then the output is global#vendor

			$key = preg_replace('/#[^#]*$/', '', $key);

			// Call function recursively with modified key
			return $object->getTemplate($client, $key, $language, $backend);
		}

		return (!empty($templates[0]) ? $templates[0] : false);
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
