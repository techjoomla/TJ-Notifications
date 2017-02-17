<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Notifications list controller class.
 *
 * @since  0.0.1
 */
class TjnotificationsControllerNotifications extends JControllerAdmin
{
/**
	* Proxy for getModel.
	*
	* @param   string  $name    Optional. Model name
	* @param   string  $prefix  Optional. Class prefix
	* @param   array   $config  Optional. Configuration array for model
	*
	* @return  object	The Model
	*
	* @since    1.6
	*/
	public function getModel($name = 'Notification', $prefix = 'TjnotificationsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

/**
	* delete
	*
	* @return  null		nothing
	*
	* @since    1.6
	*/
	public function delete()
	{
		$modelDelete = JModelList::getInstance('Notifications', 'TjnotificationsModel', array('ignore_request' => true));
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$modelDelete->delete($cid);
		parent::delete();
	}
}
