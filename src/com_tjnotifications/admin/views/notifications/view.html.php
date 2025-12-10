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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of notifications.
 *
 * @since  1.6
 */
class TjnotificationsViewNotifications extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	public $app;

	public $activeFilters;

	public $component;

	public $filterForm;

	public $languages;

	public $user;

	public $sidebar;

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
		// Validate
		$this->app  = Factory::getApplication();
		$this->user = Factory::getUser();

		if (empty($this->user->authorise('core.viewlist', 'com_tjnotifications')))
		{
			$msg = Text::_('JERROR_ALERTNOAUTHOR');
			throw new \Exception($msg, 403);
		}

		// Get data from the model
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state         = $this->get('State');
		$this->component     = $this->state->get('filter.component');
		$this->languages     = $this->get('Languages');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode('<br />', $errors), 500);
		}

		// Set the tool-bar and number of found items
		$this->addToolbar();

		$extension = $this->app->input->getCmd('extension', '');

		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models');
		$model       = AdminModel::getInstance('Preferences', 'TJNotificationsModel');
		$this->count = $model->count();

		if ($extension)
		{
			require_once JPATH_COMPONENT . '/helpers/tjnotifications.php';

			if ($extension)
			{
				TjnotificationsHelper::addSubmenu('notifications');
			}

			$this->_setToolbar();
			
			// Joomla 6 compatible sidebar rendering
			$this->sidebar = $this->renderSidebar();
		}

		parent::display($tpl);
	}

	/**
	 * Render the sidebar for Joomla 6
	 *
	 * @return string  The rendered sidebar HTML
	 *
	 * @since  2.0.0
	 */
	protected function renderSidebar()
	{
		// Joomla 4+ doesn't use sidebars in the same way as Joomla 3
		// The addSubmenu() method handles submenu registration
		// Return empty string as sidebar is handled by Joomla core
		return '';
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    0.0.1
	 */
	protected function addToolbar()
	{
		$title = Text::_('COM_TJNOTIFICATIONS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'></span>";
		}

		ToolbarHelper::title($title, 'notification');

		if ($this->user->authorise('core.create', 'com_tjnotifications'))
		{
			ToolbarHelper::addNew('notification.add');
		}

		if ($this->user->authorise('core.edit', 'com_tjnotifications'))
		{
			ToolbarHelper::editList('notification.edit');
		}

		if ($this->user->authorise('core.delete', 'com_tjnotifications'))
		{
			ToolbarHelper::deleteList(
				Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DELETE_MESSAGE'), 
				'notifications.delete', 
				Text::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DELETE')
			);
		}

		if ($this->user->authorise('core.admin', 'com_tjnotifications'))
		{
			ToolbarHelper::preferences('com_tjnotifications');
		}
	}

	/**
	 * Function to set tool bar.
	 *
	 * @return void
	 *
	 * @since	1.8
	 */
	public function _setToolbar()
	{
		$component = $this->state->get('filter.component');
		$section   = $this->state->get('filter.section');

		// Avoid nonsense situation.
		if ($component == 'com_notifications')
		{
			return;
		}
		
		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// If a component notification title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_NOTIFICATIONS_TEMPLATES'))
		{
			$title = Text::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		{
			// Else if the component section string exits, let's use it
			$title = Text::sprintf('COM_TJNOTIFICATIONS_NOTIFICATION_TITLE', $this->escape(Text::_($component_section_key)));
		}
		else
		{
			// Else use the base title
			$title = Text::_('COM_TJNOTIFICATIONS_NOTIFICATION_BASE_TITLE');
		}

		// Prepare the toolbar.
		ToolbarHelper::title(
			$title, 
			'folder notifications ' . substr($component, 4) . ($section ? "-$section" : '') . '-notification templates'
		);
	}
}