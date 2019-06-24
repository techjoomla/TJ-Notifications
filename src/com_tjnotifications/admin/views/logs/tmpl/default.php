<?php
/**
 * @package    Com_Tjnotifications
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;
include 'header.html';



JHTML::_('behavior.modal', 'a.modal');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));
?>
<script type="text/javascript">
jQuery(document).ready(function() {
    var width = jQuery(window).width();
    var height = jQuery(window).height();

    //ID of container
    jQuery('a#modal_info').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.10))+', y: '+(height-(height*0.10))+'}}');
});
</script>

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
echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => 1,'filtersHidden' => 0)));?>

	<div class="btn-group pull-right hidden-phone">
		<label for="limit" class="element-invisible">
			<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
				<div class="table-responsive">
				<table class="table table-striped">
					<thead>
					<tr>
						<th width="2%" class="nowrap center">
							<input type="checkbox" name="checkall-toggle" value=""
										   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						<th width="2%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_ID"), 'id', $listDirn, $listOrder);?>
						</th>
						<th width="2%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_TITLE"), 'title', $listDirn, $listOrder);?>
						</th>

						<th width="2%" class="hidden-phone">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_SUBJECT"), 'subject', $listDirn, $listOrder);?>
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

						<th width="2%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_KEY"), 'key', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_PROVIDER"), 'provider', $listDirn, $listOrder);?>
						</th>

						<th width="10%" class="nowrap center">
							<?php echo JHtml::_('grid.sort', JText::_("COM_TJNOTIFICATIONS_VIEW_NOTIFICATIONS_DEFAULT_FIELD_FROM"), 'from', $listDirn, $listOrder);?>
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
									<td class="center">
											<?php echo htmlspecialchars($row->id, ENT_COMPAT, 'UTF-8');
									?>
									</td>
									<td class="center">
											<?php echo htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8');
									?>
									</td>
									<td class="">
										<a id="modal_info" class="modal" href="<?php echo JRoute::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=body&id='. $row->id); ?>"><?php echo htmlspecialchars($row->subject, ENT_COMPAT, 'UTF-8'); ?></a>

									<td class="center">
											<?php echo htmlspecialchars($row->to, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="center">
											<?php echo htmlspecialchars($row->cc, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="center">
											<?php echo htmlspecialchars($row->bcc, ENT_COMPAT, 'UTF-8'); ?>
									</td>
									<td class="center">
											<?php echo htmlspecialchars($row->date, ENT_COMPAT, 'UTF-8');?>
									</td>

									<td class="center">
											<?php echo htmlspecialchars($row->state, ENT_COMPAT, 'UTF-8'); ?>
									</td>
									<td class="center">
											<?php echo htmlspecialchars($row->key, ENT_COMPAT, 'UTF-8');
									?>
									</td>

									<td class="center">
											<?php echo htmlspecialchars($row->provider, ENT_COMPAT, 'UTF-8'); ?>
									</td>

									<td class="center">
											<?php echo htmlspecialchars($row->from, ENT_COMPAT, 'UTF-8'); ?>
									</td>
									<td class="center">
									<?php if(!empty($row->params)){ ?>
									<a id="modal_info" class="modal" href="<?php echo JRoute::_('index.php?option=com_tjnotifications&tmpl=component&view=logs&layout=param&id='. $row->id); ?>"><?php echo JText::_("COM_TJNOTIFICATIONS_VIEW_PARAMS_POPUP");?></a>
									</a>
									<?php }
									else
									{
										echo JText::_('COM_TJNOTIFICATIONS_EMPTY_PARAMS');
									}?>
									</td>
									</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
					<?php echo $this->pagination->getListFooter(); ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
