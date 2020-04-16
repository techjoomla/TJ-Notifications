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
use \Joomla\CMS\Date\Date;

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
		$date = new Date;

		if (empty($data['created_on']))
		{
			$data['created_on'] = $date->format(Text::_('DATE_FORMAT_FILTER_DATETIME'));
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

		$query->select($db->quoteName('ntc.replacement_tags'));
		$query->from('#__tj_notification_template_configs AS ntc');
		$query->join('LEFT', '#__tj_notification_templates AS nt ON nt.id = ntc.template_id');
		$query->where($db->quoteName('nt.client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('nt.key') . ' = ' . $db->quote($key));
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
	 * Method to  save notification date
	 *
	 * @param   ARRAY  $data  notification data
	 *
	 * @return  mixed Template ID
	 *
	 * @since    1.0.0
	 */
	public function save($data)
	{
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
		}

		if (!parent::save($data))
		{
			return false;
		}

		// Get current Template id
		$templateId = (int) $this->getState($this->getName() . '.id');
		$db    = Factory::getDBO();

		if (empty($templateId))
		{
			return false;
		}

		$params = array();

		foreach ($data as $datakey => $record)
		{
			//created and updated date
			$created_on = $data['created_on'];
			$updated_on = $data['updated_on'];

			// For email provider
			if ($datakey == 'email')
			{
				//To delete the language, body and subject on editing the template.
				if (count($record['emailfields']) != 0)
				{
					$deleteid = array();
					$storeid  = array();

					$existEmailTemplates = $this->getExistingTemplates($data['id'],$datakey);

					$templateconfig = array();

					// To check which email template must be deleted
					foreach ($record['emailfields'] as $key=>$emailFieldsInfo)
					{
						foreach ($existEmailTemplates as $existEmailTemplate)
						{
							$existingEmailtemplateId   = json_decode(json_encode($existEmailTemplate->id), true);
							$existingEmailtemplateLang = json_decode(json_encode($existEmailTemplate->language), true);

							if (($existingEmailtemplateId == $emailFieldsInfo['id'] || $existingEmailtemplateLang == "*" ) ||$existingEmailtemplateId == empty($emailFieldsInfo['id']))
							{
								$storeid[] = $emailFieldsInfo['id'];
							}
							else
							{
								$deleteid[] = $existingEmailtemplateId;
							}
						}
					}

					// Array of emailtemplate id to be deleted
					$deleteEmailTemplates = array_diff($deleteid,$storeid);

					//function call to delete email template
					$this->deleteTemplate($deleteEmailTemplates);
				}

				// Foreach to save the repeatable email fields
				foreach ($record['emailfields'] as $key=>$arrayvalue)
				{
					$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));
					$templateConfigTable->load(array('template_id' => $templateId, 'provider' => $key));

					$templateConfigTable->template_id = $templateId;
					$templateConfigTable->provider    = $datakey;
					$templateConfigTable->subject     = $arrayvalue['subject'];
					$templateConfigTable->body        = $arrayvalue['body'];
					$templateConfigTable->language    = $arrayvalue['language'];

					$existEmailFields = $this->getExistingTemplates($data['id'],$datakey);

					// To check whether there is existing template for particular template and language.
					foreach ($existEmailFields   as $existEmailField)
					{
						$existingLang   = json_decode(json_encode($existEmailField->language), true);
						$existingTempId = json_decode(json_encode($existEmailField->template_id), true);
						$existingId     = json_decode(json_encode($existEmailField->id), true);

						// If there is existing template then return to form view.
						if ($existingLang == $arrayvalue['language'] && $existingTempId == $templateId && $existingId != $arrayvalue['id'])
						{
							return false;
						}
					}

					if (!empty($record['cc']))
					{
						$params['cc'] = $record['cc'];
					}

					if (!empty($record['bcc']))
					{
						$params['bcc'] = $record['bcc'];
					}

					if (!empty($record['from_name']))
					{
						$params['from_name'] = $record['from_name'];
					}

					if (!empty($record['from_email']))
					{
						$params['from_email'] = $record['from_email'];
					}

					$templateConfigTable->params = json_encode($params);

					if (!empty($record['replacement_tags']))
					{
						$templateConfigTable->replacement_tags = json_encode($record['replacement_tags']);
					}

					$templateConfigTable->state      = $record['state'];
					$templateConfigTable->created_on = $created_on;
					$templateConfigTable->updated_on = $updated_on;

					// Save provider in config table
					if (empty($arrayvalue['id']))
					{
						$templateConfigTable->save($templateConfigTable);
					}
					else
					{
						$templateConfigTable->id = $arrayvalue['id'];
						$templateConfigTable->save($templateConfigTable);
					}
				}
			}

			// To save sms field
			if ($datakey == "sms")
			{
				//To delete the language, body and subject on edit.
				if (count($record['smsfields']) != 0)
				{
					$deleteSmsId = array();
					$storeSmsId  = array();

					// Fetch existing sms templates
					$existSmsTemplates = $this->getExistingTemplates($data['id'],$datakey);

					$templateconfig = array();

					// Foreach loop to check which template must be deleted
					foreach ($record['smsfields'] as $key=>$smsFieldsInfo)
					{
						foreach ($existSmsTemplates as $ExistSmsTemplate)
						{
							$existSmsId    = json_decode(json_encode($ExistSmsTemplate->id), true);
							$existLanguage = json_decode(json_encode($ExistSmsTemplate->language), true);

							if (($existSmsId == $smsFieldsInfo['id'] || $existLanguage == "*" ) || $existSmsId == empty($smsFieldsInfo['id']))
							{
								$storeSmsId[] = $smsFieldsInfo['id'];
							}
							else
							{
								$deleteSmsId[] = $existSmsId;
							}
						}
					}

					// Array of smstemplate id to be deleted
					$deleteSmsTemplates = array_diff($deleteSmsId,$storeSmsId);

					//function call to delete sms template
					$this->deleteTemplate($deleteSmsTemplates);
				}

				// To save repeatable smsfields
				foreach ($record['smsfields'] as $key=>$smsArrayvalues)
				{
					$templateConfigTable = Table::getInstance('Template', 'TjnotificationTable', array('dbo', $db));
					$templateConfigTable->load(array('template_id' => $templateId, 'provider' => $key));

					$templateConfigTable->template_id = $templateId;
					$templateConfigTable->provider    = $datakey;

					$templateConfigTable->body     = $smsArrayvalues['body'];
					$templateConfigTable->language = $smsArrayvalues['language'];

					// To get existing sms templates 
					$existSmsFields = $this->getExistingTemplates($data['id'],$datakey);

					// To check whether there is existing template for particular template and language. 
					foreach ($existSmsFields as $existSmsField)
					{
						$existingSmsLang   = json_decode(json_encode($existSmsField->language), true);
						$existingSmsTempId = json_decode(json_encode($existSmsField->template_id), true);
						$existingSmsId     = json_decode(json_encode($existSmsField->id), true);

						// If there is existing template then return to form view.
						if ($existingSmsLang == $smsArrayvalues['language'] && $existingSmsTempId == $templateId && $existingSmsId != $smsArrayvalues['id'])
						{
							return false;
						}
					}

					if (!empty($record['replacement_tags']))
					{
						$templateConfigTable->replacement_tags = json_encode($record['replacement_tags']);
					}

					$templateConfigTable->state      = $record['state'];
					$templateConfigTable->created_on = $created_on;
					$templateConfigTable->updated_on = $updated_on;

					// // Save provider in config table to save data in new id or existing id
					 if (empty($smsArrayvalues['id']))
					 {
					 	$templateConfigTable->save($templateConfigTable);
					 }
					 else
					 {
					 	$templateConfigTable->id = $smsArrayvalues['id'];
						$templateConfigTable->save($templateConfigTable);
					 }
				}
			}
		}

		return $templateId;
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

		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('ntc.*');
		$query->from($db->qn('#__tj_notification_template_configs', 'ntc'));
		$query->where($db->qn('ntc.template_id') . '=' . (int) $item->id);
		$db->setQuery($query);
		$templateConfigs = $db->loadObjectlist();

		$providerConfigs = array();

		foreach ($templateConfigs as $key => $tConfig)
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

			$providerConfigs['is_override'] = $tConfig->is_override;
			$providerConfigs['replacement_tags'] = $tConfig->replacement_tags;

			$provider        = $tConfig->provider;
			$item->$provider = $providerConfigs;

			foreach (tjNotificationsConstantProvidersArray as $keyProvider => $provider)
			{
				$db       = Factory::getDBO();
				$smsquery = $db->getQuery(true);
				$smsquery->select('ntc.*');
				$smsquery->where($db->qn('ntc.template_id') . '=' . (int) $item->id . ' AND ' . $db->quoteName('provider') . " = '" .  $provider  . "'");
				$smsquery->from($db->qn('#__tj_notification_template_configs', 'ntc'));

				$db->setQuery($smsquery);
				if ($provider == "email")
				{
					$emailInfoList              = $db->loadObjectlist();
					$item->email['emailfields'] = $emailInfoList ;
				}
				else
				{
					$smsInfoList            = $db->loadObjectlist();
					$item->sms['smsfields'] = $smsInfoList;
				}
			}

		}

		return $item;
	}

	/**
	 * get notification templates of component
	 *
	 * @param   integer  $templateId  id and $provider for the event in integration table
	 *
	 * @return array.
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
		$query->where($db->quoteName('template_id') . ' = ' . $db->quote($templateId) . ' AND ' . $db->quoteName('provider') . " = '" . $provider  . "'");
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to delete templates if they are deleted from form view
	 *
	 * @param   array  $data  template data
	 *
	 * @return  void
	 *
	 * @since   1.0.4
	 */
	public function deleteTemplate($templatesTodelete)
	{
		if ($templatesTodelete)
		{
			foreach ($templatesTodelete as $templateTodelete)
			{
				$db          = Factory::getDBO();
				$deleteQuery = $db->getQuery(true);
				$deleteQuery = $db->getQuery(true);
				$conditions  = array(
				$db->qn('id') . '=' . (int) $templateTodelete);
				$deleteQuery->delete($db->quoteName('#__tj_notification_template_configs'));
				$deleteQuery->where($conditions);
				$db->setQuery($deleteQuery);

				$db->execute();
			}
		}
	}
}
