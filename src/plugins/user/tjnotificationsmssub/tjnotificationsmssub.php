<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.tjnotificationsmssub
 *
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/models', 'SubscriptionsModel');

/**
 * Class for Tjnotificationsmssub User Plugin
 *
 * @since  1.0.0
 */
class PlgUserTjnotificationsmssub extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 *
	 * @since  3.2.11
	 */
	protected $autoloadLanguage = true;

	/**
	 * Utility method to act on a user after it has been saved.
	 *
	 * This method sends a registration email to new users created in the backend.
	 *
	 * @param   array    $user     Holds the new user data.
	 * @param   boolean  $isNew    True if a new user is stored.
	 * @param   boolean  $success  True if user was successfully stored in the database.
	 * @param   string   $msg      Message.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function onUserAfterSave($user, $isNew, $success, $msg)
	{
		$notificationsParams = JComponentHelper::getParams('com_tjnotifications');
		$phoneSetting = $notificationsParams->get('tjsms_phone_number');
		$phoneField = $notificationsParams->get('tjsms_joomla_field');

		if ($phoneSetting == 'joomla')
		{
			if (!empty($phoneField))
			{
				if (strpos($phoneField, 'com_fields') !== false)
				{
					$phoneNumber = $user['com_fields'][str_replace("com_fields.", "", $phoneField)];
				}
				else
				{
					$phoneNumber = $user[$phoneField];
				}
			}

			$title       = $user['name'];
			$userId      = $user['id'];
			$backend     = 'sms';
			$address     = $phoneNumber;
			$state       = $user['block'] ? '0' : '1';
			$isConfirmed = '1';

			// GLOBAL - Get TJNotifications subscriptions details for current user
			$model = ListModel::getInstance('Subscriptions', 'TjnotificationsModel', array('ignore_request' => true));
			$model->setState('filter.backend', $backend);
			$model->setState('filter.user_id', $userId);
			$userSubscriptions = $model->getItems();

			if (empty($userSubscriptions))
			{
				$method = 'add';
			}
			else
			{
				$method = 'update';
			}

			Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
			$table = Table::getInstance('Subscription', 'TjnotificationsTable');

			$user = Factory::getUser();

			$data = array (
				'user_id'      => $userId,
				'title'        => $title,
				'backend'      => $backend,
				'address'      => $address,
				'state'        => $state,
				'is_confirmed' => $isConfirmed
			);

			$date = Factory::getDate();

			if ($method == 'update')
			{
				$data['id']          = $userSubscriptions[0]->id;
				$data['modified_by'] = $user->id;
				$data['updated_on']  = $date->toSql(true);
			}
			else
			{
				$data['created_by'] = $user->id;
				$data['created_on'] = $date->toSql(true);
			}

			try
			{
				$table->bind($data);

				// Check and store the object.
				if (!$table->check())
				{
					ApiError::raiseError(400, Text::_($table->getError()));
				}

				// Store the coupon item in the database
				$result = $table->store();

				// Set the id for the coupon object in case we created a new coupon.
				if ($result)
				{
					$table->load($table->id);
				}
			}
			catch (\Exception $e)
			{
				ApiError::raiseError(400, Text::_($e->getMessage()));
			}
		}
	}
}
