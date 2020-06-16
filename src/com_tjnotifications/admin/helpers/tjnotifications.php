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

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;

/**
 * helper class for tjnotificationss
 *
 * @package     TJnotification
 * @subpackage  com_tjnotifications
 * @since       2.2
 */
class TjnotificationsHelper extends ContentHelper
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
		$full_client = $input->getCmd('extension', '');
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

			if (class_exists($cName) && is_callable(array($cName, 'addSubmenu')))
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

		/*JHtmlSidebar::addEntry(
			JText::_('COM_TJNOTIFICATIONS_TITLE_NOTIFICATIONS'),
			'index.php?option=com_tjnotifications&view=notifications',
			$view == 'notifications'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TJNOTIFICATIONS_TITLE_NOTIFICATIONLOGS'),
			'index.php?option=com_tjnotifications&view=logs',
			$view == 'logs'
		);*/
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string   $component  The component name.
	 * @param   string   $section    The access section name.
	 * @param   integer  $id         The item ID.
	 *
	 * @return  \JObject
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public static function getActions($component = '', $section = '', $id = 0)
	{
		// Get list of actions
		return ContentHelper::getActions($component, $section, $id);
	}
}
