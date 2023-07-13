<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Plugin\PluginHelper;
use \Joomla\CMS\Table\Table;

/**
 * TJNotification model.
 *
 * @since  1.6
 */
class TJNotificationsModelPreferences extends Joomla\CMS\MVC\Model\AdminModel
{
	/**
	 * Method to getClient the form data.
	 *
	 * @return array
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function getClient()
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('DISTINCT(client)');
		$query->from($db->quoteName('#__tj_notification_templates'));
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to get keys the form data.
	 *
	 * @param   string  $client  Client
	 *
	 * @return  array
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function Keys($client)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('DISTINCT(`key`)');
		$query->from($db->quoteName('#__tj_notification_templates'));
		$query->where($db->quoteName('client') . ' = ' . $db->quote($client));
		$query->where($db->quoteName('user_control') . ' = 1');
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to getState the form data.
	 *
	 * @return  array|boolean
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function getStates()
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$uid   = Factory::getUser()->id;
		$query->select('client,`key`,provider');
		$query->from($db->quoteName('#__tj_notification_user_exclusions'));

		if ($uid)
		{
			$query->where($db->quoteName('user_id') . ' = ' . $db->quote($uid));
		}

		$db->setQuery($query);
		$preferences = $db->loadObjectList();

		if ($preferences)
		{
			return $preferences;
		}

		return false;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		if ($data)
		{
			parent::save($data);
			PluginHelper::importPlugin('tjnotification');
			Factory::getApplication()->triggerEvent('onAfterTjnUnsubscribeNotification', array($data));

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to delete data
	 *
	 * @param   array  &$data  Data to be deleted
	 *
	 * @return bool|int If success returns the id of the deleted item, if not false
	 *
	 * @throws Exception
	 */
	public function deletePreference(&$data)
	{
		if ($data)
		{
			PluginHelper::importPlugin('tjnotification');
			Factory::getApplication()->triggerEvent('onAfterTjnResubscribeNotification', array($data));
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('user_id') . ' = ' . $db->quote($data['user_id']),
				$db->quoteName('client') . ' = ' . $db->quote($data['client']),
				$db->quoteName('key') . ' = ' . $db->quote($data['key']),
				$db->quoteName('provider') . ' = ' . $db->quote($data['provider'])
			);
			$query->delete($db->quoteName('#__tj_notification_user_exclusions'));
			$query->where($conditions);

			$db->setQuery($query);

			return $db->execute();
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm|boolean
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_tjnotifications.preferences',
			'preferences',
			array(
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
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the Table class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for Table object
	 *
	 * @return  Table|boolean Table if found, boolean false on failure
	 */

	public function getTable($type ='Preferences', $prefix = 'TJNotificationTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to count the form data.
	 *
	 * @return preferences
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function count()
	{
		// Initialize variables.
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('COUNT(*) as name');
		$query->from($db->quoteName('#__tj_notification_user_exclusions'));
		$query->where($db->quoteName('provider') . ' = ' . $db->quote('email'));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Method to get keys the form data.
	 *
	 * @param   string  $provider  The form data
	 *
	 * @return  array
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function adminPreferences($provider)
	{
		$db       = Factory::getDbo();
		$query    = $db->getQuery(true);
		$provider = strtolower($provider);
		$query->select('client,`key`');
		$query->from($db->quoteName('#__tj_notification_templates','tnt'));
		$query->join('INNER', $db->qn('#__tj_notification_template_configs', 'tntc') . ' ON (' . $db->qn('tntc.template_id') . ' = ' . $db->qn('tnt.id') . ')');
		$query->where($db->quoteName('tntc.backend') . '=' . $db->quote($provider));
		$query->where($db->quoteName('tntc.state') . '=' . $db->quote('1'));
		// print_r($query->dump());die;
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array  $client  An optional array of data for the form to interogate.
	 * @param   array  $key     True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getUnsubscribedUsers($client,$key)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select('user_id');
		$query->from($db->quoteName('#__tj_notification_user_exclusions'));
		$query->where(
					array(
							$db->quoteName('client') . ' = ' . $db->quote($client),
							$db->quoteName('key') . ' = ' . $db->quote($key)
							)
						);
		$db->setQuery($query);
		$userIds = $db->loadObjectList();
		$unsubscribed_users = array();

		foreach ($userIds as $userId)
		{
			$unsubscribed_users[] = $userId->user_id;
		}

		return $unsubscribed_users;
	}

	/**
	 * Method to get notifications unsubscribed by users
	 *
	 * @param   int     $userId  User id
	 * @param   string  $client  Client
	 * @param   string  $key     Template key
	 *
	 * @return    array
	 *
	 * @since    2.0.0
	 */
	public function getUnsubscribedListByUser($userId, $client, $key)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Create the base select statement.
		$query->select(array('user_id', 'provider'));
		$query->from($db->quoteName('#__tj_notification_user_exclusions'));
		$query->where(
			array(
				$db->quoteName('user_id') . ' = ' . (int) $userId,
				$db->quoteName('client') . ' = ' . $db->quote($client),
				$db->quoteName('key') . ' = ' . $db->quote($key)
			)
		);

		$db->setQuery($query);

		return $db->loadObjectList('provider');
	}
}
