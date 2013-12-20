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

require_once JPATH_ADMINISTRATOR.DS."components".
        DS."com_seoboss".DS."algorithm".DS."GoogleKeywordRank.php";

class Keywords{
	public function updateOldestKeywords($site, $google_url, $lang, $count = 1){
		$db = JFactory::getDBO();
	
		$sql = "SELECT id, name, google_rank, google_rank_change_date 
		FROM #__seoboss_keywords 
		ORDER BY google_rank_change_date ASC LIMIT 0,$count";
        $db->setQuery($sql);
        $keywords = $db->loadObjectList();

         foreach($keywords as $keyword){
         	$this->updateGoogleRank($keyword, $site, $google_url, $lang);
         }
	}
    public function updateKeywordsByIds($site, $google_url, $lang, $ids){
    	for($i = 0 ; $i< count($ids) ; $i++){
    		$ids[$i] = intval($ids[$i]);
    	}
    	if(count($ids)== 0){
    		return;
    	}
        $db = JFactory::getDBO();
    
        $sql = "SELECT id, name, google_rank, google_rank_change_date 
        FROM #__seoboss_keywords 
        WHERE id IN (".implode(",", $ids).")";
        $db->setQuery($sql);
        $keywords = $db->loadObjectList();

         foreach($keywords as $keyword){
            $this->updateGoogleRank($keyword, $site, $google_url, $lang);
         }
    }
	
	private function updateGoogleRank(&$keyword, $site, $google_url, $lang){
		$db = JFactory::getDBO();
	   $rank = getGoogleKeywordRank($keyword->name, $site, $google_url, $lang);
    if($rank == 1000 ){
    	if($keyword->google_rank > 0 && $keyword->google_rank < 1000){
            $change =    "-100";
    	}else{
    		$change=0;
    	}
    }elseif($keyword->google_rank_change_date == '0000-00-00 00:00:00'){
        $change = 0;
    }elseif(( $keyword->google_rank==0 || $keyword->google_rank==1000)&& $rank!= 0){
        $change = "+100";
    }else{
        $change = $keyword->google_rank - $rank;
    }
    
    $sql = "UPDATE #__seoboss_keywords SET google_rank=".$db->quote($rank).", google_rank_change=".$db->quote($change).",
    google_rank_change_date=NOW() WHERE id=".$db->quote($keyword->id);
    
    $db->setQuery($sql);
    $db->query();
	}
}
?>