<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Api.tjnotifications
 *
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http:/www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;

/**
 * Class for Tjnotifications Api Plugin
 *
 * @since  1.0.0
 */
class PlgApiTjnotifications extends ApiPlugin
{
	/**
	 * Constructor
	 *
	 * @param   string  $subject  subject
	 * @param   array   $config   config
	 *
	 * @since   1.0.0
	 */
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config = array());

		$componentPath = JPATH_ROOT . '/components/com_tjnotifications';

		if (!file_exists($componentPath))
		{
			return;
		}

		// Load language files
		$lang = Factory::getLanguage();
		$lang->load('plg_api_tjnotifications', JPATH_ADMINISTRATOR, '', true);

		ApiResource::addIncludePath(dirname(__FILE__) . '/tjnotifications');
	}
}
