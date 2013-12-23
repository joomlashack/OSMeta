<?php
/**
 * @category  Joomla Component
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once "MetatagsContainer.php";
class MenuItemMetatagsContainer extends MetatagsContainer{
	public $code=15;
        public function getMetatags($lim0, $lim, $filter=null){
		$db = JFactory::getDBO();
		$sql = "SELECT SQL_CALC_FOUND_ROWS c.id as id, c.{$this->getField()} AS title,
		        (SELECT GROUP_CONCAT(k.name SEPARATOR ',')
		            FROM #__osmeta_keywords k,
		            #__osmeta_keywords_items ki
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		       ) AS metakey,
		        m.description as metadesc,
		        m.title as metatitle,
		        m.title_tag as title_tag
		        FROM
		        #__menu c
		        LEFT JOIN
		        #__osmeta_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE 1";

		$search = JRequest::getVar("filter_search", "");
                $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
                $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
                $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");

        if ($search != ""){
        	if (is_numeric($search)){
        		$sql .= " AND c.id=".$db->quote($search);
        	}else{
        		$sql .= " AND c.{$this->getField()} LIKE ".$db->quote('%'.$search.'%');
        	}
        }
        if ($menu_type){
        	$sql .= " AND c.menutype=".$db->quote($menu_type);
        }

        if ($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND (ISNULL(m.description) OR m.description='') ";
        }
        if ($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " HAVING (ISNULL(metakey) OR metakey='') ";
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
            $rows[$i]->edit_url = "index.php?option=com_menus&task=item.edit&id={$rows[$i]->id}";
        }
        return $rows;
	}

  public function getPages($lim0, $lim, $filter=null){
    $db = JFactory::getDBO();
    $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.{$this->getField()} as title,
            (SELECT GROUP_CONCAT(k.name SEPARATOR ',')
                        FROM #__osmeta_keywords k,
                        #__osmeta_keywords_items ki
                        WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
                            AND ki.keyword_id=k.id
                   )  as metakey, c.published as state,
    '' AS content
     FROM
    #__menu c WHERE 1
    ";

    $search = JRequest::getVar("filter_search", "");
    $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
    $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
    $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");

    if ($search != ""){
      if (is_numeric($search)){
        $sql .= " AND c.id=".$db->quote($search);
      }else{
        $sql .= " AND c.{$this->getField()} LIKE ".$db->quote('%'.$search.'%');
      }
    }
    if ($menu_type){
      $sql .= " AND c.menutype=".$db->quote($menu_type);
    }

    if ($com_content_filter_show_empty_descriptions != "-1"){
      $sql .= " AND (ISNULL(m.description) OR m.description='') ";
    }
    if ($com_content_filter_show_empty_keywords != "-1"){
      $sql .= " HAVING (ISNULL(metakey) OR metakey='') ";
    }
    $db->setQuery($sql, $lim0, $lim);
    $rows = $db->loadObjectList();
    if ($db->getErrorNum()) {
      echo $db->stderr();
      return false;
    }
    for($i = 0 ; $i < count($rows);$i++){
      if ($rows[$i]->metakey){
        $rows[$i]->metakey = explode(",", $rows[$i]->metakey);
      }else{
        $rows[$i]->metakey = array("");
      }
      $rows[$i]->edit_url = "index.php?option=com_menus&task=item.edit&id={$rows[$i]->id}";
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
        $sql = "INSERT INTO #__osmeta_metadata (item_id,
         item_type, title, description
                    ,title_tag
           )VALUES (
         ".$db->quote($ids[$i]).",
         {$this->code},
         ".$db->quote($metatitles[$i]).",
         ".$db->quote($metadescriptions[$i])."
                     ,".$db->quote($title_tags!=null?$title_tags[$i]:'')."
        ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i])
                     .", title_tag=".$db->quote($title_tags!=null?$title_tags[$i]:'');
        $db->setQuery($sql);
        $db->query();
        parent::saveKeywords($metakeys[$i], $ids[$i],$this->code);
    }
  }
  public function saveKeywords($keys, $id, $itemTypeId=null){
    parent::saveKeywords($keys, $id,$itemTypeId?$itemTypeId:$this->code);
  }

  public function copyKeywordsToTitle($ids){
    $db = JFactory::getDBO();
    foreach($ids as $key=>$value){
      if (!is_numeric($value)){
        unset($ids[$key]);
      }
    }
    $sql = "SELECT c.id, (SELECT GROUP_CONCAT(k.name SEPARATOR ' ')
                FROM #__osmeta_keywords k,
                #__osmeta_keywords_items ki
                WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
                    AND ki.keyword_id=k.id
           ) AS metakey FROM #__menu c WHERE c.id IN (".implode(",", $ids).")";
    $db->setQuery($sql);
    $items = $db->loadObjectList();
    foreach($items as $item){
      if ($item->metakey != ''){
        $sql = "INSERT INTO #__osmeta_metadata
          (item_id, item_type, title, description)
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
      if (!is_numeric($value)){
        unset($ids[$key]);
      }
    }

    $sql = "SELECT item_id, title FROM #__osmeta_metadata WHERE item_id IN (".implode(",", $ids).") AND item_type={$this->code}";
    $db->setQuery($sql);
    $categories = $db->loadObjectList();
    foreach($categories as $category){
      if ($category->title){
        $keywords = preg_split("/[\s,-\?!\\\"\\\'\\.]+/", $category->title);
        foreach($keywords as $key=>$value){
          if (strlen($value) < 4){
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
        if (!is_numeric($value)){
          unset($ids[$key]);
        }
      }
      $sql = "SELECT id, {$this->getField()} as title FROM  #__menu WHERE id IN (".implode(",", $ids).")";
      $db->setQuery($sql);
      $items = $db->loadObjectList();
      foreach($items as $item){
        if ($item->title != ''){
          $sql = "INSERT INTO #__osmeta_metadata (item_id,
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
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT id, {$this->getField()} as title FROM #__menu  WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $categories = $db->loadObjectList();
        foreach($categories as $category){
            if ($category->title){
                $keywords = preg_split("/[\s,-\?!\\\"\\\'\\.]+/", $category->title);
                foreach($keywords as $key=>$value){
                    if (strlen($value) < 4){
                        unset($keywords[$key]);
                    }
                }
                $this->saveKeywords(implode(",", $keywords), $category->id);
            }
        }
    }

    public function GenerateDescriptions($ids){
    }

  public function getFilter(){
    $db = JFactory::getDBO();

    $search = JRequest::getVar("filter_search", "");
    $menu_type= JRequest::getVar("filter_menu_type", "mainmenu");
    $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_category_filter_show_empty_keywords", "-1");
    $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_category_filter_show_empty_descriptions", "-1");

    $result =  'Filter:
    <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter Menu ID"/>
  <button id="Go" onclick="this.form.submit();">Go</button>
  <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>

  &nbsp;&nbsp;&nbsp;';
          $sql = "SELECT menutype, title from #__menu_types ORDER BY title";
          $db->setQuery($sql);
          $sections = $db->loadObjectList();

  $result .= '<select name="filter_menu_type" id="filter_menutype" class="inputbox" size="1" onchange="document.adminForm.submit();">';

  foreach($sections as $section){
          $result .= '<option value="'.$section->menutype.'" '.($menu_type==$section->menutype?'selected="true"':'').'>'.$section->title.'</option>';
  }


  $result .= '</select>


  <br/>
  <label>Show only Menu Items with empty keywords</label>
  <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_keywords" '.($com_content_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>
  <label>Show only Menu Items with empty descriptions</label>
  <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_category_filter_show_empty_descriptions" '.($com_content_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
  return $result;

  }

	public function getItemData($id){
		$db = JFactory::getDBO();
		$sql = "SELECT c.id as id, c.{$this->getField()} as title,
		(SELECT GROUP_CONCAT(k.name SEPARATOR ',')
		            FROM #__osmeta_keywords k,
		            #__osmeta_keywords_items ki
		            WHERE ki.item_id=c.id and ki.item_type_id={$this->code}
		                AND ki.keyword_id=k.id
		       ) as metakeywords,
         m.description as metadescription, m.title as metatitle
         FROM
        #__menu c
        LEFT JOIN
        #__osmeta_metadata m ON m.item_id=c.id and m.item_type={$this->code} WHERE c.id=".$db->quote($id);
		$db->setQuery($sql);
		return $db->loadAssoc();
	}


  public function setItemData($id, $data){
    $db = JFactory::getDBO();
    $sql = "UPDATE #__menu SET
        `{$this->getField()}` = ".$db->quote($data["title"])."
        WHERE `id`=".$db->quote($id);
    $db->setQuery($sql);
    $db->query();
    $this->saveMetadata($id, $this->code, $data);
  }

  public function getTypeId(){
    return $this->code;
  }

  function getMetadataByRequest($query){
    $result = null;
    $params = array();
      parse_str($query, $params);
      $metadata = null;
      if (isset($params["Itemid"])){
        $metadata = $this->getMetadata($params["Itemid"]);
      }
      return $metadata;
    return $result;
  }

  public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($query, $params);
      if (isset($params["Itemid"]) && $params["Itemid"]){
        $this->setMetadata($params["Itemid"], $data);
      }
    }

  private $field=null;
        private function getField(){
          if ($this->field==null){
	        jimport("joomla.version");
	        $version = new JVersion();
                if ($version->RELEASE=="1.5"){
                  $this->field = "name";
                }else{
                  $this->field = "title";
                }
          }
          return $this->field;
        }
   public function isAvailable(){
     return true;
   }
}
?>
