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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
JHTML::_( 'behavior.modal' ); 
?>
<form action="index.php" method="post" name="adminForm">
  <table width="100%">
    <tr>
      <td align="right">
      <label><?php echo JText::_('SEO_SELECT_CONTENT_TYPE');?>:&nbsp;</label>
      <select name="type" onchange="document.adminForm.submit();">
<?php 
    foreach($this->availableTypes as $typeId=>$typeName){
?>
    <option value="<?php echo $typeId?>" <?php if ($typeId==$this->itemType){?>selected="true"<?php }?>><?php echo $typeName["name"]?></option>
<?php }?>
</select>
    </td>
  </tr>
  <tr>
    <td align="right">
        <?php echo $this->filter;?>  
    </td>
  </tr>
  </table>
   <table class="adminlist">
    <thead>
      <tr>
        
             <th class="title">
            <?php echo JText::_('SEO_PAGE');?>
            </th>
            <th class="title">
            <?php echo JText::_('SEO_PAGE_EDIT');?>
            </th>
            <th class="title">
            <?php echo JText::_('SEO_KEYWORDS_PHRASES');?>
                
            </th>
            <th class="title">
            <?php echo JText::_('SEO_TIMES_ON_CONTENT');?>
                
            </th>
            <th class="title">
            <?php echo JText::_('SEO_DENSITY');?>
            </th>
        </tr>
    </thead>
    <?php 
        $k = 0;
        for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
    {
        $row = $this->rows[$i];?>
    <tr >
        <td rowspan="<?php echo count( $row->metakey) ;?>">
            <?php echo $row->title;?>
        </td>
        <td rowspan="<?php echo count( $row->metakey) ;?>">
            <strong><a class="modal" rel="{handler:'iframe', size: {x: 650, y: 350}}" href="index.php?format=raw&option=com_seoboss&task=pages_edit_text&type=<?php echo $this->itemType; ?>&id=<?php echo $row->id;?>"><?php echo JText::_('SEO_EDIT');?></a></strong>
        </td>
        <td>
            <?php echo $row->metakey[0];?>
        </td>
        <td>
            <strong><?php echo $row->stat[0]["frequency"]?></strong>
        </td>
        <td>
            <strong><?php  printf("%.2f", $row->stat[0]["density"] );?></strong>
        </td>
    </tr>
    <?php for ($j = 1 ; $j < count($row->metakey) ; $j++) {?>
    <tr >
        <td>
            <?php echo $row->metakey[$j];?>
        </td>
        <td>
            <strong><strong><?php echo $row->stat[$j]["frequency"]?></strong></strong>
        </td>
        <td>
            <strong><?php printf("%.2f", $row->stat[$j]["density"] );?></strong>
        </td>
    </tr>
    <?php }?>
    <?php } ?>
    <tfoot>
    <tr>
      <td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
    </tr>
  </tfoot>
 </table>
 <input type="hidden" name="option" value="com_seoboss" /> 
 <input type="hidden" name="task" value="pages_manager" /> 
 <input type="hidden" name="boxchecked" value="0" />
</form>
