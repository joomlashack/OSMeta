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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 
require_once "MetatagsContainer.php";
class JS_Team_MetaTagsContainer extends MetatagsContainer{
    private $itemType=5;
    public function getMetatags($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.t_name as title, ( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->itemType}
		                AND ki.keyword_id=k.id
		        ) AS metakey,
		  m.description as metadesc,  
		 m.title as metatitle 
		 FROM
		#__bl_teams c
		LEFT JOIN
		#__seoboss_metadata m ON m.item_id=c.id and m.item_type={$this->itemType} WHERE 1";
	
        $search = JRequest::getVar("filter_search", "");
	var_dump($search);
        $cat_id = JRequest::getVar("com_content_filter_catid", "0");
        $author_id = JRequest::getVar("com_content_filter_authorid", "0");
        $state = JRequest::getVar("com_content_filter_state", "");
        $com_content_filter_show_empty_keywords =
            JRequest::getVar("com_content_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions =
            JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        if($search != ""){
            if(is_numeric($search)){
                $sql .= " AND c.id=".$db->quote($search);
            }else{
                $sql .= " AND c.t_name LIKE ".$db->quote('%'.$search.'%');
            }
        }

        if( $cat_id > 0 ){
            $sql .= " AND c.catid=".$db->quote($cat_id);
        }

        if( $author_id > 0 ){
            $sql .= " AND c.created_by=".$db->quote($author_id);
        }
        switch($state){
            case 'P':
                $sql .= " AND c.state=1";
                break;
            case 'U':
                $sql .= " AND c.state=0";
                break;
            case 'A':
                $sql .= " AND c.state=-1";
                break;
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
        if($order_dir == "asc"){
            $sql .= " ASC";
        }else{
            $sql .= " DESC";
        }

        $db->setQuery( $sql, $lim0, $lim );
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }

        for($i = 0 ; $i < count($rows);$i++){
            $rows[$i]->edit_url = "index.php?option=com_joomsport&task=team_edit&cid[]={$rows[$i]->id}";
        }
        return $rows;
    }
    
    public function copyKeywordsToTitle($ids){
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if(!is_numeric($value)){
                unset($ids[$key]);
            }else{
                $sql = "SELECT k.name 
                  FROM #__seoboss_keywords k, #__seoboss_keywords_items ki 
                  WHERE k.id=ki.keyword_id 
                     AND ki.item_id=".$db->quote($value).
                    "  AND ki.item_type_id={$this->itemType}";
                $db->setQuery($sql);
                $keywords = $db->loadObjectList();
                if(count($keywords) > 0){
                    $keywords_arr = array();
                    foreach($keywords as $keyword){
                        $keywords_arr[] = $keyword->name;
                    }
                    $keywords_str = implode("," , $keywords_arr);
                    $sql = "INSERT INTO #__seoboss_metadata (item_id,
                        item_type, title, description)
                        VALUES (
                        ".$db->quote($value).",
                        {$this->itemType},
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
            if(!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT item_id, title FROM #__seoboss_metadata WHERE item_type={$this->itemType} AND item_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if($item->title != ''){
            $this->saveKeywords($item->title, $item->item_id);
            }
        }
    }
    
    public function copyItemTitleToTitle($ids){
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if(!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT id as id, t_name as title FROM  #__bl_teams WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if($item->title != ''){
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             {$this->itemType},
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
            if(!is_numeric($value)){
                unset($ids[$key]);
            }else{
                $sql = "SELECT t_name FROM #__bl_teams WHERE id=".$db->quote($value);
                $db->setQuery($sql);
                $title = $db->loadResult();
                if($title){
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
            if(!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT id as id, t_descr as introtext FROM  #__bl_teams WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if($item->introtext != ''){
            $introtext = strip_tags($item->introtext);
            if(strlen($introtext) > $max_description_length){
              $introtext = substr($introtext, 0, $max_description_length);
            }
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             {$this->itemType},
             
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
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id AS id, c.t_name AS title,
        ( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
            FROM #__seoboss_keywords k, 
            #__seoboss_keywords_items ki 
            WHERE ki.item_id=c.id and ki.item_type_id={$this->itemType}
                AND ki.keyword_id=k.id
        ) AS metakey,
        c.t_descr  AS content
         FROM 
        #__bl_teams c WHERE 1
        ";
        
        $search = JRequest::getVar("filter_search", "");
       
        if($search != ""){
            if(is_numeric($search)){
                $sql .= " AND c.id=".$db->quote($search);
            }else{
                $sql .= " AND c.t_name LIKE ".$db->quote('%'.$search.'%');
            }
        }
      
        
        $db->setQuery( $sql, $lim0, $lim );
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        // Get outgoing links and keywords density
        for($i = 0 ; $i < count($rows);$i++){
            if($rows[$i]->metakey){
                $rows[$i]->metakey = explode(",", $rows[$i]->metakey);
            }else{
                $rows[$i]->metakey = array("");
            }
            $rows[$i]->edit_url = "index.php?option=com_k2&view=item&cid={$rows[$i]->id}";
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
			 {$this->itemType},
			 ".$db->quote($metatitles[$i]).",
			 ".$db->quote($metadescriptions[$i])."
			 ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i]);
            $db->setQuery($sql);
            $db->query();
            parent::saveKeywords($metakeys[$i], $ids[$i],$this->itemType);
        }
    }
    
    public function getMetadata($id){
        $db = JFactory::getDBO();
        $sql = "SELECT c.id as id, c.t_name as title,
         FROM 
        #__bl_teams c
        WHERE c.id=".$db->quote($id);
        $db->setQuery($sql);
        $data=$db->loadAssoc();
        $parentData = parent::getMetadata($id);
        $data["title_tag"] = $parentData["title_tag"];
        $data["metatitle"] = $parentData["metatitle"];
        $data["metakeywords"] = $parentData["metakeywords"];
        $data["metadescription"] = $parentData["metadescription"];
        return $data;
    }
    
    public function getMetadataByRequest($query){
      $params = array();
      parse_str($query, $params);
      $metadata = null;
      if(isset($params["tid"])){
        $metadata = $this->getMetadata($params["tid"]);
      }
      return $metadata;
    }
    
    public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($url, $params);
      if( isset($params["tid"]) && $params["tid"]){
        $this->setMetadata($params["tid"], $data);
      }
    }
    //public function setItemData($id, $data){
    //    $this->saveMetadata($id, $this->itemType, $data);
   // }
    
    function getFilter(){
        $search = JRequest::getVar("filter_search", "");
       
        $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/> 
        <button onclick="this.form.submit();">Go</button> 
        <button onclick="document.getElementById(\'search\').value=\'\';;this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>
         ';               
     
        return $result;
    }
    
    public function getTypeId(){
      return $this->itemType;
    }
    
    public function isAvailable(){
      require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
      return MetatagsContainerFactory::componentExists("com_joomsport");
    }
}
