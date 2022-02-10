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

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;

BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models');
require_once JPATH_ADMINISTRATOR . '/components/com_tjnotifications/defines.php';

/**
 * notification model.
 *
 * @since  1.6
 */
class TjnotificationsModelNotification extends AdminModel
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
	 * @return  JTable|boolean
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
		$extension = Factory::getApplication()->input->getCmd('extension', '');
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
		$date  = new Date;

		if (empty($data['created_on']))
		{
			$data['created_on'] = $date->format(Text::_('DATE_FORMAT_FILTER_DATETIME'));
		}

		// To save  data of replacement tags
		if (!empty($data['replacement_tags']))
		{
			$data['replacement_tags'] = json_encode($data['replacement_tags']);
		}

		if (!empty($data))
		{
			$this->save($data);

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
	 * @param   array  &$cid  An array of record primary keys.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function delete(&$cid)
	{
		$db    = Factory::getDbo();
		$value = array();
		$model = AdminModel::getInstance('Notification', 'TJNotificationsModel');
		$user  = Factory::getUser();

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
					$db->qn('template_id') . '=' . (int) $id
				);
				$deleteQuery->delete($db->quoteName('#__tj_notification_template_configs'));
				$deleteQuery->where($conditions);
				$db->setQuery($deleteQuery);
				$db->execute();

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
	 * @return  array
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

		return $db->loadColumn();
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

		return count((array) json_decode($replacementTags));
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

		$db->execute();
	}

	/**
	 * Method to  save notification data
	 *
	 * @param   ARRAY  $data  notification data
	 *
	 * @return  mixed Template ID
	 *
	 * @since    1.0.0
	 */
	public function save($data)
	{
		$isNew = true;

		// 1 - save template first
		if (!empty($data))
		{
			$date = Factory::getDate();

			if ($data['id'])
			{
				$data['updated_on'] = $date->toSql(true);
			}
			else
			{
				$data['created_on'] = $date->toSql(true);
			}

			if (!empty($data['replacement_tags']))
			{
				$data['replacement_tags'] = json_encode($data['replacement_tags']);
			}
		}

		if (!parent::save($data))
		{
			return false;
		}
		else
		{
			// IMPORTANT to set new id in state, it is fetched in controller later
			// Get current Template id
			$templateId = (int) $this->getState($this->getName() . '.id');
			$this->setState('com_tjnotifications.edit.notification.id', $templateId);
			$this->setState('com_tjnotifications.edit.notification.new', $isNew);
		}

		if (empty($templateId))
		{
			return false;
		}

		// Get DB
		$db = Factory::getDbo();

		// Get global configs
		$notificationsParams = ComponentHelper::getParams('com_tjnotifications');
		$webhookUrls = $notificationsParams->get('webhook_url');
		$webhookUrls = array_column((array) $webhookUrls, 'url');

		// 2 - save backend specific config
		$backendsArray = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

		foreach ($backendsArray as $keyBackend => $backend)
		{
			// 2.1 Check if current backend exists in posted data
			// If not $data['email'] or $data['sms']
			if (empty($data[$backend]))
			{
				continue;
			}

			// Eg: Get count of $data['email']['emailfields'] or $data['sms']['smsfields']
			if (count($data[$backend][$backend . 'fields']) == 0)
			{
				continue;
			}

			$idsToBeDeleted = array();
			$idsToBeStored  = array();

			// For current template, get existing lang. sepcific template for current backend
			$existingBackendConfigs = $this->getExistingTemplates($data['id'], $backend);

			// 2.2 Find existing template config entries to be deleted (i.e. language specific templates removed by user)
			foreach ($data[$backend][$backend . 'fields'] as $backendName => $backendFieldValues)
			{
				// webhook stuff starts here
				if ($backend == 'webhook' && $data[$backend]['state'])
				{
					// If not using global webhook URLs & custom webhooks URLs are also empty
					if (empty($backendFieldValues['use_gbwh_url']) && empty($backendFieldValues['webhook_url']))
					{
						$this->setError(Text::_('COM_TJNOTIFICATIONS_TEMPLATE_ERR_MSG_CUSTOM_WEBHOOK_URLS'));

						return false;
					}
					// If using global webhook URl & the global urls are empty
					elseif ($backendFieldValues['use_gbwh_url'] && empty($webhookUrls[0]))
					{
						$this->setError(Text::_('COM_TJNOTIFICATIONS_TEMPLATE_ERR_MSG_GLOBAL_WEBHOOK_URLS'));

						return false;
					}
				}
				// webhook stuff ends here

				// Iterate through each lang. specific config entry
				foreach ($existingBackendConfigs as $existingBackendConfig)
				{
					$existingBackendConfigId     = json_decode(json_encode($existingBackendConfig->id), true);
					$existingBackendConfigTempId = json_decode(json_encode($existingBackendConfig->template_id), true);
					$existingBackendConfigLang   = json_decode(json_encode($existingBackendConfig->language), true);

					// If there is existing template then return to form view.
					if ($existingBackendConfigLang == $backendFieldValues['language']
						&& $existingBackendConfigTempId == $templateId
						&& $existingBackendConfigId != $backendFieldValues['id'])
					{
						$this->setError(Text::_('COM_TJNOTIFICATIONS_TEMPLATE_ERR_MSG_TEMPLATE_EXISTS'));

						return false;
					}

					// Find to be deleted or saved
					if (($existingBackendConfigId == $backendFieldValues['id'] || $existingBackendConfigLang == "*" )
						|| $existingBackendConfigId == empty($backendFieldValues['id']))
					{
						$idsToBeStored[] = $backendFieldValues['id'];
					}
					else
					{
						$idsToBeDeleted[] = $existingBackendConfigId;
					}
				}
			}

			// Array of backend specific template configs id to be deleted
			$backendConfigIdsToBeDeleted = array_diff($idsToBeDeleted, $idsToBeStored);

			// Function call to delete template configs
			$this->deleteBackendConfigs($backendConfigIdsToBeDeleted);

			// 2.3 Common data for saving
			$createdOn = !empty($data['created_on']) ? $data['created_on'] : '';
			$updatedOn = !empty($data['updated_on']) ? $data['updated_on'] : '';

			// 2.4 try saving all backend specific configs
			// This has repeatable data eg: $data['email']['emailfields'] or $data['sms']['smsfields']
			foreach ($data[$backend][$backend . 'fields'] as $backendName => $backendFieldValues)
			{
				$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));
				$templateConfigTable->load(array('template_id' => $templateId, 'backend' => $backendName));

				// Non-repeat data
				$templateConfigTable->template_id = $templateId;
				$templateConfigTable->backend     = $backend;
				$templateConfigTable->state       = $data[$backend]['state'];
				$templateConfigTable->created_on  = $createdOn;
				$templateConfigTable->updated_on  = $updatedOn;

				// Get params data
				// State, emailfields / smsfields
				$nonParamsFields = array('state', $backend . 'fields');
				$params = array();

				foreach ($data[$backend] as $fieldKey => $fieldValue)
				{
					if (!in_array($fieldKey, $nonParamsFields) && !empty($data[$backend][$fieldKey]))
					{
						$params[$fieldKey] = $data[$backend][$fieldKey];
					}
				}

				$templateConfigTable->params = json_encode($params);

				// Repeatable data
				$templateConfigTable->subject  = !empty($backendFieldValues['subject']) ? $backendFieldValues['subject']: '';
				$templateConfigTable->body     = $backendFieldValues['body'];
				$templateConfigTable->language = $backendFieldValues['language'];

				// Webhook stuff starts here
				// Add URLs for webhook
				$templateConfigTable->webhook_url  = !empty($backendFieldValues['webhook_url']) ? json_encode($backendFieldValues['webhook_url']): '';

				$templateConfigTable->use_gbwh_url  = !empty($backendFieldValues['use_gbwh_url']) ? $backendFieldValues['use_gbwh_url']: 0;
				// Webhook stuff ends here

				if (!empty($backendFieldValues['provider_template_id']))
				{
					$templateConfigTable->provider_template_id = $backendFieldValues['provider_template_id'];
				}

				// Save backend in config table
				if (empty($backendFieldValues['id']))
				{
					$templateConfigTable->save($templateConfigTable);
				}
				else
				{
					$templateConfigTable->id = $backendFieldValues['id'];
					$templateConfigTable->save($templateConfigTable);
				}
			}
		}

		return true;
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($id = null)
	{
		$item = parent::getItem($id);

		if (empty($item->id))
		{
			return $item;
		}

		$backendsArray = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

		foreach ($backendsArray as $keyBackend => $backend)
		{
			$db                   = Factory::getDBO();
			$backendConfigsQuery = $db->getQuery(true);

			$backendConfigsQuery->select('ntc.*');
			$backendConfigsQuery->where(
				$db->qn('ntc.template_id') . '=' . (int) $item->id .
				' AND ' . $db->quoteName('backend') . " = '" . $backend . "'"
			);
			$backendConfigsQuery->from($db->qn('#__tj_notification_template_configs', 'ntc'));
			$db->setQuery($backendConfigsQuery);
			$backendConfigsList = $db->loadObjectlist();

			if (empty($backendConfigsList))
			{
				continue;
			}

			// We can get common config for current backend from any backend config for current template ID
			$singleBackendRow = $backendConfigsList[0];

			// Define non-params fields - state, emailfields / smsfields
			$nonParamsFields  = array('state', $backend . 'fields');

			// Start setting backend specific data for edit
			$item->$backend = array();

			if (version_compare(phpversion(), '7.4.0', '<'))
			{
				$item->{$backend}['state'] = $singleBackendRow->state;

				// Get params for current backend from any backend config for current template ID
				$json = (array) json_decode($singleBackendRow->params);

				foreach ($json as $fieldKey => $fieldValue)
				{
					if (!in_array($fieldKey, $nonParamsFields))
					{
						$item->{$backend}[$fieldKey] = $fieldValue;
					}
				}

				// Last, set all config rows list as repeatable data
				$item->{$backend}[$backend . 'fields'] = $backendConfigsList;
			}
			else
			{
				$item->$backend['state'] = $singleBackendRow->state;

				// Get params for current backend from any backend config for current template ID
				$json = (array) json_decode($singleBackendRow->params);

				foreach ($json as $fieldKey => $fieldValue)
				{
					if (!in_array($fieldKey, $nonParamsFields))
					{
						$item->$backend[$fieldKey] = $fieldValue;
					}
				}

				// Last, set all config rows list as repeatable data
				$item->$backend[$backend . 'fields'] = $backendConfigsList;
			}
		}

		return $item;
	}

	/**
	 * get notification templates of component
	 *
	 * @param   integer  $templateId  Notification template ID
	 * @param   string   $backend     Backend name
	 *
	 * @return array
	 *
	 * @since  2.1
	 */
	public function getExistingTemplates($templateId, $backend)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('`id`,`language`,`template_id`');
		$query->from($db->quoteName('#__tj_notification_template_configs'));
		$query->where(
			$db->quoteName('template_id') . ' = ' . $db->quote($templateId) .
			' AND ' . $db->quoteName('backend') . " = '" . $backend . "'"
		);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to delete templates if they are deleted from form view
	 *
	 * @param   array  $backendConfigIdsToBeDeleted  template data
	 *
	 * @return  void
	 *
	 * @since   1.0.4
	 */
	public function deleteBackendConfigs($backendConfigIdsToBeDeleted)
	{
		if (!empty($backendConfigIdsToBeDeleted))
		{
			foreach ($backendConfigIdsToBeDeleted as $entry)
			{
				$db          = Factory::getDBO();
				$deleteQuery = $db->getQuery(true);
				$conditions  = array(
				$db->qn('id') . '=' . (int) $entry);
				$deleteQuery->delete($db->quoteName('#__tj_notification_template_configs'));
				$deleteQuery->where($conditions);
				$db->setQuery($deleteQuery);

				$db->execute();
			}
		}
	}

	/**
	 * Method to replace tags if they are changed
	 *
	 * @param   array   $template  This is single template array of data
	 * @param   string  $client    client like com_jgive
	 *
	 * @return  void
	 *
	 * @since   2.0.1
	 */
	public function updateTemplates($template, $client)
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'key')));
		$query->from($db->quoteName('#__tj_notification_templates', 'temp'));
		$query->order($db->quoteName('id') . ' ASC');
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('key') . ' = ' . $db->quote($template['key']));
		$db->setQuery($query);
		$templateKeyIdObj = $db->loadObject();

		if (!empty($templateKeyIdObj))
		{
			$query = $db->getQuery(true);
			$query->select('backend');
			$query->from($db->quoteName('#__tj_notification_template_configs', 'con'));
			$query->where($db->quoteName('con.template_id') . ' = ' . (int) $templateKeyIdObj->id);
			$db->setQuery($query);
			$existingBackends = $db->loadColumn();

			$backendsArray          = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);
			$remainTemplateBackends = array_values(array_diff($backendsArray, $existingBackends));

			if (!empty($remainTemplateBackends))
			{
				foreach ($remainTemplateBackends as $key => $value)
				{
					if ($template['key'] == $templateKeyIdObj->key)
					{
						$db    = JFactory::getDBO();
						$templateConfigTable = JTable::getInstance('Template', 'TjnotificationTable', array('dbo', $db));
						$templateConfigTable->template_id = $templateKeyIdObj->id;
						$templateConfigTable->backend     = $value;
						$templateConfigTable->subject     = (!empty($template[$value][$value . 'fields'][$value . 'fields0']['subject']))
							? $template[$value][$value . 'fields'][$value . 'fields0']['subject'] : '';
						$templateConfigTable->body        = $template[$value][$value . 'fields'][$value . 'fields0']['body'];
						$templateConfigTable->state       = $template[$value]['state'];
						$templateConfigTable->created_on  = Factory::getDate('now')->toSQL();
						$templateConfigTable->updated_on  = '';
						$templateConfigTable->is_override = 0;
						$templateConfigTable->save($templateConfigTable);
					}
				}
			}
		}
	}
}
