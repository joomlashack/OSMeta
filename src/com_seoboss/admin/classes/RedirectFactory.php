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

class RedirectFactory{
	public static function Redirect($data){
		require_once JPATH_ADMINISTRATOR."/components/com_seoboss/lib/Snoopy.class.php";
		$db = JFactory::getDBO();
		$obj = new SnoopySeoBoss();
		$obj->fetchLinksForRedirect($data);
		$url = $_SERVER['HTTP_HOST'];
		$extLink = array();
		//rectal replacement
		if ($obj->results){
			$linksInText = array_flip($obj->results);
			$linksInText = array_flip($linksInText);

			foreach($linksInText as $linkInText){
				if (preg_match("~^[^=]+://~", $linkInText) && !preg_match("~^[^://]+://(www\.)?".$url."~i", $linkInText)) {
					$extLink[]='|'.$linkInText.'|';
				}
			}
			$query="SELECT * FROM #__seoboss_redirects WHERE 1";
			$db->setQuery($query);
			$r_links = $db->loadObjectList();
			foreach($r_links as $r_link){
				for($i=0; $i<count($extLink); $i++){
					$target = ($r_link->target == '1')?"_blank":"_self";
					if (substr($r_link->url, -1) == '*' && stripos($extLink[$i],substr($r_link->url, 0, -1))){
						$extLink[$i] = str_replace("|", "", $extLink[$i]);

						$data = str_replace($extLink[$i].'"', '/index.php?option=com_seoboss&amp;url='.$extLink[$i].'" target="'.$target.'"', $data);
					}else if (stripos($extLink[$i],$r_link->url."|")){
						$extLink[$i] = str_replace("|", "", $extLink[$i]);
						$data = str_replace($extLink[$i].'"', '/index.php?option=com_seoboss&amp;url='.$extLink[$i].'" target="'.$target.'"', $data);
					}
				}
			}

		}



		return $data;
	}

}

?>
