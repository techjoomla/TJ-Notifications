<?php
/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Logs controller class.
 *
 * @since  1.1.0
 */
class TjnotificationsControllerLogs extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  Array of configuration parameters.
	 *
	 * @return  JModelLegacy
	 *
	 * @since   1.1.0
	 */
	public function getModel($name = 'log', $prefix = 'TjnotificationsModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}
