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

class VM2_CategoryMetatagsContainer extends MetatagsContainer{
    public $code=11;
    public function getMetatags($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $language = $this->getLanguage();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        c.virtuemart_category_id as id,
        c.category_name AS title,
        c.metakey AS metakey,
        c.metadesc AS metadesc,
        c.customtitle as metatitle ,
        m.title_tag as title_tag
         FROM
        #__virtuemart_categories_$language c
        LEFT JOIN
		#__osmeta_metadata m ON m.item_id=c.virtuemart_category_id and m.item_type={$this->code}
        WHERE 1 ";

        $search = JRequest::getVar("filter_search", "");

        $com_vm_filter_show_empty_keywords =
            JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        $com_vm_filter_show_empty_descriptions =
            JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.virtuemart_category_id=".$db->quote($search);
            }else{
                $sql .= " AND c.category_name LIKE ".$db->quote('%'.$search.'%');
            }
        }

        if ($com_vm_filter_show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(metakey) OR metakey='') ";
        }
        if ($com_vm_filter_show_empty_descriptions != "-1"){
            $sql .= "AND (ISNULL(metadesc) OR metadesc='') ";
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
            $rows[$i]->edit_url = "index.php?option=com_virtuemart&view=category&task=edit&virtuemart_category_id={$rows[$i]->id}";
        }
        return $rows;
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

    public function copyKeywordsToTitle($ids){
        $language = $this->getLanguage();
        $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        if (count($ids) > 0){
            $sql = "UPDATE #__virtuemart_categories_$language SET customtitle=metakey WHERE virtuemart_category_id IN (".
                               implode(",", $ids). ")";
            $db->setQuery($sql);
            $db->query();
        }
    }

    public function copyTitleToKeywords($ids){
       $language = $this->getLanguage();
       $db = JFactory::getDBO();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        if (count($ids) > 0){
            $sql = "UPDATE #__virtuemart_categories_$language SET metakey=customtitle WHERE virtuemart_category_id IN (".
                               implode(",", $ids). ")";
            $db->setQuery($sql);
            $db->query();

            //save keywords
            $sql = "SELECT virtuemart_category_id as id , metakey FROM #__virtuemart_categories_$language WHERE virtuemart_category_id IN (".
            implode(",", $ids). ")";
            $db->setQuery($sql);
            $items = $db->loadObjectList();
            foreach($items as $item){
                $this->saveKeywords($item->metakey, $item->id);
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
    if (count($ids) > 0){
            $language = $this->getLanguage();
            $sql = "UPDATE #__virtuemart_categories_$language SET customtitle=category_name WHERE virtuemart_category_id IN (".
                               implode(",", $ids). ")";
            $db->setQuery($sql);
            $db->query();
        }
    }

    public function copyItemTitleToKeywords($ids){
        $db = JFactory::getDBO();
        $language = $this->getLanguage();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }
        if (count($ids) > 0){
            $sql = "UPDATE #__virtuemart_categories_$language SET metakey=category_name WHERE virtuemart_category_id IN (".
                               implode(",", $ids). ")";
            $db->setQuery($sql);
            $db->query();

            //save keywords
            $sql = "SELECT virtuemart_category_id as id , metakey FROM #__virtuemart_categories_$language WHERE virtuemart_category_id IN (".
                               implode(",", $ids). ")";
            $db->setQuery($sql);
            $items = $db->loadObjectList();
            foreach($items as $item){
                $this->saveKeywords($item->metakey, $item->id);
            }
        }
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
        $language = $this->getLanguage();
        foreach($ids as $key=>$value){
            if (!is_numeric($value)){
                unset($ids[$key]);
            }
        }

        $sql = "SELECT virtuemart_category_id, category_description
                FROM  #__virtuemart_categories_$language
                WHERE virtuemart_category_id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $items = $db->loadObjectList();

        foreach($items as $item){
          if ($item->product_s_desc != ''){
            $introtext = strip_tags($item->product_s_desc);
            if (strlen($introtext) > $max_description_length){
              $introtext = substr($introtext, 0, $max_description_length);
            }
            $sql = "INSERT INTO #__osmeta_metadata (item_id,
            item_type, title, description)
            VALUES (
            ".$db->quote($item->virtuemart_product_id).",
            {$this->getTypeId()},

            '',
            ".$db->quote($introtext)."
           ) ON DUPLICATE KEY UPDATE description=".$db->quote($introtext);

            $db->setQuery($sql);
            $db->query();

            $sql = "UPDATE #__virtuemart_categories_$language
                    SET metadesc=".$db->quote($introtext)."
                    WHERE virtuemart_category_id=".$db->quote($item->virtuemart_product_id);

            $db->setQuery($sql);
            $db->query();
          }
        }
    }

    public function getPages($lim0, $lim, $filter=null){
        $db = JFactory::getDBO();
        $language = $this->getLanguage();
        $sql = "SELECT SQL_CALC_FOUND_ROWS
        	c.virtuemart_category_id AS id,
        	c.category_name AS title,
        	c.metakey AS metakey,
        	c.category_description AS content
         FROM
        #__virtuemart_categories_$language c WHERE 1
        ";

        $search = JRequest::getVar("filter_search", "");
        $com_vm_filter_show_empty_keywords = JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        $com_vm_filter_show_empty_descriptions = JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

        if ($search != ""){
            if (is_numeric($search)){
                $sql .= " AND c.virtuemart_category_id=".$db->quote($search);
            }else{
                $sql .= " AND c.category_name LIKE ".$db->quote('%'.$search.'%');
            }
        }

        if ($com_vm_filter_show_empty_keywords != "-1"){
            $sql .= " AND (ISNULL(metakey) OR metakey='') ";
        }
        if ($com_vm_filter_show_empty_descriptions != "-1"){
            $sql .= "AND (ISNULL(metadesc) OR metadesc='') ";
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
                $rows[$i]->edit_url = "index.php?option=com_virtuemart&view=category&task=edit&virtuemart_category_id={$rows[$i]->id}";
        }
        return $rows;
    }
    public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys, $title_tags=null){
        $db = JFactory::getDBO();
        $language = $this->getLanguage();
        for($i = 0 ;$i < count($ids); $i++){
            $sql = "UPDATE #__virtuemart_categories_$language
             SET customtitle=".$db->quote($metatitles[$i]).",
             metadesc=".$db->quote($metadescriptions[$i]).",
             metakey=".$db->quote($metakeys[$i])."
             WHERE virtuemart_category_id=".$db->quote($ids[$i]);
            $db->setQuery($sql);
            $db->query();
            $this->saveKeywords($metakeys[$i], $ids[$i]);

            $sql = "INSERT INTO #__osmeta_metadata (item_id,
            item_type, title, description, title_tag)
            VALUES (
            ".$db->quote($ids[$i]).",
            {$this->code},
            ".$db->quote($metatitles[$i]).",
            ".$db->quote($metadescriptions[$i]).",
            ".$db->quote($title_tags!=null?$title_tags[$i]:'')."
           ) ON DUPLICATE KEY UPDATE title=".$db->quote($metatitles[$i])." , description=".$db->quote($metadescriptions[$i]).
            ", title_tag=".$db->quote($title_tags!=null?$title_tags[$i]:'');

            $db->setQuery($sql);
            $db->query();
        }

    }
    public function saveKeywords($keys, $id){
        parent::saveKeywords($keys, $id,$this->code);
    }

    public function getItemData($id){
        $db = JFactory::getDBO();
        $language = $this->getLanguage();
        $sql = "SELECT c.virtuemart_category_id as id,
        c.category_name as title,
        c.metakey AS metakeywords,
        c.metadesc as metadescription,
        c.customtitle as metatitle
         FROM
        #__virtuemart_categories_$language c
        WHERE c.virtuemart_category_id=".$db->quote($id);
        $db->setQuery($sql);
        $data = $db->loadAssoc();
        $parentData = parent::getItemData($id);
        $data["title_tag"] = $parentData["title_tag"];
        return $data;
    }


    public function setMetadata($id, $data){
        $keywords = $data["metakeywords"];
        $title = isset($data["title"])?$data["title"]:"";
        $metatitle = $data["metatitle"];
        $metadescription = $data["metadescription"];
        $language = $this->getLanguage();
        $db = JFactory::getDBO();
        //Save metatitles and metadata
        $sql = "UPDATE #__virtuemart_categories_$language
        		SET ".
        		($title?"category_name= ".$db->quote($title).",":"")."
        		customtitle=".$db->quote($metatitle).",
        		metadesc=".$db->quote($metadescription).",
        		metakey=".$db->quote($keywords)."
        		WHERE virtuemart_category_id=".$db->quote($id);
        $db->setQuery($sql);
        $db->query();
        parent::setMetadata($id, $data);
    }

    public function getMetadataByRequest($query){
      $params = array();
      parse_str($query, $params);
      $metadata = null;
      if (isset($params["virtuemart_category_id"])){
        $metadata = $this->getMetadata($params["virtuemart_category_id"]);
      }
      return $metadata;
    }

    public function setMetadataByRequest($query, $data){
      $params = array();
      parse_str($query, $params);
      if (isset($params["virtuemart_category_id"]) && $params["virtuemart_category_id"]){
        $this->setMetadata($params["virtuemart_category_id"], $data);
      }
    }

    function getFilter(){
        $language = $this->getLanguage();
        $search = JRequest::getVar("filter_search", "");
        $category_id = JRequest::getVar("filter_category_id", "");

        $com_vm_filter_show_empty_keywords = JRequest::getVar("com_vm_filter_show_empty_keywords", "-1");
        $com_vm_filter_show_empty_descriptions = JRequest::getVar("com_vm_filter_show_empty_descriptions", "-1");

                $result =  'Filter:
        <input type="text" name="filter_search" id="search" value="'.$search.'" class="text_area" onchange="document.adminForm.submit();" title="Filter by Title or enter an Product ID"/>
        <button onclick="this.form.submit();">Go</button>
        <button onclick="document.getElementById(\'search\').value=\'\';this.form.getElementById(\'filter_sectionid\').value=\'-1\';this.form.getElementById(\'catid\').value=\'0\';this.form.getElementById(\'filter_authorid\').value=\'0\';this.form.getElementById(\'filter_state\').value=\'\';this.form.submit();">Reset</button>

        &nbsp;&nbsp;&nbsp;';

        $result .= '<br/>
        <label>Show only Items with empty keywords</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_vm_filter_show_empty_keywords" '.($com_vm_filter_show_empty_keywords!="-1"?'checked="yes" ':'').'/>
        <label>Show only Items with empty descriptions</label>
        <input type="checkbox" onchange="document.adminForm.submit();" name="com_vm_filter_show_empty_descriptions" '.($com_vm_filter_show_empty_descriptions!="-1"?'checked="yes" ':'').'/>                ';
        return $result;
    }

    private function getLanguage(){
        $language="en_gb";
        $vmHelperPath = dirname(__FILE__)."/../../com_virtuemart/helpers/config.php";
        if (is_file($vmHelperPath)){
            require_once($vmHelperPath);
            $config = VmConfig::loadConfig();
            $language = $config->lang;
        }
        return $language;
    }

    public function getTypeId(){
      return $this->code;
    }

    public function isAvailable(){
      return file_exists(dirname(__FILE__)."/../../com_virtuemart/models/virtuemart.php");
    }


}
