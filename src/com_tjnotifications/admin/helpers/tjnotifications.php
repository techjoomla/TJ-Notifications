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
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Filesystem\Path;

/**
 * helper class for tjnotificationss
 *
 * @package     TJnotification
 * @subpackage  com_tjnotifications
 * @since       2.2
 */
class TjnotificationsHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   STRING  $view  view name
	 *
	 * @return null
	 */
	public static function addSubmenu($view = '')
	{
		$input       = Factory::getApplication()->input;
		$full_client = $input->get('extension', '', 'STRING');
		$full_client = explode('.', $full_client);

		// Eg com_jgive
		$component = $full_client[0];
		$eName     = str_replace('com_', '', $component);
		$file      = Path::clean(JPATH_ADMINISTRATOR . '/components/' . $component . '/helpers/' . $eName . '.php');

		if (file_exists($file))
		{
			require_once $file;

			$prefix = ucfirst(str_replace('com_', '', $component));
			$cName = $prefix . 'Helper';

			if (class_exists($cName))
			{
				if (is_callable(array($cName, 'addSubmenu')))
				{
					$lang = Factory::getLanguage();

					// Loading language file from the administrator/language directory then
					// Loading language file from the administrator/components/*extension*/language directory
					$lang->load($component, JPATH_BASE, null, false, false)
					|| $lang->load($component, Path::clean(JPATH_ADMINISTRATOR . '/components/' . $component), null, false, false)
					|| $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
					|| $lang->load($component, Path::clean(JPATH_ADMINISTRATOR . '/components/' . $component), $lang->getDefault(), false, false);

					// Call_user_func(array($cName, 'addSubmenu'), 'categories' . (isset($section) ? '.' . $section : ''));
					call_user_func(array($cName, 'addSubmenu'), $view . (isset($section) ? '.' . $section : ''));
				}
			}
		}
	}
}
