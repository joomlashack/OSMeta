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

class CommonMetatagsContainer extends MetatagsContainer{
    private $code = 20;

	public function getMetatags($lim0, $lim, $filter=null){
        return null;
	}

    public function getPages($lim0, $lim, $filter=null){
        return null;
    }

	public function processBody($body){
	   $db = JFactory::getDBO();

	   if (JRequest::getVar('id') > 0){
          $sql = "SELECT title, title_tag from #__osmeta_metadata WHERE item_id=".$db->quote(JRequest::getVar('id'))." AND
	      item_type=1";

	   	  $db->setQuery($sql);
          $metadata = $db->loadObject();
          if ($metadata && $metadata->title){
          	$replaced = 0;
          	$body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
          	'<meta name="title" content="'.htmlspecialchars($metadata->title).'" />', $body, 1, $replaced);
          	if ($replaced != 1){
          		$body = preg_replace('/<head>/i', "<head>\n  <meta name=\"title\" content=\"".htmlspecialchars($metadata->title).'" />', $body, 1);
          	}
          }elseif ($metadata){
              $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",'', $body, 1, $replaced);
          }
          if ($metadata && $metadata->title_tag){
              $replaced = 0;
              $body = preg_replace("/<title[^>]*>.*<\\/title>/i",
                      '<title>'.htmlspecialchars($metadata->title_tag).'</title>', $body, 1, $replaced);
          }
	   }

	   return $body;
	}
	private function setMetaTag($body, $name, $value){
	    $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+{$name}[\\\"\\\']+[^>]*>/i",
	            '<meta name="'.$name.'" content="'.htmlspecialchars($value).'" />', $body, 1, $replaced);
	    if ($replaced != 1){
	        $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"$name\" content=\"".htmlspecialchars($value).'" />', $body, 1);
	    }
	    return $body;
	}
	public function saveMetatags($ids, $metatitles, $metadescriptions, $metakeys, $title_tags=null){
	}
	public function saveKeywords($keys, $id, $itemTypeId=null){
	  parent::saveKeywords($keys, $id, $itemTypeId?$itemTypeId:$this->code);
	}


	public function setMetadataByRequest($url,$data) {
	    $id = $this->getURLId($url);
	    if (!$id){
	      $id = $this->createURL($url);
	    }

		$this->setMetadata($id, $data);
	}

	public function getMetadataByRequest($query){
	  $id = $this->getURLId($query);
	  $result=null;
	  if ($id){
	    return $this->getMetadata($id);
	  }
	  return $result;
	}

	public function getURLId($url){
      if (empty($url)){
        $url = "/";
      }
	  $db = JFactory::getDBO();
	  $db->setQuery("SELECT `id` FROM `#__osmeta_urls` where `url`=".$db->quote($url));
	  return $db->loadResult();
	}

	public function createURL($url){
	  if (empty($url)){
	    $url = "/";
	  }
	  $db = JFactory::getDBO();
	  $db->setQuery("INSERT INTO `#__osmeta_urls` (`url`) VALUES (".$db->quote($url).")");
	  $db->query();
	  return $db->insertid();
	}

	public function getTypeId(){
	  return $this->code;
	}

	public function isAvailable(){
	  return true;
	}
}
?>
