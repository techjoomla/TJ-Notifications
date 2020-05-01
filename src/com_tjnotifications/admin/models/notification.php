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

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Date\Date;

jimport('joomla.application.component.model');
BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models');

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

		// 2 - save provider specific config
		foreach (TJNOTIFICATIONS_CONST_PROVIDERS_ARRAY as $keyProvider => $provider)
		{
			// 2.1 Check if current provider exists in posted data
			// If not $data['email'] or $data['sms']
			if (empty($data[$provider]))
			{
				continue;
			}

			// Eg: Get count of $data['email']['emailfields'] or $data['sms']['smsfields']
			if (count($data[$provider][$provider . 'fields']) == 0)
			{
				continue;
			}

			$idsToBeDeleted = array();
			$idsToBeStored  = array();

			// For current template, get existing lang. sepcific template for current provider
			$existingProviderConfigs = $this->getExistingTemplates($data['id'], $provider);

			// 2.2 Find existing template config entries to be deleted (i.e. language specific templates removed by user)
			foreach ($data[$provider][$provider . 'fields'] as $providerName => $providerFieldValues)
			{
				// Iterate through each lang. specific config entry
				foreach ($existingProviderConfigs as $existingProviderConfig)
				{
					$existingProviderConfigId     = json_decode(json_encode($existingProviderConfig->id), true);
					$existingProviderConfigTempId = json_decode(json_encode($existingProviderConfig->template_id), true);
					$existingProviderConfigLang   = json_decode(json_encode($existingProviderConfig->language), true);

					// If there is existing template then return to form view.
					if ($existingProviderConfigLang == $providerFieldValues['language']
						&& $existingProviderConfigTempId == $templateId
						&& $existingProviderConfigId != $providerFieldValues['id'])
					{
						$this->setError(Text::_('COM_TJNOTIFICATIONS_TEMPLATE_ERR_MSG_TEMPLATE_EXISTS'));

						return false;
					}

					// Find to be deleted or saved
					if (($existingProviderConfigId == $providerFieldValues['id'] || $existingProviderConfigLang == "*" )
						|| $existingProviderConfigId == empty($providerFieldValues['id']))
					{
						$idsToBeStored[] = $providerFieldValues['id'];
					}
					else
					{
						$idsToBeDeleted[] = $existingProviderConfigId;
					}
				}
			}

			// Array of provider specific template configs id to be deleted
			$providerConfigIdsToBeDeleted = array_diff($idsToBeDeleted, $idsToBeStored);

			// Function call to delete template configs
			$this->deleteProviderConfigs($providerConfigIdsToBeDeleted);

			// 2.3 Common data for saving
			$createdOn = !empty($data['created_on']) ? $data['created_on'] : '';
			$updatedOn = !empty($data['updated_on']) ? $data['updated_on'] : '';

			// 2.4 try saving all provider specific configs
			// This has repeatable data eg: $data['email']['emailfields'] or $data['sms']['smsfields']
			foreach ($data[$provider][$provider . 'fields'] as $providerName => $providerFieldValues)
			{
				$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));
				$templateConfigTable->load(array('template_id' => $templateId, 'provider' => $providerName));

				// Non-repeat data
				$templateConfigTable->template_id = $templateId;
				$templateConfigTable->provider    = $provider;
				$templateConfigTable->state       = $data[$provider]['state'];
				$templateConfigTable->created_on  = $createdOn;
				$templateConfigTable->updated_on  = $updatedOn;

				// Get params data
				// State, emailfields / smsfields
				$nonParamsFields = array('state', $provider . 'fields');
				$params = array();

				foreach ($data[$provider] as $fieldKey => $fieldValue)
				{
					if (!in_array($fieldKey, $nonParamsFields) && !empty($data[$provider][$fieldKey]))
					{
						$params[$fieldKey] = $data[$provider][$fieldKey];
					}
				}

				$templateConfigTable->params = json_encode($params);

				// Repeatable data
				$templateConfigTable->subject     = !empty($providerFieldValues['subject']) ? $providerFieldValues['subject']: '';
				$templateConfigTable->body        = $providerFieldValues['body'];
				$templateConfigTable->language    = $providerFieldValues['language'];

				// Save provider in config table
				if (empty($providerFieldValues['id']))
				{
					$templateConfigTable->save($templateConfigTable);
				}
				else
				{
					$templateConfigTable->id = $providerFieldValues['id'];
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

		foreach (TJNOTIFICATIONS_CONST_PROVIDERS_ARRAY as $keyProvider => $provider)
		{
			$db                   = Factory::getDBO();
			$providerConfigsQuery = $db->getQuery(true);

			$providerConfigsQuery->select('ntc.*');
			$providerConfigsQuery->where(
				$db->qn('ntc.template_id') . '=' . (int) $item->id .
				' AND ' . $db->quoteName('provider') . " = '" . $provider . "'"
			);
			$providerConfigsQuery->from($db->qn('#__tj_notification_template_configs', 'ntc'));
			$db->setQuery($providerConfigsQuery);
			$providerConfigsList = $db->loadObjectlist();

			if (empty($providerConfigsList))
			{
				continue;
			}

			// We can get common config for current provider from any provider config for current template ID
			$singleProviderRow = $providerConfigsList[0];

			// Define non-params fields - state, emailfields / smsfields
			$nonParamsFields  = array('state', $provider . 'fields');

			// Start setting provider specific data for edit
			$item->$provider          = array();
			$item->$provider['state'] = $singleProviderRow->state;

			// Get params for current provider from any provider config for current template ID
			$json = (array) json_decode($singleProviderRow->params);

			foreach ($json as $fieldKey => $fieldValue)
			{
				if (!in_array($fieldKey, $nonParamsFields))
				{
					$item->$provider[$fieldKey] = $fieldValue;
				}
			}

			// Last, set all config rows list as repeatable data
			$item->$provider[$provider . 'fields'] = $providerConfigsList;
		}

		return $item;
	}

	/**
	 * get notification templates of component
	 *
	 * @param   integer  $templateId  Notification template ID
	 * @param   string   $provider    Provider name
	 *
	 * @return array
	 *
	 * @since  2.1
	 */
	public function getExistingTemplates($templateId, $provider)
	{
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select('`id`,`language`,`template_id`');
		$query->from($db->quoteName('#__tj_notification_template_configs'));
		$query->where(
			$db->quoteName('template_id') . ' = ' . $db->quote($templateId) .
			' AND ' . $db->quoteName('provider') . " = '" . $provider . "'"
		);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to delete templates if they are deleted from form view
	 *
	 * @param   array  $providerConfigIdsToBeDeleted  template data
	 *
	 * @return  void
	 *
	 * @since   1.0.4
	 */
	public function deleteProviderConfigs($providerConfigIdsToBeDeleted)
	{
		if (!empty($providerConfigIdsToBeDeleted))
		{
			foreach ($providerConfigIdsToBeDeleted as $entry)
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
}
