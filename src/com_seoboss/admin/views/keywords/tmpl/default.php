<?php
/*------------------------------------------------------------------------
# SEO Boss Pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm">
<input type="hidden" name="filter_order" value="<?php echo $this->order ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->order_Dir ?>" />

   <table class="adminlist">
    <thead>
      <tr>
            <th width="20"><input type="checkbox" name="toggle" value=""
                onclick="checkAll(<?php echo
count($this->rows); ?>);" /></th>
            <th class="title">
            <?php echo JHTML::_('grid.sort', JText::_('SEO_KEYWORD_PHRASE'), 'name', $this->order_Dir, $this->order, "keywords_view"); ?>

            </th>
            <th class="title">
            <?php echo JHTML::_('grid.sort', JText::_('SEO_GOOGLE_POSITION'), 'google_rank', $this->order_Dir, $this->order, "keywords_view"); ?>

            </th>
            <th class="title">
            <?php echo JHTML::_('grid.sort', JText::_('SEO_CHANGE'), 'google_rank_change', $this->order_Dir, $this->order, "keywords_view"); ?>

            </th>
            <th class="title">
            <?php echo JHTML::_('grid.sort', JText::_('SEO_CHANGE_SINCE'), 'google_rank_change_date', $this->order_Dir, $this->order, "keywords_view"); ?>

            </th>

            <th class="title">
                <?php echo JText::_('SEO_VIEW_ON_GOOGLE');?>
            </th>
        </tr>
    </thead>
    <?php
        $k = 0;
        for ($i=0, $n=count($this->rows); $i < $n; $i++)
    {
        $row = $this->rows[$i];
        $checked = JHTML::_('grid.id', $i, $row->id);?>

    <tr class="<?php echo "row$k"; ?>">
        <td><?php echo $checked; ?>
            <input type="hidden" name="ids[]" value="<?php echo $row->id ?>"/>
        </td>
        <td>
            <?php echo $row->name;?>
        </td>
        <td>
            <?php
            if ($row->google_rank > 0){
            	echo $row->google_rank<1000?$row->google_rank:"out of top 100";
            }else{
            	echo JText::_('SEO_UNKNOWN');
            }?>
        </td>
        <td>
            <?php
            if ($row->google_rank_change>0){
            echo "+".$row->google_rank_change;
            }elseif ($row->google_rank_change<0){
            echo $row->google_rank_change;
            }
            ?>
        </td>
        <td>
            <?php echo $row->google_rank_change_date!='0000-00-00 00:00:00'?$row->google_rank_change_date:"never";?>
        </td>

        <td>
            <a href="http://<?php echo $this->google_url?>/search?aq=f&q=<?php echo $row->name?>" target="_blank"><?php echo JText::_('SEO_VIEW');?></a>
        </td>
    </tr>
    <?php } ?>
    <tfoot>
    <tr>
      <td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
    </tr>
  </tfoot>
 </table>
 <input type="hidden" name="option" value="com_seoboss" /> <input
    type="hidden" name="task" value="keywords_view" />
    <input type="hidden" name="boxchecked" value="0" />
 </form>

