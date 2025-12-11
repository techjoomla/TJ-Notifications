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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;

$tjnotificationsHelperPath = JPATH_ADMINISTRATOR . '/components/com_tjnotifications/helpers/tjnotifications.php';
if (file_exists($tjnotificationsHelperPath)) {
	require_once $tjnotificationsHelperPath;
}

/**
 * View class for a list of notifications logs.
 *
 * @since  1.1.0
 */
class TjnotificationsViewLogs extends HtmlView
{
	protected $extension;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->input         = Factory::getApplication()->input;
		$this->extension     = $this->input->getCmd('extension', '');
		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode('<br />', $errors), 500);

			return false;
		}

		$this->canDo = ContentHelper::getActions('com_tjnotifications');

		TjnotificationsHelper::addSubmenu('logs');
		$this->addToolBar();
		$this->sidebar = '';;

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.1.0
	 */
	protected function addToolBar()
	{
		ToolbarHelper::title(Text::_('COM_TJNOTIFICATIONS_LOGS'), 'list');

		if ($this->canDo->get('core.export'))
		{
			// Adding techjoomla library for csv Export
			require_once JPATH_LIBRARIES . '/techjoomla/tjtoolbar/button/csvexport.php';

			$bar = Toolbar::getInstance('toolbar');

			$message = array();
			$message['success']    = Text::_("COM_TJNOTIFICATIONS_EXPORT_FILE_SUCCESS");
			$message['error']      = Text::_("COM_TJNOTIFICATIONS_EXPORT_FILE_ERROR");
			$message['inprogress'] = Text::_("COM_TJNOTIFICATIONS_EXPORT_FILE_NOTICE");

			$bar->appendButton('CsvExport',  $message);
		}

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'logs.delete', 'JTOOLBAR_DELETE');
		}
	}
}
