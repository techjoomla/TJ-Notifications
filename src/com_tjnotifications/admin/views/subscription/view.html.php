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
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
/**
 * View to edit tjnotifications
 *
 * @package  Tjnotifications
 *
 * @since    2.0.0
 */
class TjnotificationsViewSubscription extends HtmlView
{
	protected $extension;

	protected $form;

	protected $input;

	protected $item;

	protected $state;

	protected $user;

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
		$this->input     = Factory::getApplication()->input;
		$this->extension = $this->input->getCmd('extension', '');
		$this->user      = Factory::getUser();
		$this->state     = $this->get('State');
		$this->item      = $this->get('Item');
		$this->form      = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * checks if user has access to create or edit record
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 */
	protected function isAuthorised()
	{
		$authorised = false;

		// New record
		if (empty($this->item->id))
		{
			if ($this->user->authorise('core.create', 'com_tjnotifications'))
			{
				$authorised = true;
			}
		}
		// Edit record
		else
		{
			// User has access to edit any record
			if ($this->user->authorise('core.edit', 'com_tjnotifications'))
			{
				$authorised = true;
			}
			else
			{
				// If item has created_by field
				if (isset($this->item->created_by))
				{
					// User has access to edit own record && user is owner of this record
					if ($this->user->authorise('core.edit.own', 'com_tjnotifications') && $this->item->created_by == $this->user->id)
					{
						$authorised = true;
					}
				}
			}
		}

		return $authorised;
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
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

		$canDo = ContentHelper::getActions('com_tjnotifications', '', 0);

		JToolBarHelper::title(Text::_('COM_TJNOTIFICATIONS_SUBSCRIPTION_PAGE_TITLE'), 'edit.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('subscription.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('subscription.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			JToolBarHelper::custom('subscription.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create'))
		{
			JToolBarHelper::custom('subscription.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('subscription.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('subscription.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
