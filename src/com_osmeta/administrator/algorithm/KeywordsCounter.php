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

/**
 * Gets keywords statistic (frequency, density) against the specified text.
 * @param String $keyword - Keyword phrase.
 * @param String $text - Specified text.
 * @return Array asociative array with following keys:
 *  frequency - keyword frequency in the specified text.
 *  density - keyword density against the specified text.
 */
function getStat($keyword, $text){
	$frequency  = 0;
	$density = 0;
	if ($keyword && $text){
		$text = strtoupper(strip_tags(trim($text)));
		$result = array();
		$keyword = strtoupper(trim($keyword));

		$keywords_arr = explode(" ", $keyword);
		foreach($keywords_arr as $keyword){
			$start = 0;
			$count = 0;
			while(($start = strpos($text, $keyword, $start)) !== false){
				$count++;
				$start++;
			}
			$result[] = $count;
		}
		$frequency = $result[0];
		for($i = 1 ; $i < count($result); $i++){
			if ($result[$i] < $frequency){
				$frequency = $result[$i];
			}
		}

		$density =  (strlen($text) > 0)?$frequency * strlen($keyword) * 100.0 / strlen($text) : 0;
	}
	return array(
	   "frequency"=>$frequency,
	   "density"=>$density
	);
}

?>
