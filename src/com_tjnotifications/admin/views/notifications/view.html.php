<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');



/**
 * View class for a list of notifications.
 *
 * @since  1.6
 */
class TjnotificationsViewNotifications extends JViewLegacy
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
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state         = $this->get('State');
		$this->component     = $this->state->get('filter.component');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		// Set the tool-bar and number of found items
		$this->addToolBar();

		$extension = JFactory::getApplication()->input->get('extension', '', 'word');

		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_tjnotifications/models');
		$model = JModelAdmin::getInstance('Preferences', 'TJNotificationsModel');
		$this->count    = $model->count();

		if ($extension)
		{
			require_once JPATH_COMPONENT . '/helpers/tjnotifications.php';

			if ($extension)
			{
				TjnotificationsHelper::addSubmenu('notifications');
			}

			$this->_setToolBar();
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    0.0.1
	 */
	protected function addToolBar()
	{
		$title = JText::_('COM_TJNOTIFICATIONS');

		if ($this->pagination->total)
		{
			$title .= "<span style='font-size: 0.5em; vertical-align: middle;'></span>";
		}

		JToolBarHelper::title($title, 'notification');
		JToolBarHelper::addNew('notification.add');
		JToolBarHelper::editList('notification.edit');
		JToolBarHelper::deleteList(
		JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DELETE_MESSAGE'), 'notifications.delete', JText::_('COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DELETE')
		);
	}

	/**
	 * Function to set tool bar.
	 *
	 * @return void
	 *
	 * @since	1.8
	 */
	public function _setToolBar()
	{
		$component  = $this->state->get('filter.component');
		$section    = $this->state->get('filter.section');

		// Avoid nonsense situation.
		if ($component == 'com_notifications')
		{
			return;
		}
		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = JFactory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// If a component notification title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_NOTIFICATIONS_TEMPLATES'))
		{
			$title = JText::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$title = JText::sprintf('COM_TJNOTIFICATIONS_NOTIFICATION_TITLE', $this->escape(JText::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$title = JText::_('COM_TJNOTIFICATIONS_NOTIFICATION_BASE_TITLE');
		}

		// Prepare the toolbar.
		JToolbarHelper::title($title, 'folder notifications ' . substr($component, 4) . ($section ? "-$section" : '') . '-notification templates');
	}
}
