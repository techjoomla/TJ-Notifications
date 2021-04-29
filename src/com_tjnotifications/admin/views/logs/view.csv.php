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

jimport('techjoomla.view.csv');

/**
 * View class for a list of notifications logs.
 *
 * @since  1.1.0
 */
class TjnotificationsViewLogs extends TjExportCsv
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{

		$app   = Factory::getApplication();
		$input = $app->input;

		if ($input->get('task') == 'download')
		{
			$fileName = $input->get('file_name');
			$this->download($fileName);
			$app->close();
		}
		else
		{
			parent::display();
		}
	}
}
