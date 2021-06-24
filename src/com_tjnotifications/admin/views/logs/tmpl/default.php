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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.renderModal', 'a.modal');
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

HTMLHelper::stylesheet('media/com_tjnotifications/css/tjnotifcations.min.css');
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		var width = jQuery(window).width();
		var height = jQuery(window).height();
		//ID of container
		jQuery('.modal notification').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');
	});
</script>

<form action="<?php echo Route::_('index.php?option=com_tjnotifications&view=logs&extension=' . $this->extension); ?>"
	method="post" id="adminForm" name="adminForm">
	<?php
	if (!empty( $this->sidebar))
	{
		?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php
	}
	else
	{
		?>
	<div id="j-main-container">
		<?php
	}
	?>

		<?php
		// Search tools bar
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => 1,'filtersHidden' => 0)));
		?>
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible">
				<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
			<div class="table-responsive">
				<table class="table-wrap table table-striped">
					<thead>
						<tr>
							<th width="2%" class="">
								<input type="checkbox" name="checkall-toggle" value=""
								title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>"
								onclick="Joomla.checkAll(this)"/>
							</th>
							<th width="15%" class="">
								<?php echo HTMLHelper::_(
									'grid.sort',
									Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_SUBJECT"), 'subject', $listDirn, $listOrder
								); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BODY"), 'title', $listDirn, $listOrder); ?>
							</th>
							<th width="15%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TO"), 'to', $listDirn, $listOrder); ?>
							</th>
							<th width="15%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_CC"), 'cc', $listDirn, $listOrder); ?>
							</th>
							<th width="10%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BCC"), 'bcc', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_DATE"), 'date', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_STATE"), 'state', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_(
									'grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BACKEND"), 'backend', $listDirn, $listOrder
								); ?>
							</th>
							<th width="10%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_FROM"), 'from', $listDirn, $listOrder); ?>
							</th>
							<th width="5%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PARAMS"), 'params', $listDirn, $listOrder); ?>
							</th>
							<th width="2%" class="">
								<?php echo HTMLHelper::_('grid.sort', Text::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_ID"), 'id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>

					<tbody>
						<?php
						if (!empty($this->items))
						{
							foreach ($this->items as $i => $row)
							{
								?>
								<tr>
									<td class="" width="2%">
										<?php echo HTMLHelper::_('grid.id', $i, $row->id); ?>
									</td>

									<td class="small" width="15%">
										<?php echo htmlspecialchars($row->subject, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="small" width="5%">
										<a class="modal notification"
											href="<?php echo Route::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=body&id=' . $row->id); ?>">
											<?php echo Text::_("COM_TJNOTIFICATIONS_TITLE_VIEW_CONTENTS"); ?>
										</a>
									</td>

									<td class="wrap small" width="15%">
										<?php echo str_replace(',', '<br />', $row->to); ?>
									</td>

									<td class="wrap small" width="15%">
										<?php echo str_replace(',', '<br />', $row->cc); ?>
									</td>

									<td class="wrap small" width="10%">
										<?php echo str_replace(',', '<br />', $row->bcc); ?>
									</td>

									<td class="small" width="5%">
										<?php echo htmlspecialchars($row->date, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="small" width="5%">
										<?php
											echo ($row->state == 1) ? Text::_('COM_TJNOTIFICATIONS_STATE_SENT') :  Text::_('COM_TJNOTIFICATIONS_STATE_FAILED');
										?>
									</td>

									<td class="small" width="5%">
										<?php echo htmlspecialchars($row->key, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="small" width="5%">
										<?php echo htmlspecialchars($row->backend, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="small" width="10%">
										<?php echo htmlspecialchars($row->from, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="small" width="5%">
										<?php
										if (!empty($row->params))
										{
											?>
											<a class="modal notification"
												href="<?php echo Route::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=param&id=' . $row->id); ?>">
												<?php echo Text::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP"); ?>
											</a>
											<?php
										}
										else
										{
											?>
											<?php echo Text::_('COM_TJNOTIFICATIONS_EMPTY_PARAMS'); ?>
											<?php
										}
										?>
									</td>

									<td class="" width="2%">
										<?php echo htmlspecialchars($row->id, ENT_COMPAT, 'UTF-8'); ?>
									</td>
								</tr>

								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>

			<?php echo $this->pagination->getListFooter(); ?>

			<input type="hidden" name="extension"  value="<?php echo $this->extension; ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</div>
</form>
