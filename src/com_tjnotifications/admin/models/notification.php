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
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\CMS\MVC\Model\AdminModel;
use \Joomla\CMS\MVC\Model\ListModel;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;

jimport('joomla.application.component.model');
BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models');

/**
 * notification model.
 *
 * @since  1.6
 */
class TjnotificationsModelNotification extends \Joomla\CMS\MVC\Model\AdminModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   3.2
	 */
	public function __construct($config = array())
	{
		$config['event_after_save'] = 'tjnOnAfterSaveNotificationTemplate';
		parent::__construct($config);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type='Notification',$prefix='tjnotificationTable',$config=array())
	{
		// Get the table.
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_tjnotifications.notification',
			'notification',
			array
			(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since   1.0
	 */
	protected function loadFormData()
	{
		$extension = Factory::getApplication()->input->get('extension', '', 'word');
		$parts     = explode('.', $extension);

		// Extract the component name
		$this->setState('filter.component', $parts[0]);

		// Extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts[1] : null);

		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_tjnotifications.edit.tjnotifications.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to create notifications templates.
	 *
	 * @param   array  $templates  An array of data for the notifications templates.
	 *
	 * @return  Boolean  true or false
	 *
	 * @since    1.6
	 */
	public function createTemplates($templates)
	{
		$data  = $templates;
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$model = ListModel::getInstance('Notifications', 'TJNotificationsModel');

		if (!empty($data['replacement_tags']))
		{
			$data['replacement_tags'] = json_encode($data['replacement_tags']);
		}

		if ($data['client'] and $data['key'])
		{
			$model->setState('filter.client', $data['client']);
			$model->setState('filter.key', $data['key']);

			$result = $model->getItems();

			foreach ($result as $res)
			{
				if ($res->id)
				{
					$data['id'] = $res->id;
				}
			}
		}

		if ($data)
		{
			parent::save($data);

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete notification template
	 *
	 * @param   int  &$cid  Id of template.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function delete(&$cid)
	{
		$db          = Factory::getDbo();
		$deleteQuery = $db->getQuery(true);
		$value       = array();
		$model       = AdminModel::getInstance('Notification', 'TJNotificationsModel');
		$user        = Factory::getUser();

		if (empty($user->authorise('core.delete', 'com_tjnotifications')))
		{
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		foreach ($cid as $id)
		{
			$data = $model->getItem($id);

			if ($data->core == 0)
			{
				$deleteQuery = $db->getQuery(true);
				$conditions = array(
					$db->quoteName('client') . ' = ' . $db->quote($data->client),
					$db->quoteName('key') . ' = ' . $db->quote($data->key)
				);
				$deleteQuery->delete($db->quoteName('#__tj_notification_user_exclusions'));
				$deleteQuery->where($conditions);
				$db->setQuery($deleteQuery);
				$result = $db->execute();

				if ($result)
				{
					$value[] = 1;
					parent::delete($data->id);
					$dispatcher = JDispatcher::getInstance();
					PluginHelper::importPlugin('tjnotification');
					$dispatcher->trigger('tjnOnAfterDeleteNotificationTemplate', array($data));
				}
			}
			else
			{
				$value[] = 0;
			}
		}

		return $value;
	}

	/**
	 * Method to get existing keys
	 *
	 * @param   string  $client  client.
	 *
	 * @return  existingkeys
	 *
	 * @since   1.0
	 */
	public function getKeys($client)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('key'));
		$query->from($db->quoteName('#__tj_notification_templates'));
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$db->setQuery($query);

		$existingKeys = $db->loadColumn();

		return $existingKeys;
	}

	/**
	 * Method to get tag replacement count
	 *
	 * @param   string  $key     Template key.
	 * @param   string  $client  client.
	 *
	 * @return  integer  replacement tags count
	 *
	 * @since   1.0.4
	 */
	public function getReplacementTagsCount($key, $client)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('replacement_tags'));
		$query->from($db->quoteName('#__tj_notification_templates'));
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('key') . ' = ' . $db->quote($key));
		$db->setQuery($query);
		$replacementTags = $db->loadResult();

		return count(json_decode($replacementTags));
	}

	/**
	 * Method to replace tags if they are changed
	 *
	 * @param   array  $data  template data
	 *
	 * @return  void
	 *
	 * @since   1.0.4
	 */
	public function updateReplacementTags($data)
	{
		if (!empty($data['replacement_tags']))
		{
			$replacementTags = json_encode($data['replacement_tags']);
		}
		else
		{
			return;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Fields to update.
		$fields = array(
			$db->quoteName('replacement_tags') . ' = ' . $db->quote($replacementTags)
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('client') . ' = ' . $db->quote($data['client']),
			$db->quoteName('key') . ' = ' . $db->quote($data['key'])
		);

		$query->update($db->quoteName('#__tj_notification_templates'))->set($fields)->where($conditions);
		$db->setQuery($query);

		$result = $db->execute();
	}
}
