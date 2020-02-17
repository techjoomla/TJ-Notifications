<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;

/**
 * This controller to redirect to model of tjnotification.
 *
 * @since  1.6
 */
class TJNotificationsControllerPreferences extends \Joomla\CMS\MVC\Controller\FormController
{
	/**
	 * Method to save the model state.
	 *
	 * @param   string  $key     key
	 * @param   string  $urlVar  urlVar
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = '')
	{
		$jinput       = Factory::getApplication()->input;
		$clientName   = $jinput->get('client_name', '');
		$user         = Factory::getUser();
		$id           = $user->id;
		$providerName = $jinput->get('provider_name', '');
		$key          = $jinput->get('key', '');
		$data = array (
						'user_id'	=> $id,
						'client'  => $clientName,
						'provider' => $providerName,
						'key'	  => $key
					);
		$app   = Factory::getApplication();
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
		$jinput       = Factory::getApplication()->input;
		$clientName   = $jinput->get('client_name', '');
		$user         = Factory::getUser();
		$id           = $user->id;
		$providerName = $jinput->get('provider_name', '');
		$key          = $jinput->get('key', '');
		$data = array (
						'user_id'	=> $id,
						'client'  => $clientName,
						'provider' => $providerName,
						'key'	  => $key
					);
		$app    = Factory::getApplication();
		$model  = $this->getModel('Preferences', 'TJNotificationsModel');
		$result = $model->deletePreference($data);
		echo json_encode($result);
		jexit();
	}
}
