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

class RedirectFactory{
	public static function Redirect($data){
		require_once JPATH_ADMINISTRATOR."/components/com_osmeta/lib/Snoopy.class.php";
		$db = JFactory::getDBO();
		$obj = new SnoopyOSMeta();
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
			$query="SELECT * FROM #__osmeta_redirects WHERE 1";
			$db->setQuery($query);
			$r_links = $db->loadObjectList();
			foreach($r_links as $r_link){
				for($i=0; $i<count($extLink); $i++){
					$target = ($r_link->target == '1')?"_blank":"_self";
					if (substr($r_link->url, -1) == '*' && stripos($extLink[$i],substr($r_link->url, 0, -1))){
						$extLink[$i] = str_replace("|", "", $extLink[$i]);

						$data = str_replace($extLink[$i].'"', '/index.php?option=com_osmeta&amp;url='.$extLink[$i].'" target="'.$target.'"', $data);
					}else if (stripos($extLink[$i],$r_link->url."|")){
						$extLink[$i] = str_replace("|", "", $extLink[$i]);
						$data = str_replace($extLink[$i].'"', '/index.php?option=com_osmeta&amp;url='.$extLink[$i].'" target="'.$target.'"', $data);
					}
				}
			}

		}



		return $data;
	}

}

?>
