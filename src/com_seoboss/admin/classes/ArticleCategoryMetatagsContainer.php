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

require_once "MetatagsContainer.php";
class ArticleCategoryMetatagsContainer extends MetatagsContainer{
	public $code=4;
	public function getTypeId(){
	  return $this->code;
	}
	public function getMetatags($lim0, $lim, $filter=null){
		$db = JFactory::getDBO();
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id as id, c.title AS title,
		        ( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        ) AS metakey,
		        m.description as metadesc, 
		        m.title as metatitle, 
		        m.title_tag as title_tag
		         FROM 
		        #__categories c
		        INNER JOIN #__sections AS s ON s.id=c.section
		        LEFT JOIN
		        #__seoboss_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE 1";
		
		$search = JRequest::getVar("com_content_category_filter_search", "");
        $section_id= JRequest::getVar("com_content_category_filter_sectionid", "-1");
        $state = JRequest::getVar("com_content_category_filter_state", "");
        $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");
        
        if($search != ""){
        	if(is_numeric($search)){
        		$sql .= " AND c.id=".$db->quote($search);
        	}else{
        		$sql .= " AND c.title LIKE ".$db->quote('%'.$search.'%');
        	}
        }
        if( $section_id > 0 ){
        	$sql .= " AND c.section=".$db->quote($section_id);
        }
        switch($state){
        	case 'P':
        		$sql .= " AND c.published=1";
        		break;
        	case 'U':
        		$sql .= " AND c.published=0";
                break;
        }
        
	    if($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND ( ISNULL(m.description) OR m.description='') ";
        }
        if($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " HAVING ( ISNULL(metakey) OR metakey='') ";
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
            case "title_tag":
                $sql .= " ORDER BY title_tag ";
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
            $rows[$i]->edit_url = "index.php?option=com_categories&section=com_content&task=edit&cid[]={$rows[$i]->id}&type=content";
        }
        return $rows;
	}
	
    public function getPages($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.title,
		( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        )  as metakey, c.published as state,
        c.description AS content 
         FROM 
        #__categories c WHERE 1
        ";
        
        $search = JRequest::getVar("com_content_category_filter_search", "");
        $section_id= JRequest::getVar("com_content_category_filter_sectionid", "-1");
        $state = JRequest::getVar("com_content_category_filter_state", "");
        $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");
        
        if($search != ""){
        	if(is_numeric($search)){
        		$sql .= " AND c.id=".$db->quote($search);
        	}else{
        		$sql .= " AND c.title LIKE ".$db->quote('%'.$search.'%');
        	}
        }
        if( $section_id > 0 ){
        	$sql .= " AND c.section=".$db->quote($section_id);
        }
        switch($state){
        	case 'P':
        		$sql .= " AND c.published=1";
        		break;
        	case 'U':
        		$sql .= " AND c.published=0";
                break;
        }
        
	    if($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND ( ISNULL(m.description) OR m.description='') ";
        }
        if($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " HAVING ( ISNULL(metakey) OR metakey='') ";
        }
	    $db->setQuery( $sql, $lim0, $lim );
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        for($i = 0 ; $i < count($rows);$i++){
            if($rows[$i]->metakey){
                $rows[$i]->metakey = explode(",", $rows[$i]->metakey);
            }else{
                $rows[$i]->metakey = array("");
            }
            $rows[$i]->edit_url = "index.php?option=com_categories&section=com_content&task=edit&cid[]={$rows[$i]->id}&type=content";
        }
        return $rows;
    }
    
	public function saveMetatags($ids
		,$metatitles
		,$metadescriptions
		,$metakeys
		,$title_tags=null		
   		){
	$db = JFactory::getDBO();
        for($i = 0 ;$i < count($ids); $i++){
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description
			,title_tag
		)VALUES (
             ".$db->quote($ids[$i]).",
             {$this->code},
             ".$db->quote($metatitles[$i]).",
             ".$db->quote($metadescriptions[$i])."
			 ,".$db->quote($title_tags!=null?$title_tags[$i]:'')."
             ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i]
			 .", title_tag=".$db->quote($title_tags!=null?$title_tags[$i]:'')
			);
            $db->setQuery($sql);
            $db->query();
            parent::saveKeywords($metakeys[$i], $ids[$i],$this->code);
        }
	}
	public function saveKeywords($keys, $id){
	    parent::saveKeywords($keys, $id,$this->code);
	}
	public function copyKeywordsToTitle($ids){
		$db = JFactory::getDBO();
		foreach($ids as $key=>$value){
			if(!is_numeric($value)){
				unset($ids[$key]);
			}
		}
		$sql = "SELECT c.id, ( SELECT GROUP_CONCAT(k.name SEPARATOR ' ') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        ) AS metakey, FROM #__categories c WHERE c.id IN (".implode(",", $ids).")";
		$db->setQuery($sql);
		$items = $db->loadObjectList();
		foreach($items as $item){
			if($item->metakey != ''){
			$sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             {$this->code},
             ".$db->quote($item->metakey).",
             ''
             ) ON DUPLICATE KEY UPDATE title=".$db->quote($item->metakey);
			
			$db->setQuery($sql);
            $db->query();
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
        
        $sql = "SELECT item_id, title FROM #__seoboss_metadata WHERE item_id IN (".implode(",", $ids).") AND item_type={$this->code}";
        $db->setQuery($sql);
        $categories = $db->loadObjectList();
        foreach($categories as $category){
            if($category->title){
                $keywords = preg_split("/[\s,-\?!\\\"\\\'\\.]+/", $category->title);
                foreach($keywords as $key=>$value){
                    if(strlen($value) < 4){
                        unset($keywords[$key]);
                    }
                }
                $this->saveKeywords(implode(",", $keywords), $category->item_id);
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
        $sql = "SELECT id, title FROM  #__categories WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if($item->title != ''){
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             {$this->code},
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
            }
        }
        $sql = "SELECT id, title FROM #__categories  WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $categories = $db->loadObjectList();
        foreach($categories as $category){
            if($category->title){
                $keywords = preg_split("/[\s,-\?!\\\"\\\'\\.]+/", $category->title);
                foreach($keywords as $key=>$value){
                    if(strlen($value) < 4){
                        unset($keywords[$key]);
                    }
                }
                $this->saveKeywords(implode(",", $keywords), $category->id);
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
        $sql = "SELECT id, description FROM  #__categories WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if($item->description != ''){
            $introtext = strip_tags($item->description);
            if(strlen($introtext) > $max_description_length){
                $introtext = substr($introtext, 0, $max_description_length);
            }
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             {$this->code},
             
             '',
             ".$db->quote($introtext)."
             ) ON DUPLICATE KEY UPDATE description=".$db->quote($introtext);
            
            $db->setQuery($sql);
            $db->query();
            }
        }
    }
	
	public function getFilter(){
		$db = JFactory::getDBO();
		
		$search = JRequest::getVar("com_content_category_filter_search", "");
		$section_id= JRequest::getVar("com_content_category_filter_sectionid", "-1");
		$state = JRequest::getVar("com_content_category_filter_state", "");
		$com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
		$com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");

		$result =  'Filter:                        
		<input type="text" name="com_content_category_filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Article ID"/> 
        <button id="Go" onclick="this.form.submit();">Go</button> 
        <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>
                        
        &nbsp;&nbsp;&nbsp;';
		$sql = "SELECT id, title from #__sections ORDER BY title";
		$db->setQuery($sql);
		$sections = $db->loadObjectList();
		
        $result .= '<select name="com_content_category_filter_sectionid" id="filter_sectionid" class="inputbox" size="1" onchange="document.adminForm.submit();">
        <option value="-1" '.($section_id==-1?'selected="true"':'').'>- Select Section -</option>
        <option value="0" '.($section_id==0?'selected="true"':'').'>Uncategorised</option>';
                
        foreach($sections as $section){
        	$result .= '<option value="'.$section->id.'" '.($section_id==$section->id?'selected="true"':'').'>'.$section->title.'</option>';
        }
        
        
        $result .= '</select>
        
        
        <select name="com_content_category_filter_state" id="filter_state" class="inputbox" size="1" onchange="submitform( );">
        <option value=""  >- Select State -</option>
        <option value="P" '.($state=='P'?'selected="selected"':'').'>Published</option>
        <option value="U" '.($state=='U'?'selected="selected"':'').'>Unpublished</option>
        </select>
        <br/>
        <label>Show only Article Categories with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_keywords" '.($com_content_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>                
        <label>Show only Article Categories with empty descriptions</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_descriptions" '.($com_content_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
        return $result;
         
	}
	
	public function getItemData($id){
		$db = JFactory::getDBO();
		$sql = "SELECT c.id as id, c.title as title, 
		( SELECT GROUP_CONCAT(k.name SEPARATOR ',') 
		            FROM #__seoboss_keywords k, 
		            #__seoboss_keywords_items ki 
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		        ) as metakeywords,
         m.description as metadescription, m.title as metatitle 
         FROM 
        #__categories c
        LEFT JOIN
        #__seoboss_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE c.id=".$db->quote($id);
		$db->setQuery($sql);
		return $db->loadAssoc();
	}
	
	
	public function setItemData($id, $data){
		$db = JFactory::getDBO();
        $sql = "UPDATE #__categories SET
        `title` = ".$db->quote($data["title"])."
        WHERE `id`=".$db->quote($id);
        $db->setQuery($sql);
        $db->query();
        $this->saveMetadata($id, $this->code, $data);
	}
	
	function getMetadataByRequest($query){
	  $params = array();
	  parse_str($query, $params);
	  $metadata = null;
	  if(isset($params["id"])){
	    $metadata = $this->getMetadata($params["id"]);
	  }
	  return $metadata;
	}
	
	public function setMetadataByRequest($url,$data) {
	  $params = array();
	  parse_str($url, $params);
	  if( isset($params["id"]) && $params["id"]){
	    $this->setMetadata($params["id"], $data);
	  }
	}
	
	public function isAvailable(){
	  require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
	  return MetatagsContainerFactory::getJoomlaVersion() == "1.5";
	}
}
?>