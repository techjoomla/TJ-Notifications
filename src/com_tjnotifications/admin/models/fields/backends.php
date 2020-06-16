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

use Joomla\CMS\Language\Text;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of backends
 *
 * @since  __DEPLOY_VERSION__
 */
class JFormFieldBackends extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var	 string
	 * @since   __DEPLOY_VERSION__
	 */
	protected $type = 'backends';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array   An array of JHtml options.
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] = JHtml::_('select.option', '',         Text::_('COM_TJNOTIFICATIONS_BACKEND_LIST_CHOOSE'));
		$options[] = JHtml::_('select.option', 'email',    Text::_('COM_TJNOTIFICATIONS_BACKEND_EMAIL'));
		$options[] = JHtml::_('select.option', 'push',     Text::_('COM_TJNOTIFICATIONS_BACKEND_PUSH'));
		$options[] = JHtml::_('select.option', 'sms',      Text::_('COM_TJNOTIFICATIONS_BACKEND_SMS'));
		$options[] = JHtml::_('select.option', 'whatsapp', Text::_('COM_TJNOTIFICATIONS_BACKEND_WHATSAPP'));

		return array_merge(parent::getOptions(), array_values($options));
	}

	/**
	 * Method to get a list of options for a list input externally and not from xml.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   2.1
	 */
	public function getOptionsExternally()
	{
		return $this->getOptions();
	}
}
