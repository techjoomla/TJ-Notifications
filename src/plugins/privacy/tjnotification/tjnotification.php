<?php
/**
 * @package     TJNotifications
 * @subpackage  Privacy.tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

// No direct access.
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

use Joomla\Utilities\ArrayHelper;
JLoader::register('PrivacyPlugin', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/plugin.php');
JLoader::register('PrivacyRemovalStatus', JPATH_ADMINISTRATOR . '/components/com_privacy/helpers/removal/status.php');

/**
 * Privacy plugin managing TJNotifications user data
 *
 * @since  1.0.2
 */
class PlgPrivacyTjnotification extends PrivacyPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since   1.0.2
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0.2
	 */
	protected $db;

	/**
	 * Reports the privacy related capabilities for this plugin to site administrators.
	 *
	 * @return  array
	 *
	 * @since   1.0.2
	 */
	public function onPrivacyCollectAdminCapabilities()
	{
		$this->loadLanguage();

		return array(
			JText::_('PLG_PRIVACY_TJNOTIFICATION') => array(
				JText::_('PLG_PRIVACY_TJNOTIFICATION_PRIVACY_CAPABILITY_USER_DETAIL')
			)
		);
	}

	/**
	 * Processes an export request for TJNotifications user data
	 *
	 * This event will collect data for the following tables:
	 *
	 * - #__notification_user_exclusions
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyExportDomain[]
	 *
	 * @since   1.0.2
	 */
	public function onPrivacyExportRequest(PrivacyTableRequest $request, JUser $user = null)
	{
		if (!$user)
		{
			return array();
		}

		/** @var JTableUser $userTable */
		$userTable = JUser::getTable();
		$userTable->load($user->id);

		$domains = array();

		// Create the domain for the JMailAlerts User Subscription data
		$domains[] = $this->createTJNotificationsUnsubscriptionDomain($userTable);

		return $domains;
	}

	/**
	 * Create the domain for the TJNotifications User data
	 *
	 * @param   JTableUser  $user  The JTableUser object to process
	 *
	 * @return  PrivacyExportDomain
	 *
	 * @since   1.0.2
	 */
	private function createTJNotificationsUnsubscriptionDomain(JTableUser $user)
	{
		$domain = $this->createDomain('TJNotifications unsubscription', 'TJNotifications unsubscription data');

		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__tj_notification_user_exclusions'))
			->where(
						$this->db->quoteName('user_id') . ' = ' . $this->db->quote($user->id)
				);
		$userUnsubscriptionData = $this->db->setQuery($query)->loadAssocList();

		if (!empty($userUnsubscriptionData))
		{
			foreach ($userUnsubscriptionData as $unsubscriptionData)
			{
				$domain->addItem($this->createItemFromArray($unsubscriptionData, $unsubscriptionData['user_id']));
			}
		}

		return $domain;
	}

	/**
	 * Performs validation to determine if the data associated with a remove information request can be processed
	 *
	 * This event will not allow a super user account to be removed
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  PrivacyRemovalStatus
	 *
	 * @since   1.0.2
	 */
	public function onPrivacyCanRemoveData(PrivacyTableRequest $request, JUser $user = null)
	{
		$status = new PrivacyRemovalStatus;

		if (!$user)
		{
			return $status;
		}

		return $status;
	}

	/**
	 * Removes the data associated with a remove information request
	 *
	 * This event will remove the user data
	 *
	 * @param   PrivacyTableRequest  $request  The request record being processed
	 * @param   JUser                $user     The user account associated with this request if available
	 *
	 * @return  void
	 *
	 * @since   1.0.2
	 */
	public function onPrivacyRemoveData(PrivacyTableRequest $request, JUser $user = null)
	{
		// This plugin only processes data for registered user accounts
		if (!$user)
		{
			return;
		}

		// If there was an error loading the user do nothing here
		if ($user->guest)
		{
			return;
		}

		$db = $this->db;

		// Delete TJNotification user data :
		$query = $db->getQuery(true)
				->delete($db->quoteName('#__tj_notification_user_exclusions'))
				->where('user_id = ' . $user->id);
		$db->setQuery($query);
		$db->execute();
	}
}
