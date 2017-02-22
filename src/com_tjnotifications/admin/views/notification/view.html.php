<?php

/**
 * @package    Com_Tjnotification
 * @copyright  Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die;

JLoader::import('preferences', JPATH_SITE . '/components/com_tjnotifications/models');
/**
 * new notification View
 *
 * @since  0.0.1
 */
class TjnotificationsViewNotification extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');

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

		// Assign the Data
		$this->form = $form;
		$this->item = $item;

		$model = JModelAdmin::getInstance('Preferences', 'TJNotificationsModel');
		$this->count    = $model->count();

		$extension  = JFactory::getApplication()->input->get('extension', '', 'word');

		if ($extension)
		{
			$this->addToolBarExtension();
			$this->_setToolBar();
		}

		$this->addToolBar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBarExtension()
	{
		JToolBarHelper::title(JText::_('COM_TJNOTIFICATIONS'));
		JToolBarHelper::apply('notification.editSave', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('notification.saveClose', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('notification.saveNew', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('notification.saveCancel', 'JTOOLBAR_CANCEL');
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_TJNOTIFICATIONS'));
		JToolBarHelper::apply('notification.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('notification.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('notification.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('notification.cancel', 'JTOOLBAR_CANCEL');
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
