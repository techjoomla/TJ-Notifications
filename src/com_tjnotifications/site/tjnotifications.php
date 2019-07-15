<?php
/**
 * @package     TJNotification
 * @subpackage  com_tjnotifications
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('TJNotifications');

// Perform the Request task

$input = JFactory::getApplication()->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller

$controller->redirect();
