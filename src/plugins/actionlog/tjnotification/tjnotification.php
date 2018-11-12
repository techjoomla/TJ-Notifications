<?php
/**
 * @package     TJ_Notification
 * @subpackage  Actionlog.tjnotification
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');
use Joomla\CMS\Factory;
/**
 * TJ Notification Actions Logging Plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
class PlgActionlogTjnotification extends JPlugin
{
	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  __DEPLOY_VERSION__
	 */
	protected $autoloadLanguage = true;

	/**
	 * On unsubscribing notification logging method
	 *
	 * Method is called after user unsubscribes for notification.
	 *
	 * @param   array  $data  com_tjnotification.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjnOnAfterUnsubscribeNotification($data)
	{
		if (!$this->params->get('logActionForNotificationSubscription', 1))
		{
			return;
		}

		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjnotifications/tables');
		$tjTableTemplate   = JTable::getInstance('notification', 'TjnotificationTable', array());
		$tjTableTemplate->load(array('key' => $data['key']));
		$context = Factory::getApplication()->input->get('option');
		$userId = $data["user_id"];

		if ($data["client"])
		{
			$language = Factory::getLanguage();
			$language->load($data["client"]);
		}

		$user = Factory::getUser($userId);
		$messageLanguageKey = 'PLG_ACTIONLOG_TJNOTIFICATION_NOTIFICATION_UNSUBSCRIBE';
		$message = array(
			'type'        => $data["provider"],
			'userid'      => $userId,
			'username'    => $user->name,
			'key'         => $data["key"],
			'client'      => JText::_(strtoupper($data["client"])),
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'keylink'     => 'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $tjTableTemplate->id
		);
		$this->addLog(array($message), $messageLanguageKey, $context, $userId);
	}

	/**
	 * Proxy for ActionlogsModelUserlog addLog method
	 *
	 * This method adds a record to #__action_logs contains (message_language_key, message, date, context, user)
	 *
	 * @param   array   $messages            The contents of the messages to be logged
	 * @param   string  $messageLanguageKey  The language key of the message
	 * @param   string  $context             The context of the content passed to the plugin
	 * @param   int     $userId              ID of user perform the action, usually ID of current logged in user
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */

	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		JLoader::register('ActionlogsModelActionlog', JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php');

		/* @var ActionlogsModelActionlog $model */
		$model = JModelLegacy::getInstance('Actionlog', 'ActionlogsModel');
		$model->addLog($messages, $messageLanguageKey, $context, $userId);
	}

	/**
	 * On saving notification template logging method
	 *
	 * Method is called after notification template is created or updated.
	 *
	 * @param   Object   $data      Holds the template data.
	 * @param   Object   $recordId  Holds the template data.
	 * @param   integer  $isNew     Gives the value of template id if present.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */

	public function tjnOnAfterSaveNotificationTemplate($data, $recordId, $isNew)
	{
		if (!$this->params->get('logActionForNewNotificationTemplate', 1))
		{
			return;
		}

		$context = Factory::getApplication()->input->get('option');
		$user = Factory::getUser();
		$userId = $user->id;
		$userName = $user->name;

		if ($recordId->client)
		{
			$language = Factory::getLanguage();
			$language->load($recordId->client);
		}

		if ($isNew)
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJNOTIFICATION_TEMPLATE_ADD';
		}
		else
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_TJNOTIFICATION_TEMPLATE_UPDATE';
		}

		$message = array(
			'title'       => $recordId->title,
			'userid'      => $userId,
			'username'    => $userName,
			'client'      => JText::_(strtoupper($recordId->client)),
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'keylink'     => 'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $recordId->id
		);
		$this->addLog(array($message), $messageLanguageKey, $context, $userId);
	}

	/**
	 * On deleting notification template logging method
	 *
	 * Method is called after notification template is deleted.
	 *
	 * @param   Object  $data  Holds the template data.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */

	public function tjnOnAfterDeleteNotificationTemplate($data)
	{
		if (!$this->params->get('logActionForDeleteNotificationTemplate', 1))
		{
			return;
		}

		$context = JFactory::getApplication()->input->get('option');
		$user = Factory::getUser();
		$userId = $user->id;

		if ($data->client)
		{
			$language = Factory::getLanguage();
			$language->load($data->client);
		}

		$userName = $user->name;
		$messageLanguageKey = 'PLG_ACTIONLOG_TJNOTIFICATION_TEMPLATE_DELETE';

		$message = array(
			'title'       => $data->title,
			'userid'      => $userId,
			'username'    => $userName,
			'client'      => JText::_(strtoupper($data->client)),
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,

		);
		$this->addLog(array($message), $messageLanguageKey, $context, $userId);
	}

	/**
	 * On resubscribing notification logging method
	 *
	 * Method is called after user resubscribes for notification.
	 *
	 * @param   array  $data  com_tjnotification.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function tjnOnAfterResubscribeNotification($data)
	{
		if (!$this->params->get('logActionForNotificationSubscription', 1))
		{
			return;
		}

		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjnotifications/tables');
		$tjTableTemplate   = JTable::getInstance('notification', 'TjnotificationTable', array());
		$tjTableTemplate->load(array('key' => $data['key']));
		$context = Factory::getApplication()->input->get('option');
		$userId = $data["user_id"];

		if ($data["client"])
		{
			$language = Factory::getLanguage();
			$language->load($data["client"]);
		}

		$user = Factory::getUser($userId);
		$messageLanguageKey = 'PLG_ACTIONLOG_TJNOTIFICATION_NOTIFICATION_RESUBSCRIBE';
		$message = array(
			'type'        => $data["provider"],
			'userid'      => $userId,
			'username'    => $user->name,
			'key'         => $data["key"],
			'client'      => JText::_(strtoupper($data["client"])),
			'accountlink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'keylink'     => 'index.php?option=com_tjnotifications&view=notification&layout=edit&id=' . $tjTableTemplate->id
		);
		$this->addLog(array($message), $messageLanguageKey, $context, $userId);
	}
}
