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

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once "MetatagsContainer.php";
class Hikashop_MetaTagsContainer extends MetatagsContainer{
    private $itemType=10;
    public function getMetatags($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $sql = "SELECT SQL_CALC_FOUND_ROWS c.product_id as id, c.product_name as title, c.product_keywords as metakey,
		 c.product_meta_description  as metadesc, m.title as metatitle, c.product_page_title as title_tag
		 FROM
		#__hikashop_product c
		LEFT JOIN
		#__seoboss_metadata m ON m.item_id=c.product_id  and m.item_type={$this->itemType} WHERE 1";

        $search = JRequest::getVar("filter_search", "");
        $cat_id = JRequest::getVar("filter_category_id", "0");
        $author_id = JRequest::getVar("com_content_filter_authorid", "0");
        $state = JRequest::getVar("com_content_filter_show_published", "");

        $com_content_filter_show_empty_keywords =
            JRequest::getVar("com_content_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions =
            JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.product_id=".$db->quote($search);
            }else{
                $sql .= " AND c.product_name  LIKE ".$db->quote('%'.$search.'%');
            }
        }

        if ($cat_id > 0){
            $sql .= " AND EXISTS (SELECT 1 FROM #__hikashop_product_category WHERE #__hikashop_product_category.category_id=".$db->quote($cat_id)." AND #__hikashop_product_category.product_id=c.product_id)";
        }

        if ($author_id > 0){
            $sql .= " AND c.product_created =".$db->quote($author_id);
        }
        switch($state){
            case '1':
                $sql .= " AND c.product_published =1";
                break;

        }
        if ($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(c.product_keywords) OR c.product_keywords='') ";
        }
        if ($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND (ISNULL(c.product_meta_description) OR c.product_meta_description='') ";
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
            $rows[$i]->edit_url = "index.php?option=com_hikashop&ctrl=product&task=edit&cid[]={$rows[$i]->id}";
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
                    "  AND ki.item_type_id={$this->itemType}";
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
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        $sql = "SELECT item_id, title FROM #__seoboss_metadata WHERE item_type={$this->itemType} AND item_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
            $this->saveKeywords($item->title, $item->item_id, $this->itemType);
			$query = "UPDATE #__hikashop_product SET product_keywords = '".$item->title."' WHERE product_id = ".$item->item_id;

			$db->setQuery($query);
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
        $sql = "SELECT product_id as id, product_name as title FROM  #__hikashop_product WHERE product_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();
        foreach($items as $item){
            if ($item->title != ''){
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
            if (!is_numeric($value)){
                unset($ids[$key]);
            }else{
                $sql = "SELECT product_name as title FROM #__hikashop_product WHERE product_id=".$db->quote($value);
                $db->setQuery($sql);
                $title = $db->loadResult();
                if ($title){
                    $this->saveKeywords($title, $value, $this->itemType);
					$query = "UPDATE #__hikashop_product SET product_keywords = '".$title."' WHERE product_id = ".$value;

					$db->setQuery($query);
					$db->query();


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
        $sql = "SELECT product_id as id, product_description   as introtext FROM  #__hikashop_product WHERE product_id IN (".implode(",", $ids).")";
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
             {$this->itemType},

             '',
             ".$db->quote($introtext)."
            ) ON DUPLICATE KEY UPDATE description=".$db->quote($introtext);

            $db->setQuery($sql);
            $db->query();

            $sql = "UPDATE #__hikashop_product SET product_meta_description=".$db->quote($introtext)."
                WHERE product_id=".$db->quote($item->id);

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
            WHERE ki.item_id=c.product_id and ki.item_type_id={$this->itemType}
                AND ki.keyword_id=k.id
       ) AS metakey,
        c.product_description AS content
         FROM
        #__hikashop_product c WHERE 1
        ";

        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "0");
        $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");
        $state = JRequest::getVar("com_content_filter_show_published", "");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.product_id=".$db->quote($search);
            }else{
                $sql .= " AND c.product_name LIKE ".$db->quote('%'.$search.'%');
            }
        }
      //  if ($category_id > 0){
      //      $sql .= " AND c.catid=".$db->quote($category_id);
      //  }
        if ($com_content_filter_show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(c.product_keywords) OR c.product_keywords ='') ";
        }
        if ($com_content_filter_show_empty_descriptions != "-1"){
            $sql .= " AND (ISNULL(c.product_meta_description) OR c.product_meta_description='') ";
        }
        switch($state){
            case '1':
                $sql .= " AND c.product_published =1";
                break;

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
            $rows[$i]->edit_url = "index.php?option=com_hikashop&ctrl=product&task=edit&cid[]={$rows[$i]->id}";
        }
        return $rows;
    }

    public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys ,$title_tags=null	){
        $db = JFactory::getDBO();
        for($i = 0 ;$i < count($ids); $i++){
            $sql = "UPDATE #__hikashop_product SET product_keywords =".$db->quote($metakeys[$i])." , product_meta_description=".$db->quote($metadescriptions[$i])." , product_page_title = ".$db->quote($title_tags!=null?$title_tags[$i]:'')." WHERE product_id=".$db->quote($ids[$i]);
            $db->setQuery($sql);
            $db->query();
            $sql = "INSERT INTO #__seoboss_metadata (item_id,
			 item_type, title, description, title_tag)
			 VALUES (
			 ".$db->quote($ids[$i]).",
			 {$this->itemType},
			 ".$db->quote($metatitles[$i]).",
			 ".$db->quote($metadescriptions[$i]).",
			 ".$db->quote($title_tags!=null?$title_tags[$i]:'')."
			) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i])." , title_tag = ".$db->quote($title_tags!=null?$title_tags[$i]:'');

			$db->setQuery($sql);
            $db->query();
            parent::saveKeywords($metakeys[$i], $ids[$i],$this->itemType);
        }
    }

    public function getMetadata($id){
        $db = JFactory::getDBO();
        $sql = "
        SELECT c.product_id as id,
          c.product_name as title,
          c.product_meta_description as metadescription,
          c.product_keywords as metakeywords
        FROM #__hikashop_product c
        WHERE c.product_id =".$db->quote($id);
        $db->setQuery($sql);
        $metadata = $db->loadAssoc();
        $sb_metadata = parent::getMetadata($id);
        $metadata["metatitle"] = $sb_metadata["metatitle"];
        $metadata["title_tag"] = $sb_metadata["title_tag"];
        return $metadata;
    }


    public function setMetadata($id, $data){
      $db = JFactory::getDBO();
      $sql = "
      UPDATE #__hikashop_product
      SET ".
      (isset($data["title"])&&$data["title"]?
      "product_page_title =".$db->quote($data["title"]).",":"").
      "product_meta_description=".$db->quote($data["metadescription"]).",
       product_keywords=".$db->quote($data["metakeywords"])."
      WHERE product_id=".$db->quote($id);
      $db->setQuery($sql);
      $db->query();

      parent::setMetadata($id, $data);
    }


    function getFilter(){
        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "");
		$com_content_filter_show_published = JRequest::getVar("com_content_filter_show_published", "-1");

        $com_content_filter_show_empty_keywords = JRequest::getVar("com_content_filter_show_empty_keywords", "-1");
        $com_content_filter_show_empty_descriptions = JRequest::getVar("com_content_filter_show_empty_descriptions", "-1");

        $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/>
        <button onclick="this.form.submit();">Go</button>
        <button onclick="document.getElementById(\'search\').value=\'\';;this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>

        &nbsp;&nbsp;&nbsp;';
        $db = JFactory::getDBO();
        $db->setQuery("SELECT category_id, category_name FROM #__hikashop_category WHERE category_type LIKE 'product'");
        $categories = $db->loadObjectList();
        $result .=  "<select name=\"filter_category_id\" onchange=\"document.adminForm.submit();\">".
        "<option value=\"\">Select Category</option>";

        foreach($categories as $category){
            $result.= "<option value=\"{$category->category_id}\" ".($category->category_id == $category_id?" selected=\"true\"":"").">{$category->category_name}</option>";
        }

        $result .= "</select>";

        $result .= '<br/>
		<label>Show only published Items</label>
        <input type="checkbox" value="1" onchange="document.adminForm.submit();" name="com_content_filter_show_published" '.($com_content_filter_show_published!="-1"?'checked="yes" ':'').'/>
        <label>Show only Items with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_filter_show_empty_keywords" '.($com_content_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>
        <label>Show only Items with empty descriptions</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_content_filter_show_empty_descriptions" '.($com_content_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';

        return $result;
    }

    public function getTypeId(){
      return $this->itemType;
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
      parse_str($query, $params);
      if (isset($params["id"]) && $params["id"]){
        $this->setMetadata($params["id"], $data);
      }
    }

    public function isAvailable(){
      require_once dirname(__FILE__)."/MetatagsContainerFactory.php";
      return MetatagsContainerFactory::componentExists("com_hikashop");
    }

    public function mustReplaceMetaTitle(){
      return false;
    }
    public function mustReplaceMeteaKeywords(){
      return false;
    }
    public function mustReplaceMetaDescription(){
      return false;
    }
}
