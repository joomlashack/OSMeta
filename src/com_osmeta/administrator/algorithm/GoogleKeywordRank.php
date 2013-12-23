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

require_once JPATH_ADMINISTRATOR.DS."components".
        DS."com_osmeta".DS."lib".DS."Snoopy.class.php";

/**
 * Retrieves the position of specified site in the google result list by specified keyword.
 * @param String $keyword
 * @param String $site
 * @param String $google_url cron
 * @param String $lang
 * @return Int position of the specified site in search result list.
 *  value between 1 and 100 if the site is in the first top 100 sites.
 *  1000 if site is not in the first top 100 sites.
 */
function getGoogleKeywordRank($keyword, $site, $google_url = "google.by", $lang = "be"){
	$SnoopyOSMeta = new SnoopyOSMeta;
	if (strpos($site, "https://") === 0){
	  $site = substr($site, strlen("https://"));
	}
	if (strpos($site, "http://") === 0){
		$site = substr($site, strlen("http://"));
	}
    if (strpos($site, "www.") === 0){
        $site = substr($site, strlen("www."));
    }
    $keyword = urlencode($keyword);
	$SnoopyOSMeta->fetch(
	"http://www.$google_url/search?as_q=$keyword&num=100&site=&source=hp"
	);

	preg_match_all("/<h3[\\s]+class=\\\"r\\\">[\\s]*<a[\\s]+href=\\\"([^\\\"]*)\\\"/i", $SnoopyOSMeta->results, $results);
	$rank = 1;
    foreach($results[1] as $result){
    	if (strpos($result, "http://$site") !== false ||
    	  strpos($result, "http://www.$site") !== false ||
    	  strpos($result, "https://$site") !== false ||
    	  strpos($result, "https://www.$site") !== false ||
    	  strpos($result,"http%3A%2F%2Fwww.$site%2F") > 0 ||
    	  strpos($result,"http%3A%2F%2F$site%2F") > 0){
    	  	return $rank;
    	  }
		$rank++;
	}
	return 1000;
}
?>
