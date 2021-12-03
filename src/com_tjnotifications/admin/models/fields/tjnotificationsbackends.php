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

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of backends
 *
 * @since  2.0.0
 */
class JFormFieldTjnotificationsbackends extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var	 string
	 * @since   1.0.0
	 */
	protected $type = 'tjnotificationsbackends';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array   An array of JHtml options.
	 *
	 * @since   2.0.0
	 */
	protected function getOptions()
	{
		$options  = array();
		$backends = explode(',', TJNOTIFICATIONS_CONST_BACKENDS_ARRAY);

		foreach ($backends as $keyBackend => $backend)
		{
			$options[] = HTMLHelper::_('select.option', $backend, Text::_('COM_TJNOTIFICATIONS_BACKEND_' . strtoupper($backend)));
		}

		return array_merge(parent::getOptions(), array_values($options));
	}
}
