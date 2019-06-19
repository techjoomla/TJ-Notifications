<?php
/**
 * @package    Com_Tjnotifications
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

//Import CSV library view
jimport('techjoomla.view.csv');


/**
 * View class for a list of notifications logs.
 *
 * @since  1.1.0
 */
class TjnotificationsViewLogs extends TjExportCsv
{
	public function display($tpl = null)
	{
		parent::display();
	}

}
