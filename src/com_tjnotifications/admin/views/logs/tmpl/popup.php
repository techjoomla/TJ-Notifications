<?php
/**
 * @package    Com_Tjnotifications
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

$jinput  = Factory::getApplication()->input;
$logId = $jinput->get('id', 0, 'INT');
$subject = $jinput->get('subject', 0, 'INT');

Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
$logTable = Table::getInstance('Log', 'TjnotificationsTable');
$logTable->load(array('id' => $logId));

if($subject==="true")
{
?>
	<div>
	<h4 class="modal-title"><?php echo
	JText::_("COM_TJNOTIFICATIONS_FIELD_EMAIL_BODY_LABEL");
	  ?></h4>
	</div>
	<div class="col-xs-12">
	 <p><?php echo htmlspecialchars($logTable->body, ENT_COMPAT, 'UTF-8'); ?>
	</p>
	</div>
	</div>
<?php }
else
{?>
	<div>
	<h4 class="modal-title"><?php echo
	JText::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP");
	  ?></h4>
	</div>
	<div class="col-xs-12">
	 <p><?php echo htmlspecialchars($logTable->params, ENT_COMPAT, 'UTF-8'); ?>
	</p>
	</div>
	</div>
<?php
}
?>
