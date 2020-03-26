<?php
/**
 * @package     TJNotifications
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Toolbar\ToolbarHelper;

JLoader::import('preferences', JPATH_SITE . '/components/com_tjnotifications/models');
/**
 * new notification View
 *
 * @since  0.0.1
 */
class TjnotificationsViewNotification extends \Joomla\CMS\MVC\View\HtmlView
{
	/**
	 * View form
	 *
	 * @var         form
	 */
	protected $form = null;

	protected $state;

	protected $item;

	public $user;

	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get data from the model
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->state         = $this->get('State');
		$this->form 		 = $this->get('Form');
		$this->item          = $this->get('Item');
		$this->component     = $this->state->get('filter.component');
		$this->user          = Factory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}

		$this->addToolBar();

		$extension  = Factory::getApplication()->input->get('extension', '', 'word');

		if ($extension)
		{
			$this->_setToolBar();
		}

		parent::display($tpl);
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
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $this->user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		JToolBarHelper::title(Text::_('COM_TJNOTIFICATIONS'), 'edit.png');

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			JToolBarHelper::apply('notification.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('notification.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('notification.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew)
		{
			JToolBarHelper::custom('notification.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('notification.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('notification.cancel', 'JTOOLBAR_CLOSE');
		}
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
		$lang = Factory::getLanguage();
		$lang->load($component, JPATH_BASE, null, false, true)
		|| $lang->load($component, JPATH_ADMINISTRATOR . '/components/' . $component, null, false, true);

		// If a component notification title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component . ($section ? "_$section" : '')) . '_NOTIFICATIONS_TEMPLATES'))
		{
			$title = Text::_($component_title_key);
		}
		elseif ($lang->hasKey($component_section_key = strtoupper($component . ($section ? "_$section" : ''))))
		// Else if the component section string exits, let's use it
		{
			$title = Text::sprintf('COM_TJNOTIFICATIONS_NOTIFICATION_TITLE', $this->escape(Text::_($component_section_key)));
		}
		else
		// Else use the base title
		{
			$title = Text::_('COM_TJNOTIFICATIONS_NOTIFICATION_BASE_TITLE');
		}

		// Prepare the toolbar.
		ToolbarHelper::title($title, 'folder notifications ' . substr($component, 4) . ($section ? "-$section" : '') . '-notification templates');
	}
}
