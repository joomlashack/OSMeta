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
require_once "MetatagsContainer.php";
class MosetsTree_CategoryMetatagsContainer extends MetatagsContainer{
  public $code=8;

  public function getTypeId(){
    return $this->code;
  }

  public function getMetatags($lim0, $lim, $filter=null){
    $db = JFactory::getDBO();
    $sql = "SELECT SQL_CALC_FOUND_ROWS
    c.cat_id as id,
    c.cat_name AS title,
    c.metakey AS metakey,
    c.metadesc as metadesc,
    m.title as metatitle,
    m.title_tag as title_tag
    FROM
    #__mt_cats c
    LEFT JOIN
    #__seoboss_metadata m ON m.item_id=c.cat_id and m.item_type={$this->code}
    WHERE 1 ";

    $search = JRequest::getVar("filter_search", "");
    $filter_show_empty_keywords =
    JRequest::getVar("filter_show_empty_keywords", "-1");
    $filter_show_empty_descriptions =
    JRequest::getVar("filter_show_empty_descriptions", "-1");

    if ($search != ""){
      if (is_numeric($search)){
        $sql .= " AND c.cat_id=".$db->quote($search);
      }else{
        $sql .= " AND c.cat_name LIKE ".$db->quote('%'.$search.'%');
      }
    }
    if ($filter_show_empty_keywords != "-1"){
      $sql .= " AND (ISNULL(metakey) OR metakey='') ";
    }
    if ($filter_show_empty_descriptions != "-1"){
      $sql .= "AND (ISNULL(metadesc) OR metadesc='') ";
    }
    //Sorting
    $order = JRequest::getCmd("filter_order", "title");
    $order_dir = JRequest::getCmd("filter_order_Dir", "asc");



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
      case "title":
        $sql .= " ORDER BY title ";
        break;
      default:
        $sql .= " ORDER BY `lft` ";
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
      $rows[$i]->edit_url = "index.php?option=com_mtree&task=editcat&cat_id={$rows[$i]->id}";
    }
    return $rows;
  }

	public function mustReplaceMeteaKeywords(){
	  return false;
	}
	public function mustReplaceMetaDescription(){
	  return false;
	}

  public function copyKeywordsToTitle($ids){
    $db = JFactory::getDBO();
    foreach($ids as $key=>$value){
      if (!is_numeric($value)){
        unset($ids[$key]);
      }
    }
    $sql = "SELECT cat_id, metakey FROM #__mt_cats WHERE cat_id IN (".implode(",", $ids).")";
    $db->setQuery($sql);
    $items = $db->loadObjectList();
    foreach($items as $item){
      if ($item->metakey != ''){
        $sql = "INSERT INTO #__seoboss_metadata (item_id,
            item_type, title_tag)
            VALUES (
            ".$db->quote($item->cat_id).",
            '{$this->code}',
            ".$db->quote($item->metakey)."
               ) ON DUPLICATE KEY UPDATE title_tag=".$db->quote($item->metakey);

        $db->setQuery($sql);
        $db->query();
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
    $sql = "SELECT item_id, title_tag
        FROM #__seoboss_metadata
        WHERE item_id IN (".implode(",", $ids).") AND item_type='{$this->code}'";
    $db->setQuery($sql);
    $items = $db->loadObjectList();
    foreach($items as $item){
      if ($item->title_tag != ''){
        $sql = "UPDATE #__mt_cats SET metakey=".$db->quote($item->title_tag)."
            WHERE cat_id=".$db->quote($item->item_id);
        $db->setQuery($sql);
        $db->query();
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
    $sql = "SELECT cat_id, cat_name
        FROM  #__mt_cats
        WHERE cat_id IN (".implode(",", $ids).")";
    $db->setQuery($sql);
    $items = $db->loadObjectList();
    foreach($items as $item){
      if ($item->cat_name != ''){
        $sql = "INSERT INTO #__seoboss_metadata (item_id,
            item_type, title_tag)
            VALUES (
            ".$db->quote($item->cat_id).",
            '{$this->code}',
            ".$db->quote($item->cat_name)."
               ) ON DUPLICATE KEY UPDATE title_tag=".$db->quote($item->cat_name);

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
      }
    }
    if (count($ids) > 0){
      $sql = "UPDATE #__mt_cats SET metakey=cat_name WHERE cat_id IN (".
          implode(",", $ids). ")";
      $db->setQuery($sql);
      $db->query();

      //save keywords
      $sql = "SELECT cat_id as id , metakey FROM #__mt_cats WHERE cat_id IN (".
          implode(",", $ids). ")";
      $db->setQuery($sql);
      $items = $db->loadObjectList();
      foreach($items as $item){
        $this->saveKeywords($item->metakey, $item->id);
      }
    }
  }

  public function GenerateDescriptions($ids){
    $db = JFactory::getDBO();
    foreach($ids as $key=>$value){
      if (!is_numeric($value)){
        unset($ids[$key]);
      }
    }
    $sql = "UPDATE #__mt_cats
        SET  metadesc=cat_desc
        WHERE cat_id IN (".implode(",", $ids).")";
    $db->setQuery($sql);
    $db->query();
  }

  public function getPages($lim0, $lim, $filter=null){
    $db = JFactory::getDBO();
    $sql = "SELECT SQL_CALC_FOUND_ROWS
        c.cat_id AS id,
        c.cat_name AS title,
        c.metakey AS metakey,
        c.cat_desc AS content
        FROM
        #__mt_cats c WHERE 1
        ";

    $search = JRequest::getVar("filter_search", "");
    //$category_id = JRequest::getVar("filter_category_id", "0");
    $filter_show_empty_keywords =
    JRequest::getVar("filter_show_empty_keywords", "-1");
    $filter_show_empty_descriptions =
    JRequest::getVar("filter_show_empty_descriptions", "-1");

    if ($search != ""){
      if (is_numeric($search)){
        $sql .= " AND c.cat_id=".$db->quote($search);
      }else{
        $sql .= " AND c.cat_name LIKE ".$db->quote('%'.$search.'%');
      }
    }
    if ($filter_show_empty_keywords != "-1"){
      $sql .= " AND (ISNULL(metakey) OR metakey='') ";
    }
    if ($filter_show_empty_descriptions != "-1"){
      $sql .= "AND (ISNULL(metadesc) OR metadesc='') ";
    }

    $sql .= " ORDER BY `lft` ASC";

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
      $rows[$i]->edit_url = "index.php?option=com_mtree&task=editcat&cat_id={$rows[$i]->id}";
    }
    return $rows;
  }

  public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys, $title_tags){
    $db = JFactory::getDBO();
    for($i = 0 ;$i < count($ids); $i++){
      /*customtitle=".$db->quote($metatitles[$i]).",*/
      $sql = "UPDATE #__mt_cats
          SET
          metadesc=".$db->quote($metadescriptions[$i]).",
              metakey=".$db->quote($metakeys[$i])."
                  WHERE cat_id=".$db->quote($ids[$i]);
      $db->setQuery($sql);
      $db->query();

      $sql = "INSERT INTO #__seoboss_metadata (
          item_id,
          item_type,
          title,
          title_tag)
          VALUES (
          ".$db->quote($ids[$i]).",
          {$this->code},
          ".$db->quote($metatitles[$i]).",
              ".$db->quote($title_tags[$i])."
                 ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , title_tag=".$db->quote($title_tags[$i]);
          $db->setQuery($sql);
          $db->query();

          $this->saveKeywords($metakeys[$i], $ids[$i]);


    }

  }
  public function saveKeywords($keys, $id){
    parent::saveKeywords($keys, $id,$this->code);
  }

  public function getMetadata($id){
    $db = JFactory::getDBO();

    $sql = "SELECT c.cat_id as id,
    c.cat_name as title,
    c.metakey AS metakeywords,
    c.metadesc as metadescription,
    m.title as metatitle
    FROM
    #__mt_cats c
    LEFT JOIN #__seoboss_metadata m ON m.item_id=c.cat_id and m.item_type={$this->code}
    WHERE c.cat_id=".$db->quote($id);
    $db->setQuery($sql);
    return $db->loadAssoc();
  }


  public function setMetadata($id, $data){
    $keywords = $data["metakeywords"];
    $title = isset($data["title"])?$data["title"]:"";
    $metatitle = $data["metatitle"];
    $metadescription = $data["metadescription"];

    $db = JFactory::getDBO();
    //Save metatitles and metadata
    $sql = "UPDATE #__mt_cats
        SET ".
        ($title?"cat_name= ".$db->quote($title).",":"")."
            metadesc=".$db->quote($metadescription).",
                metakey=".$db->quote($keywords)."
                    WHERE cat_id=".$db->quote($id);
    $db->setQuery($sql);
    $db->query();
    parent::setMetadata($id, $data);
  }

  public function getMetadataByRequest($query){
    $metadata = null;
    $params = array();
    parse_str($query, $params);
    if (isset($query["cat_id"]) && $query["cat_id"]){
      $metadata = $this->getMetadata($query["cat_id"]);
    }
    return $metadata;
  }

  public function setMetadataByRequest($query, $metadata){
    $params = array();
    parse_str($query, $params);
    if (isset($query["cat_id"]) && $query["cat_id"]){
      $this->setMetadata($query["cat_id"], $metadata);
    }
  }

  function getFilter(){
    $search = JRequest::getVar("filter_search", "");

    $filter_show_empty_keywords = JRequest::getVar("filter_show_empty_keywords", "-1");
    $filter_show_empty_descriptions = JRequest::getVar("filter_show_empty_descriptions", "-1");

    $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/>
            <button onclick="this.form.submit();">Go</button> ';


    $result .= '<br/>
        <label>Show only Items with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="filter_show_empty_keywords" '.($filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>
            <label>Show only Items with empty descriptions</label>
            <input type="checkbox" onchange="document.adminForm.submit();" name="filter_show_empty_descriptions" '.($filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
    return $result;
  }

  public function isAvailable(){
    require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
    return MetatagsContainerFactory::componentExists("com_mtree");
  }
}
?>
