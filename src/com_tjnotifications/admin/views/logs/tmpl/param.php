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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

$jinput  = Factory::getApplication()->input;
$logId = $jinput->get('id', 0, 'INT');

if ($logId)
{
	Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjnotifications/tables');
	$logTable = Table::getInstance('Log', 'TjnotificationsTable');
	$logTable->load(array('id' => $logId));
}
?>
<div>
	<?php
	if ($logTable->id)
	{
		?>
		<h3 class="modal-title">
			<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP"); ?>
		</h3>

		<div class="col-xs-12">
			<pre><?php echo json_encode(json_decode($logTable->params, true), JSON_PRETTY_PRINT); ?></pre>
		</div>
		<?php
	}
	?>
</div>
