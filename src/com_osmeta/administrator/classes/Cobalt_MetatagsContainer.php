<?php
/**
 * @category   Joomla Component
 * @package    Osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once "MetatagsContainer.php";
class Cobalt_MetatagsContainer extends MetatagsContainer{
    public $code=7;
    public function getMetatags($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        c.id as id,
        c.title AS title,
        c.meta_key AS metakey,
        c.meta_descr as metadesc,
        m.title as metatitle
         FROM
        #__js_res_record c
        LEFT JOIN #__osmeta_metadata m ON c.id=m.item_id AND m.item_type=".$db->quote($this->code)."
        WHERE 1 ";

        $search = JRequest::getVar("filter_search", "");
        $section_id = JRequest::getVar("section_id", "0");
        $type_id = JRequest::getVar("type_id", "0");
        $status_id = JRequest::getVar("status_id");

        $show_empty_keywords =
            JRequest::getVar("show_empty_keywords", "-1");
        $show_empty_descriptions =
            JRequest::getVar("show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.id=".$db->quote($search);
            }else{
                $sql .= " AND c.title LIKE ".$db->quote('%'.$search.'%');
            }
        }

        if ($section_id > 0){
            $sql .= " AND c.section_id=".$db->quote($section_id);

        }
        if ($type_id > 0){
            $sql .= " AND c.type_id=".$db->quote($type_id);

        }
        if ($status_id ){
            switch($status_id){
                case "P":
                    $sql .= " AND c.published=1";
                    break;
                case "U":
                    $sql .= " AND c.published=0";
                    break;
                case "A":
                    $sql .= " AND c.published=2";
                    break;
            }
        }
        if ($show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(meta_key) OR meta_key='') ";
        }
        if ($show_empty_descriptions != "-1"){
            $sql .= "AND (ISNULL(meta_descr) OR meta_descr='') ";
        }

        //Sorting
        $order = JRequest::getCmd("filter_order", "title");
        $order_dir = JRequest::getCmd("filter_order_Dir", "ASC");
        switch($order){
            case "meta_title":
                $sql .= " ORDER BY title ";
                break;
            case "meta_key":
                $sql .= " ORDER BY meta_key ";
                break;
            case "meta_desc":
                $sql .= " ORDER BY meta_descr ";
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
            $rows[$i]->edit_url = "#";
        }
        return $rows;
    }

    public function getPages($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
            	c.id AS id,
            	c.title AS title,
            	c.meta_key AS metakey,
            	c.title AS content
             FROM
            #__js_res_record c WHERE 1
            ";
        $search = JRequest::getVar("filter_search", "");
        $section_id = JRequest::getVar("section_id", "0");
        $type_id = JRequest::getVar("type_id", "0");
        $status_id = JRequest::getVar("status_id");

        $show_empty_keywords =
        JRequest::getVar("show_empty_keywords", "-1");
        $show_empty_descriptions =
        JRequest::getVar("show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.virtuemart_product_id=".$db->quote($search);
            }else{
                $sql .= " AND c.product_name LIKE ".$db->quote('%'.$search.'%');
            }
        }
        if ($section_id > 0){
            $sql .= " AND c.section_id=".$db->quote($section_id);

        }
        if ($type_id > 0){
            $sql .= " AND c.type_id=".$db->quote($type_id);

        }
        if ($status_id ){
            switch($status_id){
                case "P":
                    $sql .= " AND c.published=1";
                    break;
                case "U":
                    $sql .= " AND c.published=0";
                    break;
                case "A":
                    $sql .= " AND c.published=2";
                    break;
            }
        }
        if ($show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(meta_key) OR meta_key='') ";
        }
        if ($show_empty_descriptions != "-1"){
            $sql .= "AND (ISNULL(meta_descr) OR meta_descr='') ";
        }
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
            $rows[$i]->edit_url = "#";
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
		$sql = "SELECT id, meta_key FROM #__js_res_record WHERE id IN (".implode(",", $ids).")";
		$db->setQuery($sql);
		$items = $db->loadObjectList();
		foreach($items as $item){
			if ($item->meta_key != ''){
			$sql = "INSERT INTO #__osmeta_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             ".$db->quote($this->code).",
             ".$db->quote($item->meta_key).",
             ''
            ) ON DUPLICATE KEY UPDATE title=".$db->quote($item->meta_key);

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
        $sql = "SELECT item_id, title FROM #__osmeta_metadata WHERE item_id IN (".implode(",", $ids).") AND item_type=".$db->quote($this->code);
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
            $sql = "UPDATE #__js_res_record SET meta_key=".$db->quote($item->title)."
            WHERE id=".$db->quote($item->item_id);
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
        $sql = "SELECT id, title FROM  #__js_res_record WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
            $sql = "INSERT INTO #__osmeta_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             ".$db->quote($this->code).",
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
        $sql = "UPDATE #__js_res_record SET meta_key=title WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->query();
    }

    public function GenerateDescriptions($ids){
      $max_description_length = 500;
      $model = OSModel::getInstance("options", "OsmetaModel");
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
        $sql = "SELECT id, title as introtext FROM  #__js_res_record WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->introtext != ''){
            $introtext = strip_tags($item->introtext);
            if (strlen($introtext) > $max_description_length){
                $introtext = substr($introtext, 0, $max_description_length);
            }
            $sql = "INSERT INTO #__osmeta_metadata (item_id,
             item_type, title, description)
             VALUES (
             ".$db->quote($item->id).",
             ".$db->quote($this->code).",

             '',
             ".$db->quote($introtext)."
            ) ON DUPLICATE KEY UPDATE description=".$db->quote($introtext);

            $db->setQuery($sql);
            $db->query();

            $sql = "UPDATE #__js_res_record SET meta_descr=".$db->quote($introtext)."
            WHERE id=".$db->quote($item->id);

            $db->setQuery($sql);
            $db->query();
            }
        }
    }


    public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys){
        $db = JFactory::getDBO();
        for($i = 0 ;$i < count($ids); $i++){
            $sql = "UPDATE #__js_res_record
             SET
             meta_descr=".$db->quote($metadescriptions[$i]).",
             meta_key=".$db->quote($metakeys[$i])."
             WHERE id=".$db->quote($ids[$i]);
            $db->setQuery($sql);
            $db->query();

            $sql = "INSERT INTO #__osmeta_metadata (item_id,
                    			 item_type, title, description)
                    			 VALUES (
                    			 ".$db->quote($ids[$i]).",
                    			 ".$db->quote($this->code).",
                    			 ".$db->quote($metatitles[$i]).",
                    			 ".$db->quote($metadescriptions[$i])."
                    			) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i]);
            $db->setQuery($sql);
            $db->query();

            $this->saveKeywords($metakeys[$i], $ids[$i]);
        }
    }

    public function getTypeId(){
      return $this->code;
    }

    public function getMetadata($id){
        $db = JFactory::getDBO();
		$sql = "SELECT c.id as id, c.title as title, c.meta_key as metakeywords,
         c.meta_descr as metadescription
         FROM
        #__js_res_record c
         WHERE c.id=".$db->quote($id);
		$db->setQuery($sql);
		$data=$db->loadAssoc();
        $parentData = parent::getMetadata($id);
        $data["title_tag"] = $parentData["title_tag"];
        $data["metatitle"] = $parentData["metatitle"];
        return $data;
    }

    public function setMetadata($id, $data){
        $db = JFactory::getDBO();
        $sql = "UPDATE #__js_res_record SET .".
        (isset($data["title"]) && $data["title"]? "`title` = ".$db->quote($data["title"]).", ":"")."
        `meta_key` = ".$db->quote($data["metakeywords"]).",
        `meta_descr` = ".$db->quote($data["metadescription"])."
        WHERE `id`=".$db->quote($id);
        $db->setQuery($sql);
        $db->query();

        parent::setMetadata($id, $data);
    }

    public function getMetadataByRequest($query){
      $params = array();
      parse_str($query, $params);
      $metadata = null;
      if (isset($params["id"])){
        $metadata = $this->getMetadata($params["id"]);
      }
      return $metadata;
    }

    public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($url, $params);
      if (isset($params["id"]) && $params["id"]){
        $this->setMetadata($params["id"], $data);
      }
    }

    function getFilter(){
        $search = JRequest::getVar("filter_search", "");
        $section_id = JRequest::getVar("section_id", "");
        $type_id = JRequest::getVar("type_id", "");
        $status_id = JRequest::getVar("status_id", "");

        $show_empty_keywords = JRequest::getVar("show_empty_keywords", "-1");
        $show_empty_descriptions = JRequest::getVar("show_empty_descriptions", "-1");

                $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/>
        <button onclick="this.form.submit();">Go</button>
        <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>

        &nbsp;&nbsp;&nbsp;';

        $result .=  "<select name=\"type_id\" onchange=\"document.adminForm.submit();\">".
                "<option value=\"\">Select Field Type</option>";

        $db = JFactory::getDBO();
        $db->setQuery("SELECT id , name FROM #__js_res_types ORDER BY name ASC");
        $types = $db->loadObjectList();
        foreach($types as $type){
            $result .= "<option value=\"{$type->id}\" ".($type->id==$type_id?"selected=\"true\"":"").">{$type->name}</option>";
        }
        $result .= "</select>";

        $result .=  "<select name=\"section_id\" onchange=\"document.adminForm.submit();\">".
        "<option value=\"\">Select Section</option>";

        $db = JFactory::getDBO();
        $db->setQuery("SELECT id , name FROM #__js_res_sections ORDER BY name ASC");
        $sections = $db->loadObjectList();
        foreach($sections as $section){
            $result .= "<option value=\"{$section->id}\" ".($section->id==$section_id?"selected=\"true\"":"").">{$section->name}</option>";
        }
        $result .= "</select>";

        $result .=  "<select name=\"status_id\" onchange=\"document.adminForm.submit();\">".
                "<option value=\"\">Select Status</option>";

        $result .= "<option value=\"P\" ".("P"==$status_id?"selected=\"true\"":"").">Published</option>";
        $result .= "<option value=\"U\" ".("U"==$status_id?"selected=\"true\"":"").">Unpublished</option>";
        $result .= "<option value=\"A\" ".("A"==$status_id?"selected=\"true\"":"").">Archived</option>";

        $result .= "</select>";

        $result .= '<br/>
        <label>Show only Items with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="show_empty_keywords" '.($show_empty_keywords!="-1"?'checked="yes" ':'').'/>
        <label>Show only Items with empty descriptions</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="show_empty_descriptions" '.($show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
        return $result;
    }

    public function isAvailable(){
      require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
      return MetatagsContainerFactory::componentExists("com_cobalt");
    }
}
