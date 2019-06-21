<?php
/**
 * @package    Com_Tjnotifications
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.view');
JLoader::register('TjnotificationsHelper', JPATH_ADMINISTRATOR . '/components/com_tjnotifications/helpers/tjnotifications.php');
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for a list of notifications logs.
 *
 * @since  1.1.0
 */
class TjnotificationsViewLogs extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->canDo = JHelperContent::getActions('com_tjnotifications');

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		TjnotificationsHelper::addSubmenu('logs');

		// Set the tool-bar and number of found items
		$this->addToolBar();

		$this->sidebar = JHtmlSidebar::render();

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
		JToolBarHelper::title(JText::_('COM_TJNOTIFICATIONS_LOGS'), 'log');

		if ($this->canDo->get('core.export'))
		{
			// adding techjoomla library for csv Export
			jimport('techjoomla.tjtoolbar.button.csvexport');

			$bar = JToolBar::getInstance('toolbar');

			$message = array();
			$message['success'] = JText::_("COM_TJNOTIFICATIONS_EXPORT_FILE_SUCCESS");
			$message['error'] = JText::_("COM_TJNOTIFICATIONS_EXPORT_FILE_ERROR");
			$message['inprogress'] = JText::_("COM_TJNOTIFICATIONS_EXPORT_FILE_NOTICE");

			$bar->appendButton('CsvExport',  $message);
		}

		if ($this->canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'logs.delete');
		}
	}
}
