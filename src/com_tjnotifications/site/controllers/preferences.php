<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_tjnotification
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * This controller to redirect to model of tjnotification.
 *
 * @since  1.6
 */
class TJNotificationsControllerPreferences extends JControllerForm
{
	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   12.2
	 */
	public function save($key = null, $urlVar = '')
	{
		$jinput = JFactory::getApplication()->input;
		$clientName = $jinput->get('client_name', '');
		$user = JFactory::getUser();
		$id = $user->id;
		$providerName = $jinput->get('provider_name', '');
		$key = $jinput->get('key', '');
		$data = array (
						'user_id'	=> $id,
						'client'  => $clientName,
						'provider' => $providerName,
						'key'	  => $key
					);
		$model = $this->getModel('Preferences', 'TJNotificationsModel');
		$result = $model->save($data);
		echo json_encode($result);
		jexit();
	}

	/**
	 * Method to delete the model state.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function delete()
	{
		$jinput = JFactory::getApplication()->input;
		$clientName = $jinput->get('client_name', '');
		$user = JFactory::getUser();
		$id = $user->id;
		$providerName = $jinput->get('provider_name', '');
		$key = $jinput->get('key', '');
		$data = array (
						'user_id'	=> $id,
						'client'  => $clientName,
						'provider' => $providerName,
						'key'	  => $key
					);
		$model = $this->getModel('Preferences', 'TJNotificationsModel');
		$result = $model->deletePreference($data);
		echo json_encode($result);
		jexit();
	}
}
