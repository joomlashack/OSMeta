<?php
/*------------------------------------------------------------------------
# SEO Boss pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
require_once "MetatagsContainer.php";
class VM_ProductMetatagsContainer extends MetatagsContainer{

public function getMetatags($lim0, $lim, $filter=null){


        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.product_id as id, c.product_name AS title,
        (SELECT GROUP_CONCAT(k.name SEPARATOR ',')
            FROM #__seoboss_keywords k,
            #__seoboss_keywords_items ki
            WHERE ki.item_id=c.product_id and ki.item_type_id=2
                AND ki.keyword_id=k.id
       ) AS metakey,
        m.description as metadesc,
        m.title as metatitle
         FROM
        #__vm_product c
        LEFT JOIN
        #__seoboss_metadata m ON m.item_id=c.product_id and m.item_type=2 WHERE 1";

        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "0");
        $com_vm_filter_show_empty_keywords =
            JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        $com_vm_filter_show_empty_descriptions =
            JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.product_id=".$db->quote($search);
            }else{
                $sql .= " AND c.product_name LIKE ".$db->quote('%'.$search.'%');
            }
        }
        if ($category_id > 0){
            $sql .= " AND EXISTS (SELECT 1 FROM #__vm_product_category_xref WHERE #__vm_product_category_xref.category_id=".$db->quote($category_id)." AND #__vm_product_category_xref.product_id=c.product_id)";

        }
        if ($com_vm_filter_show_empty_keywords != "-1" || $com_vm_filter_show_empty_descriptions != "-1"){
            $sql .= " HAVING ";
        }
        $andRequired = false;
        if ($com_vm_filter_show_empty_keywords != "-1"){
            $sql .= " (ISNULL(metakey) OR metakey='') ";
            $andRequired = true;
        }
        if ($com_vm_filter_show_empty_descriptions != "-1"){
            if ($andRequired){
                $sql .= " AND ";
            }
            $sql .= " (ISNULL(metadesc) OR metadesc='') ";
        }
        //Sorting
        $order = JRequest::getCmd("filter_order", "title");
        $order_dir = JRequest::getCmd("filter_order_Dir", "ASC");
        switch($order){
            case "meta_title":
                $sql .= " ORDER BY metatitle ";
                break;
            case "meta_key":
                $sql .= " ORDER BY metakey ";
                break;
            case "meta_desc":
                $sql .= " ORDER BY metadesc ";
                break;
            default:
                $sql .= " ORDER BY title ";
                break;

        }
        if ($order_dir == "asc"){
            $sql .= " ASC";
        }else{
            $sql .= " DESC";
        }

        $db->setQuery($sql, $lim0, $lim);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        for($i = 0 ; $i < count($rows);$i++){
            $rows[$i]->edit_url = "index.php?page=product.product_form&limitstart=0&".
                "keyword=&product_id={$rows[$i]->id}&product_parent_id=&option=com_virtuemart";
        }
        return $rows;
    }

    public function copyKeywordsToTitle($ids){
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }else{
                $sql = "SELECT k.name
                  FROM #__seoboss_keywords k, #__seoboss_keywords_items ki
                  WHERE k.id=ki.keyword_id
                     AND ki.item_id=".$db->quote($value).
                    "  AND ki.item_type_id=2";
                $db->setQuery($sql);
                $keywords = $db->loadObjectList();
                if (count($keywords) > 0){
                    $keywords_arr = array();
                    foreach($keywords as $keyword){
                        $keywords_arr[] = $keyword->name;
                    }
                    $keywords_str = implode("," , $keywords_arr);
                    $sql = "INSERT INTO #__seoboss_metadata (item_id,
                        item_type, title, description)
                        VALUES (
                        ".$db->quote($value).",
                        2,
                        ".$db->quote($keywords_str).",
                        ''
                       ) ON DUPLICATE KEY
                        UPDATE title=".$db->quote($keywords_str);

                    $db->setQuery($sql);
                    $db->query();
                }
            }
        }
    }

    public function copyTitleToKeywords($ids){
       $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT item_id, title FROM #__seoboss_metadata WHERE item_type=2 AND item_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
            $this->saveKeywords($item->title, $item->item_id);
            }
        }
    }

    public function copyItemTitleToTitle($ids){
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT product_id as id, product_name as title FROM  #__vm_product WHERE product_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             2,
             ".$db->quote($item->title).",
             ''
            ) ON DUPLICATE KEY UPDATE title=".$db->quote($item->title);

            $db->setQuery($sql);
            $db->query();
            }
        }
    }

    public function copyItemTitleToKeywords($ids){
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }else{
                $sql = "SELECT product_name FROM #__vm_product WHERE product_id=".$db->quote($value);
                $db->setQuery($sql);
                $title = $db->loadResult();
                if ($title){
                    $this->saveKeywords($title, $value);
                }
            }
        }

    }

    public function GenerateDescriptions($ids){
      $max_description_length = 500;
      $model = JBModel::getInstance("options", "SeobossModel");
      $params = $model->getOptions();
      $max_description_length =
        $params->max_description_length?
         $params->max_description_length:
         $max_description_length;
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT product_id as id, product_s_desc as introtext FROM  #__vm_product WHERE product_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->introtext != ''){
            $introtext = strip_tags($item->introtext);
            if (strlen($introtext) > $max_description_length){
              $introtext = substr($introtext, 0, $max_description_length);
            }
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             2,

             '',
             ".$db->quote($introtext)."
            ) ON DUPLICATE KEY UPDATE description=".$db->quote($introtext);

            $db->setQuery($sql);
            $db->query();

            }
        }
    }
    public function getPages($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.product_id AS id, c.product_name AS title,
        (SELECT GROUP_CONCAT(k.name SEPARATOR ',')
            FROM #__seoboss_keywords k,
            #__seoboss_keywords_items ki
            WHERE ki.item_id=c.product_id and ki.item_type_id=2
                AND ki.keyword_id=k.id
       ) AS metakey,
        c.product_desc AS content
         FROM
        #__vm_product c WHERE 1
        ";

        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "0");
        $com_vm_filter_show_empty_keywords = JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        //$com_vm_filter_show_empty_descriptions = JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.product_id=".$db->quote($search);
            }else{
                $sql .= " AND c.product_name LIKE ".$db->quote('%'.$search.'%');
            }
        }
        if ($category_id > 0){
            $sql .= " AND EXISTS (SELECT 1 FROM #__vm_product_category_xref WHERE #__vm_product_category_xref.category_id=".$db->quote($category_id)." AND #__vm_product_category_xref.product_id=c.product_id)";
        }
        if ($com_vm_filter_show_empty_keywords != "-1"){
            $sql .= " HAVING (ISNULL(metakey) OR metakey='') ";
        }
        /*if ($com_vm_filter_show_empty_descriptions != "-1"){
            $sql .= " AND (ISNULL(c.metadesc) OR c.metadesc='') ";
        }*/

        $db->setQuery($sql, $lim0, $lim);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        // Get outgoing links and keywords density
        for($i = 0 ; $i < count($rows);$i++){
            if ($rows[$i]->metakey){
                $rows[$i]->metakey = explode(",", $rows[$i]->metakey);
            }else{
                $rows[$i]->metakey = array("");
            }
            $rows[$i]->edit_url = "index.php?page=product.product_form&limitstart=0&".
                "keyword=&product_id={$rows[$i]->id}&product_parent_id=&option=com_virtuemart";
        }
        return $rows;
    }
    public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys){
        $db = JFactory::getDBO();
        for($i = 0 ;$i < count($ids); $i++){
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($ids[$i]).",
             2,
             ".$db->quote($metatitles[$i]).",
             ".$db->quote($metadescriptions[$i])."
            ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i]);
            $db->setQuery($sql);
            $db->query();
            parent::saveKeywords($metakeys[$i], $ids[$i],2);
        }

    }
    public function saveKeywords($keys, $id){
        parent::saveKeywords($keys, $id,2);
    }

    public function getMetadata($id){
        $db = JFactory::getDBO();
        $sql = "SELECT c.product_id as id, c.product_name as title
         FROM
        #__vm_product c
        WHERE c.product_id=".$db->quote($id);
        $db->setQuery($sql);
        $data = $db->loadAssoc();
        $baseData = parent::getMetadata($id);
        $data["title_tag"] = $baseData["title_tag"];
        $data["metatitle"] = $baseData["metatitle"];
        $data["metakeywords"] = $baseData["metakeywords"];
        $data["metadescription"] = $baseData["metadescription"];
        return $data;
    }


    public function setMetadata($id, $data){
        if (isset($data["title"]) && $data["title"]){
          $db = JFactory::getDBO();
          $sql = "UPDATE #__vm_product SET product_name=".$db->quote($data["title"])." WHERE product_id=".$db->quote($id);
          $db->setQuery($sql);
          $db->query();
        }
        parent::setMetadata($id, $data);
    }

    public function getMetadataByRequest($query){
      $params = array();
      parse_str($query, $params);
      $metadata = null;
      if (isset($params["product_id"])){
        $metadata = $this->getMetadata($params["product_id"]);
      }
      return $metadata;
    }

    public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($query, $params);
      if (isset($params["product_id"]) && $params["product_id"]){
        $this->setMetadata($params["product_id"], $data);
      }
    }

    function getFilter(){
        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "");

        $com_vm_filter_show_empty_keywords = JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        $com_vm_filter_show_empty_descriptions = JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

                $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/>
        <button onclick="this.form.submit();">Go</button>
        <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>

        &nbsp;&nbsp;&nbsp;';

        $result .=  "<select name=\"filter_category_id\" onchange=\"document.adminForm.submit();\">".
        "<option value=\"\">Select Category</option>".
        $this->list_tree($category_id).
        "</select>";

        $result .= '<br/>
        <label>Show only Items with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_vm_filter_show_empty_keywords" '.($com_vm_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>
        <label>Show only Items with empty descriptions</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_vm_filter_show_empty_descriptions" '.($com_vm_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
        return $result;
    }
    /**
     * Creates structured option fields for all categories
     *
     * @param int $category_id A single category to be pre-selected
     * @param int $cid Internally used for recursion
     * @param int $level Internally used for recursion
     * @param array $selected_categories All category IDs that will be pre-selected
     */
    private function list_tree($category_id="", $cid='0', $level='0', $selected_categories=Array(), $disabledFields=Array()) {
        $db = JFactory::getDBO();

        $result = "";
        $level++;

        $q = "SELECT category_id, category_child_id, category_name FROM #__vm_category,#__vm_category_xref ";
        $q .= "WHERE #__vm_category_xref.category_parent_id='$cid' ";
        $q .= "AND #__vm_category.category_id=#__vm_category_xref.category_child_id ";
 //       $q .= "AND #__vm_category.vendor_id ='$ps_vendor_id' ";
        $q .= "ORDER BY #__vm_category.list_order, #__vm_category.category_name ASC";
        $db->setQuery($q);
        //$db->query();
        $categories = $db->loadObjectList();
        foreach($categories as $category) {
            $child_id = $category->category_child_id;
            if ($child_id != $cid) {
                $selected = ($child_id == $category_id) ? "selected=\"selected\"" : "";
                if ($selected == "" && @$selected_categories[$child_id] == "1") {
                    $selected = "selected=\"selected\"";
                }
                $result .= "<option $selected $disabled value=\"$child_id\">\n";
                for ($i=0;$i<$level;$i++) {
                    $result .= "&#151;";
                }
                $result .= "|$level|";
                $result .= "&nbsp;" . $category->category_name . "</option>";
            }
            $result .= $this->list_tree($category_id, $child_id, $level, $selected_categories, $disabledFields);
        }
        return $result;
    }
   public function getTypeId(){
     return 2;
   }

   public function isAvailable(){
     require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
     return MetatagsContainerFactory::componentExists("VirtueMart");
   }
}
