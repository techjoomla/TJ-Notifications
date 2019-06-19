<?php
/**
 * @package    Com_Tjnotifications
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
?>

<form action="index.php?option=com_tjnotifications&view=logs" method="post" id="adminForm" name="adminForm">
	<?php if (!empty($this->sidebar)):?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar;?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else :?>
		<div id="j-main-container">
	<?php endif;?>


<?php
			//~ echo "<pre>";print_r($this);die;

//echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => 1,'filtersHidden' => 0)));?>


	<div class="btn-group pull-right hidden-phone">
		<label for="limit" class="element-invisible">
			<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>

				<table class="table table-striped table-hover">
					<thead>
					<tr>
						<th width="2%" class="nowrap center">
							<input type="checkbox" name="checkall-toggle" value=""
										   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>

						<th width="2%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_SUBJECT"), 'subject', $listDirn, $listOrder);?>
						</th>

						<th width="2%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PROVIDER"), 'provider', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_FROM"), 'from', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TO"), 'to', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_CC"), 'cc', $listDirn, $listOrder);?>
						</th>


						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_BCC"), 'bcc', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_DATE"), 'date', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_STATE"), 'state', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PARAMS"), 'params', $listDirn, $listOrder);?>
						</th>
					</tr>
					</thead>
					<tbody>
						<?php if (!empty($this->items)) : ?>
							<?php foreach ($this->items as $i => $row) :
								$link
							?>
								<tr>
									<td class="center">
										<?php echo JHtml::_('grid.id', $i, $row->id); ?>
									</td>


									<td class="">
									<a data-toggle="modal" href="#bodyModal">
										<?php echo $row->subject; ?>
									</a>
									</td>

									  <div class="modal fade" id="bodyModal" role="dialog">
										<div class="modal-dialog">

										  <!-- Modal content-->
										  <div class="modal-content">
											<div class="modal-header">
											  <button type="button" class="close" data-dismiss="modal">&times;</button>
											  <h4 class="modal-title"><?php echo
											  JText::_("COM_TJNOTIFICATIONS_VIEW_EMAIL_BODY");
											  ?></h4>
											</div>
											<div class="modal-body">
											 <p><?php echo $row->body; ?>
											</p>
											</div>
											<div class="modal-footer">
											  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo
											  JText::_("COM_TJNOTIFICATIONS_VIEW_EMAIL_POPUP_CLOSE");
											  ?></button>
											</div>
										  </div>

									<td class="">
											<?php echo $row->key; ?>
									</td>

									<td class="center">
											<?php echo $row->provider; ?>
									</td>

									<td class="center">
											<?php echo $row->from; ?>
									</td>

									<td class="center">
											<?php echo $row->to; ?>
									</td>

									<td class="center">
											<?php echo $row->cc; ?>
									</td>

									<td class="center">
											<?php echo $row->bcc; ?>
									</td>

									<td class="center">
											<?php echo $row->date; ?>
									</td>

									<td class="center">
											<?php echo $row->state; ?>
									</td>

									<td class="">
									<a data-toggle="modal" href="#paramsModal">
										<?php echo "show params"; ?>
									</a>
									</td>

									  <div class="modal fade" id="paramsModal" role="dialog">
										<div class="modal-dialog">

										  <!-- Modal content-->
										  <div class="modal-content">
											<div class="modal-header">
											  <button type="button" class="close" data-dismiss="modal">&times;</button>
											  <h4 class="modal-title"><?php echo
											  JText::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP");
											  ?></h4>
											</div>
											<div class="modal-body">
											 <p><?php echo $row->params; ?>
											</p>
											</div>
											<div class="modal-footer">
											  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo
											  JText::_("COM_TJNOTIFICATIONS_VIEW_EMAIL_POPUP_CLOSE");
											  ?></button>
											</div>
										  </div>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
					<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="extension" value="<?php echo $this->component; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
