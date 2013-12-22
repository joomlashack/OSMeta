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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.client.helper');

/**
 * Installer Controller
 *
 * @package		SeoBoss
 * @subpackage	Core
 * @since		1.1
 */
if (version_compare(JVERSION, "3.0", "ge")){
  class JBController extends JControllerLegacy{}
}else{
  class JBController extends JController{}
}
class SeobossController extends JBController
{

    function backup_manager(){
        $this->addSubmenu('backup_manager');
        $view	= $this->getView('Backup');
        $view->display();
    }

    function backup_download(){
        header("Content-type: application/octet-stream");
        header("Content-Disposition: attachment; filename=SEOBossBackup.sql");
        header("Content-Transfer-Encoding: binary");
        $model = $this->getModel('Backups');
        $dump = $model->getDump();
        echo $dump;
    }

    function backup_upload(){
        $file = JRequest::getVar("backup", null, "FILES");//from request
        $model = $this->getModel('Backups');
        if ($file!=null && isset($file["tmp_name"])){
            $dump = file_get_contents($file["tmp_name"]);
            $count = $model->applyDump($dump);
            $this->setRedirect("index.php?option=com_seoboss&task=backup_manager" , "$count rows imported successfully.");
        }
    }

    function panel(){
        $this->addSubmenu('panel');
        $view	= $this->getView('Panel');
        $model	= $this->getModel('Panel');
        $configuration = $this->getModel('Options');
        //Get system settings
        $systemInfo = $model->getSystemInfo();
        $code = $configuration->getRegistrationCode();
        $pingStatus = $configuration->getPingStatus();
        $view->assignRef("system", $systemInfo);
        $view->assignRef("code", $code);
        $view->assignRef("pingStatus", $pingStatus);
        $view->display();
    }

    function options_view_code(){
        $view	= $this->getView('Options');
        $model	= $this->getModel('Options');
        $code = $model->getRegistrationCode();
        $view->assignRef("code", $code);
        $view->display("code");
    }

    function options_update_code(){
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');

        $code = JRequest::getVar("code");
        $model->setRegistrationCode($code);
        $this->setRedirect("index.php?option=com_seoboss&task=settings" , "Registration Code was saved.");
    }

    function options_add_tag(){
        $this->addSubmenu('options');
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');

        $tagIds = JRequest::getVar("tag_ids", array());
        $tagNames = JRequest::getVar("tag_names", array());
        $tagValues = JRequest::getVar("tag_values", array());

        $tagName = JRequest::getVar("tag_name", "");
        $tagValue = JRequest::getVar("tag_value", "");

        $maxId=1;
        foreach($tagsIds as $id){
            if ($id > $maxId){
                $maxId = $id+1;
            }
        }
        $tagIds[] = $maxId;
        $tagNames[] = $tagName;
        $tagValues[] = $tagValue;
        $t = true;
        $view->assignRef('tag_ids', $tagIds);
        $view->assignRef('tag_names', $tagNames);
        $view->assignRef('tag_values', $tagValues);
        $view->assignRef('tags_updated', $t);
        $view->display("tags_inner_table");
    }

    function options_delete_tag(){
        $this->addSubmenu('options');
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');

        $tagIds = JRequest::getVar("tag_ids", array());
        $tagNames = JRequest::getVar("tag_names", array());
        $tagValues = JRequest::getVar("tag_values", array());

        $tagId = JRequest::getVar("tag_id", "");

        foreach($tagIds as $key=>$value){
            if ($value == $tagId){
                unset($tagIds[$key]);
                unset($tagNames[$key]);
                unset($tagValues[$key]);
                break;
            }
        }

        $view->assignRef('tag_ids', $tagIds);
        $view->assignRef('tag_names', $tagNames);
        $view->assignRef('tag_values', $tagValues);
        $t = true;
        $view->assignRef('tags_updated', $t);
        $view->display("tags_inner_table");
    }

    function options_update_tag(){
        $this->addSubmenu('options');
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');

        $tagIds = JRequest::getVar("tag_ids", array());
        $tagNames = JRequest::getVar("tag_names", array());
        $tagValues = JRequest::getVar("tag_values", array());

        $tagName = JRequest::getVar("tag_name", "");
        $tagValue = JRequest::getVar("tag_value", "");
        $tagId = JRequest::getVar("tag_id", "");

        foreach($tagIds as $key=>$value){
            if ($value == $tagId){
                $tagNames[$key] = $tagName;
                $tagValues[$key] = $tagValue;
                break;
            }
        }

        $view->assignRef('tag_ids', $tagIds);
        $view->assignRef('tag_names', $tagNames);
        $view->assignRef('tag_values', $tagValues);
        $t = true;
        $view->assignRef('tags_updated', $t);
        $view->display("tags_inner_table");
    }

    function options_save(){
        $this->addSubmenu('options');
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');
        //update_tags
        $tagsUpdated = JRequest::getVar('tags_updated', false);
        if ($tagsUpdated){
            $tagIds = JRequest::getVar("tag_ids", array());
            $tagNames = JRequest::getVar("tag_names", array());
            $tagValues = JRequest::getVar("tag_values", array());
            $model->deleteAllDefaultTags();
            foreach($tagIds as $key=>$value){
                $model->addDefaultTag($tagNames[$key], $tagValues[$key]);
            }
        }
    }

    function settings(){
        $this->addSubmenu('options');
        $model	= $this->getModel('Options');
        $view	= $this->getView('Options');
        $settings = $model->getOptions();

        $view->assignRef('settings', $settings);

        require_once("admin.seoboss.inc.php");
        $view->assignRef('allowed_hilight_tags', $_allowed_hilight_tags);

        require_once(dirname(__FILE__)."/classes/MetatagsContainerFactory.php");
        $features = MetatagsContainerFactory::getAllFeatures();
        $view->assignRef('features',$features);

        $view->display();
    }
    function settings_save(){
        $model = $this->getModel('Options');

        $domain = JRequest::getVar("domain");
        $google_server = JRequest::getVar("google_server");
        $hilight_keywords = JRequest::getVar("hilight_keywords", -1);
        $enable_google_ping = JRequest::getVar("enable_google_ping", -1);
        $hilight_tag = JRequest::getVar("hilight_tag");
        $hilight_class = JRequest::getVar("hilight_class");
        $hilight_skip = JRequest::getVar("hilight_skip");

        $frontpage_meta = JRequest::getVar("frontpage_meta");
        $frontpage_title = JRequest::getVar("frontpage_title");
        $frontpage_meta_title = JRequest::getVar("frontpage_meta_title");
        $frontpage_keywords = JRequest::getVar("frontpage_keywords");
        $frontpage_description = JRequest::getVar("frontpage_description");

        $sa_enable = JRequest::getVar("sa_enable", -1);
        $sa_users = JRequest::getVar("sa_users");
        $max_description_length = JRequest::getVar("max_description_length");
        $model->saveOptions(
          array('domain'=>$domain,
    	   'google_server' => $google_server,
           'hilight_keywords' => $hilight_keywords!=-1?1:0 ,
           'hilight_tag' => $hilight_tag,
           'hilight_class' => $hilight_class,
           'enable_google_ping' => $enable_google_ping!=-1?1:0 ,
           'hilight_skip' => $hilight_skip,
           'frontpage_meta' => $frontpage_meta,
           'frontpage_title' => $frontpage_title,
           'frontpage_meta_title' =>$frontpage_meta_title,
           'frontpage_keywords' => $frontpage_keywords,
           'frontpage_description' => $frontpage_description,
           'sa_enable'=>$sa_enable != -1?1:0 ,
           'sa_users'=>$sa_users,
           'max_description_length'=>$max_description_length)
       );



        $this->setRedirect("index.php?option=com_seoboss&task=settings", "Settings were saved successfully.");
    }

    function settings_edit_tag(){
        $this->addSubmenu('options');
        $view	= $this->getView('Options');
        $model = $this->getModel('Options');

        $tag_id=JRequest::getVar('tag_id', "");
        if ($tag_id){
            $tag = $model->getDefaultTag($tag_id);
            if ($tag){
                $view->assignRef("tag_id", $tag->id);
                $view->assignRef("tag_name", $tag->name);
                $view->assignRef("tag_value", $tag->value);
            }
        }
        $document = $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_seoboss/js/Autocompleter.css');

        $view->display("tags_form");
    }

    function settings_save_tag(){
        $this->addSubmenu('options');
        $view	= $this->getView('Options');
        $model = $this->getModel('Options');
        $tag_id=JRequest::getVar('tag_id', "");
        $tag_name=JRequest::getVar('tag_name', "");
        $tag_value=JRequest::getVar('tag_value', "");
        if ($tag_id){
            $model->updateDefaultTag($tag_id, $tag_name, $tag_value);
        }else{
            $model->addDefaultTag($tag_name, $tag_value);
        }
        $this->setRedirect("index.php?option=com_seoboss&task=settings_default_tags",
          "Meta Tag was saved successfully.");
    }
    function settings_delete_tag(){
        $this->addSubmenu('settings');
        $view	= $this->getView('Options');
        $model = $this->getModel('Options');
        $tag_id=JRequest::getVar('tag_id', "");
        if ($tag_id){
            $model->deleteDefaultTag($tag_id);
        }
        $this->setRedirect("index.php?option=com_seoboss&task=settings_default_tags",
          "Meta Tag was deleted successfully.");
    }

    function settings_default_tags(){
        $this->addSubmenu('settings_default_tags');
        $view	= $this->getView('Options');
        $model = $this->getModel('Options');

        $metatags = $model->getDefaultTags();
        $view->assignRef('metatags', $metatags);
        $view->display("tags_table");
    }

    function url_list(){
        $this->addSubmenu('url_list');
        $view	= $this->getView('ExternalUrls');
        $model = $this->getModel('ExternalUrls');
        $urls = $model->getUrls();
        if ($urls){
            $view->assignRef('urls', $urls);
            $view->display();
        }else{
            $view->display("empty");
        }
    }

    public function apply_redirect(){
        $this->_saveRedirects();
        $this->setRedirect('index.php?option=com_seoboss&task=url_list',
            'Redirect List Saved');
    }

    public function save_redirect(){
        $this->_saveRedirects();
        $this->setRedirect('index.php?option=com_seoboss&task=panel',
            'Redirect List Saved');
    }

    private function _saveRedirects(){
        $array = JRequest::get();
        $model = $this->getModel('ExternalUrls');
        $model->saveUrls($array);
    }
    #keywords manager
    function add_keyword(){
        $this->edit_keyword();
    }

    function edit_keyword()
    {
        $view	= $this->getView('Keywords');
        $row = JTable::getInstance('keyword', 'Table');
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        $id = $cid[0];
        $row->load($id);
        $view->assignRef('row', $row);
        $view->display("edit");
    }

    function apply_keyword(){
        $id = $this->_saveKeyword();
        $this->setRedirect('index.php?option=com_seoboss'.
    '&task=edit_keyword&cid[]='. $id, 'Keyword saved');
    }
    function save_keyword() {
        $id = $this->_saveKeyword();
        $this->setRedirect('index.php?option=com_seoboss'.
            '&task=show_keywords', 'Keyword saved');
    }
    private function _saveKeyword(){
        $row = JTable::getInstance('keyword', 'Table');
        if (!$row->bind(JRequest::get('post'))) {
            echo "<script> alert('".$row->getError()."');
                    window.history.go(-1); </script>\n";
            exit();
        }
        if (!$row->store()) {
            echo "<script> alert('".$row->getError()."');
                  window.history.go(-1); </script>\n";
            exit();
        }
        return $row->id;
    }
    function show_keywords() {
        $view	= $this->getView('Keywords');
        $db = JFactory::getDBO();
        $query = "SELECT * FROM #__seoboss_keywords";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        $view->assignRef('rows', $rows);
        $view->display('list');
    }
    function remove_keyword()
    {
        $cid = JRequest::getVar('cid', array(), '', 'array');
        $db = JFactory::getDBO();
        if (count($cid))
        {
            $cids = implode(',', $cid);
            $query = "DELETE FROM #__seoboss_keywords WHERE id IN ($cids)";
            $db->setQuery($query);
            if (!$db->query())
            {
                echo "<script> alert('".$db->getErrorMsg()."');
    window.history.go(-1); </script>\n";
            }
        }
        $this->setRedirect('index.php?option=com_seoboss&task=show_keywords');
    }


    function keywords_update_stat(){
        $db = JFactory::getDBO();
        require_once("classes/Keywords.php");
        $sql = "SELECT domain, google_server FROM #__seoboss_settings";
        $db->setQuery($sql);
        $cfg = $db->loadObject();

        $site = $cfg->domain;

        $google_url = $cfg->google_server;
        $lang= "en";
        $ids = JRequest::getVar('cid', array() , '', 'array');
        $keywords = new Keywords;
        $keywords->updateKeywordsByIds($site, $google_url, $lang, $ids);
        $this->setRedirect('index.php?option=com_seoboss&task=keywords_view');
    }
    function keywords_view(){
        $db = JFactory::getDBO();
        $sql = "SELECT domain, google_server FROM #__seoboss_settings";
        $db->setQuery($sql);
        $cfg = $db->loadObject();

        $google_url = $cfg->google_server;
        $view	= $this->getView('Keywords');
        $this->addSubmenu('keywords_view');
        $mainframe = JFactory::getApplication();

        $limit      = JRequest::getVar('limit',
        $mainframe->getCfg('list_limit'));
        $limitstart = JRequest::getVar('limitstart', 0);

        $sql = "SELECT SQL_CALC_FOUND_ROWS c.id, c.name, c.google_rank,
             c.google_rank_change,c.google_rank_change_date, c.sticky
             FROM
            #__seoboss_keywords c
            ";

        //Sorting
        $order = JRequest::getCmd("filter_order", "name");
        $order_dir = JRequest::getCmd("filter_order_Dir", "ASC");
        switch($order){
            case "google_rank":
                $sql .= " ORDER BY google_rank ";
                break;
            case "google_rank_change":
                $sql .= " ORDER BY google_rank_change ";
                break;
            case "google_rank_change_date":
                $sql .= " ORDER BY google_rank_change_date ";
                break;
            case "sticky":
                $sql .= " ORDER BY sticky ";
                break;
            default:
                $sql .= " ORDER BY name ";
            break;

        }
        if ($order_dir == "asc"){
            $sql .= " ASC";
        }else{
            $sql .= " DESC";
        }

        $db->setQuery($sql, $limitstart, $limit);
        $rows = $db->loadObjectList();
        if ($db->getErrorNum()) {
            echo $db->stderr();
            return false;
        }
        $db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($db->loadResult(), $limitstart, $limit);
        $view->assignRef('rows', $rows);
        $view->assignRef('order', $order);
        $view->assignRef('order_Dir', $order_dir);
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('google_url', $google_url);
        $view->display();
    }
    #Meta Tags manager
    public function metatags_view(){
        $this->metatags_manager('metatags_view');
    }
    public function metatags_save(){
        $this->metatags_manager('metatags_save');
    }
    public function metatags_copy_keywords_to_title(){
        $this->metatags_manager('metatags_copy_keywords_to_title');
    }
    public function metatags_copy_title_to_keywords(){
        $this->metatags_manager('metatags_copy_title_to_keywords');
    }
    public function metatags_copy_item_title_to_keywords(){
        $this->metatags_manager('metatags_copy_item_title_to_keywords');
    }
    public function metatags_copy_item_title_to_title(){
        $this->metatags_manager('metatags_copy_item_title_to_title');
    }
    public function metatags_generare_descriptions(){
        $this->metatags_manager('metatags_generare_descriptions');
    }
    public function metatags_clear_browser_titles(){
      $this->metatags_manager('metatags_clear_browser_titles');
    }
    private function metatags_manager($task){
        $mainframe = JFactory::getApplication();
        $this->addSubmenu('metatags_view');
        require_once("classes/MetatagsContainerFactory.php");

        $itemType = JRequest::getVar('type', null, '', 'string');
        if (!$itemType){
          $itemType = key(MetatagsContainerFactory::getFeatures());
        }
        $metatagsContainer = MetatagsContainerFactory::getContainerById($itemType);
        if (!is_object($metatagsContainer)){
            //TODO: throw error here.
        }
        switch($task){
            case "metatags_save":
                $ids = JRequest::getVar('ids', array(), '', 'array');
                $metatitles = JRequest::getVar('metatitle', array(), '', 'array');
                $metadescriptions = JRequest::getVar('metadesc', array(), '', 'array');
                $metakeys = JRequest::getVar('metakey', array(), '', 'array');
                $title_tags = JRequest::getVar('title_tag', array(), '', 'array');
                $metatagsContainer->saveMetatags($ids, $metatitles, $metadescriptions, $metakeys, $title_tags);
                break;
            case "metatags_copy_keywords_to_title":
                $metatagsContainer->copyKeywordsToTitle(JRequest::getVar('cid', array(), '', 'array'));
                break;
            case "metatags_copy_title_to_keywords":
                $metatagsContainer->copyTitleToKeywords(JRequest::getVar('cid', array(), '', 'array'));
                break;
            case "metatags_copy_item_title_to_keywords":
                $metatagsContainer->copyItemTitleToKeywords(JRequest::getVar('cid', array(), '', 'array'));
                break;
            case "metatags_copy_item_title_to_title":
                $metatagsContainer->copyItemTitleToTitle(JRequest::getVar('cid', array(), '', 'array'));
                break;
            case "metatags_generare_descriptions":
                $metatagsContainer->GenerateDescriptions(JRequest::getVar('cid', array(), '', 'array'));
                break;
            case "metatags_clear_browser_titles":
                $metatagsContainer->clearBrowserTitles(JRequest::getVar('cid', array(), '', 'array'));
                break;
        }
        $limit      = JRequest::getVar('limit',
        $mainframe->getCfg('list_limit'));
        $limitstart = JRequest::getVar('limitstart', 0);

        $db = JFactory::getDBO();
        $tags = $metatagsContainer->getMetatags($limitstart, $limit);
        $db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
        jimport('joomla.html.pagination');
        $pageNav = new JPagination($db->loadResult(), $limitstart, $limit);
        $view	= $this->getView('Metatags');
        $view->assignRef('itemType', $itemType);
        $view->assignRef('metatagsData', $tags);
        $view->assignRef('page', $page);
        $view->assignRef('itemsOnPage', $itemsOnPage);
        $view->assignRef('filter', $metatagsContainer->getFilter());
        $view->assignRef('availableTypes', MetatagsContainerFactory::getFeatures());
        $view->assignRef('pageNav', $pageNav);
        $view->assignRef('order', JRequest::getCmd("filter_order", "title"));
        $view->assignRef('order_Dir', JRequest::getCmd("filter_order_Dir", "ASC"));
        $view->display();
    }
    public function show_density(){
        $view	= $this->getView('Keywords');
        $name = JRequest::getVar('name');
        $view->assignRef('textareaElement', $name);
        $view->display('density_window');
    }

    public function get_density(){
        require_once "algorithm/KeywordsCounter.php";

        $keywords = JRequest::getVar('keywords');
        $text = JRequest::getVar('text');
        $result = array();
        $keywords_arr = explode(",", $keywords);
        foreach($keywords_arr as $keyword){
            $stat = getStat($keyword, $text);
            $result[] = array(
                    "keyword"=>$keyword,
                    "frequency"=>$stat["frequency"],
                "density"=>$stat["density"]
           );
        }
        //HTML_seo::showDensity($result);
        $view	= $this->getView('Keywords');
        $view->assignRef('keywords_data', $result);
        $view->display('density');
    }
    #Pages Manager feature
    public function pages_manager(){
        $this->seoPages('pages_manager');
    }
    public function pages_save_text(){
        $this->seoPages('pages_save_text');
    }
    public function pages_edit_text(){
        $this->seoPages('pages_edit_text');
    }
    private function seoPages($task){
        $mainframe = JFactory::getApplication();
        $this->addSubmenu('pages_manager');

        require_once("classes/MetatagsContainerFactory.php");
        $itemType = JRequest::getVar('type', null, '', 'string');
        if (!$itemType){
          $itemType = key(MetatagsContainerFactory::getFeatures());
        }
        $metatagsContainer = MetatagsContainerFactory::getContainerById($itemType);
        if (!is_object($metatagsContainer)){
            //TODO: throw error here.
        }
        switch($task){
            case "pages_save_text":
                $metatagsContainer->setMetadata(
                $id = JRequest::getVar('id', '', '', 'int'),
                array(
    			     "title"=>JRequest::getVar('title', '', '', 'string'),
         "metatitle"=>JRequest::getVar('metatitle', '', '', 'string'),
         "metakeywords"=>JRequest::getVar('metakeywords', '', '', 'string'),
         "metadescription"=>JRequest::getVar('metadescription', '', '', 'string')
               ));
                echo "<script>window.parent.document.adminForm.submit();</script>";
                break;
            case "pages_edit_text":
                $id = JRequest::getVar('id', '', '', 'int');
                $data = $metatagsContainer->getMetadata($id);
                $data["id"] = $id;
                $view	= $this->getView('Pages');
                $view->assignRef('itemType', $itemType);
                $view->assignRef('data', $data);
                $view->display('edit');
                break;
            default:
                $limit      = JRequest::getVar('limit',
            $mainframe->getCfg('list_limit'));
            $limitstart = JRequest::getVar('limitstart', 0);

            $db = JFactory::getDBO();
            $pages = $metatagsContainer->getPages($limitstart, $limit);
            $db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
            jimport('joomla.html.pagination');
            $pageNav = new JPagination($db->loadResult(), $limitstart, $limit);
            require_once("algorithm/KeywordsCounter.php");
            for($i = 0 ; $i < count($pages); $i++){
                $stat_arr = array();
                for($j = 0 ; $j < count($pages[$i]->metakey) ; $j++){
                    $stat_arr[] = getStat($pages[$i]->metakey[$j], $pages[$i]->content);
                }
                $pages[$i]->stat = $stat_arr;
            }
            $view	= $this->getView('Pages');
            $view->assignRef('itemType', $itemType);
            $view->assignRef('rows', $pages);
            $view->assignRef('page', $page);
            $view->assignRef('itemsOnPage', $itemsOnPage);
            $view->assignRef('filter', $metatagsContainer->getFilter());
            $view->assignRef('availableTypes', MetatagsContainerFactory::getFeatures());
            $view->assignRef('pageNav', $pageNav);
            $view->display();
        }

    }
    function enablefeature(){
      $id = JRequest::getVar("id");
      require_once dirname(__FILE__)."/classes/MetatagsContainerFactory.php";
      MetatagsContainerFactory::enableFeature($id);
      $this->setRedirect('index.php?option=com_seoboss&task=settings');
    }
    function disablefeature(){
      $id = JRequest::getVar("id");
      require_once dirname(__FILE__)."/classes/MetatagsContainerFactory.php";
      MetatagsContainerFactory::disableFeature($id);
      $this->setRedirect('index.php?option=com_seoboss&task=settings');
    }
    #Helpdesk feature
    function helpdesk(){
        $this->addSubmenu('helpdesk');
        $view	= $this->getView('Helpdesk');
        $model = $this->getModel('Helpdesk');
        $options = $this->getModel('Options');
        $code = $options->getRegistrationCode();

        $requests = $model->getRequests($code);
        $view->assignRef('requests', $requests);
        $view->display();
    }
    function helpdesk_view_request(){
        $this->addSubmenu('helpdesk');
        $view	= $this->getView('Helpdesk');
        $model = $this->getModel('Helpdesk');
        $options = $this->getModel('Options');
        $id = JRequest::getCmd('id');
        $code = $options->getRegistrationCode();
        $request = $model->getRequest($code, $id);
        $view->assignRef('request', $request);
        $view->display("request");
    }
    function helpdesk_new_request(){
        $this->addSubmenu('helpdesk');
        $view	= $this->getView('Helpdesk');
        $view->display("new_request");
    }
    function helpdesk_submit_request(){
        $this->addSubmenu('helpdesk');
        $model = $this->getModel('Helpdesk');
        $options = $this->getModel('Options');
        $id = JRequest::getCmd('id', null);
        $subject = JRequest::getVar('subject');
        $body = JRequest::getCmd('body');
        $code = $options->getRegistrationCode();
        $model->submitRequest($code, $subject, $body, $id);
        $this->setRedirect("index.php?option=com_seoboss&task=helpdesk", "You request was submitted to Helpdesk.");
    }

    function duplicated_pages(){
      $this->addSubmenu('duplicated_pages');
      $view	= $this->getView('Duplicated');
      require_once(dirname(__FILE__).'/classes/Canonical.php');
      $canonical=new SeobossCanonicalURL();
      $pages = $canonical->getCanonicalURLs();
      $view->assignRef("duplicated_pages", $pages);
      $view->display();
    }

    function duplicated_edit(){
      $this->addSubmenu('duplicated_pages');
      $view	= $this->getView('Duplicated');
      require_once(dirname(__FILE__).'/classes/Canonical.php');
      $id = JRequest::getVar("id");
      if ($id){
        $canonical=new SeobossCanonicalURL();
        $page = $canonical->getCanonicalURLById($id);
        $view->assignRef("page", $page);
      }
      $view->display("edit_form");
    }

    function duplicated_save(){
      $id = JRequest::getVar("id");
      $url = JRequest::getVar("url");
      $action = JRequest::getVar("action");

      $canonical_url = JRequest::getVar("canonical_url");
      if (substr($url, 0, 1) == "/"){
        $url = substr($url, 1);
      }
      if (substr($canonical_url, 0, 1) == "/"){
        $canonical_url = substr($canonical_url, 1);
      }
      require_once(dirname(__FILE__).'/classes/Canonical.php');
      $canonical=new SeobossCanonicalURL();
      $canonical->setCanonicalURLById($id, $url, $canonical_url, $action);
      $this->setRedirect("index.php?option=com_seoboss&task=duplicated_pages", "Rule was saved");
    }

    function duplicated_delete(){
      $id = JRequest::getVar("id");
      require_once(dirname(__FILE__).'/classes/Canonical.php');
      $canonical=new SeobossCanonicalURL();
      $canonical->deleteCanonicalURLById($id);
      $this->setRedirect("index.php?option=com_seoboss&task=duplicated_pages", "Rule was deleted");
    }

    private function addSubmenu($task)
    {
         JSubMenuHelper::addEntry(
        JText::_('SEO_CONTROL_PANEL'),
    		'index.php?option=com_seoboss&task=panel',
        $task == 'panel'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_KEYWORDS_MANAGER_TITLE'),
    		'index.php?option=com_seoboss&task=keywords_view',
        $task == 'keywords_view'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_META_TAGS_MANAGER_TITLE'),
        'index.php?option=com_seoboss&task=metatags_view',
        $task == 'metatags_view'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_DEFAULT_META_TAGS'),
        'index.php?option=com_seoboss&task=settings_default_tags',
        $task == 'settings_default_tags'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_PAGES_MANAGER'),
        'index.php?option=com_seoboss&task=pages_manager',
        $task == 'pages_manager'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_EXTERNAL_LINK'),
        'index.php?option=com_seoboss&task=url_list',
        $task == 'url_list'
       );

        JSubMenuHelper::addEntry(
            "Duplicated Conent",
            'index.php?option=com_seoboss&task=duplicated_pages',
            $task == 'duplicated_pages'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_BACKUP_MANAGER'),
        'index.php?option=com_seoboss&task=backup_manager',
        $task == 'backup_manager'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_SETTINGS'),
     		'index.php?option=com_seoboss&task=settings',
        $task == 'settings'
       );

        JSubMenuHelper::addEntry(
        JText::_('SEO_HELPDESK'),
        'index.php?option=com_seoboss&task=helpdesk',
        $task == 'helpdesk'
       );
    }
}
