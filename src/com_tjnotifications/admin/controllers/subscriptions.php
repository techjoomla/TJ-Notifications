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

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * List controller for subscription
 *
 * @package  Tjnotifications
 *
 * @since    __DEPLOY_VERSION__
 */
class TjnotificationsControllerSubscriptions extends AdminController
{
	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelBase|JModelLegacy|boolean  Model object on success; otherwise false on failure.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getModel($name = 'Subscription', $prefix = 'TjnotificationsModel', $config = array())
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
}
