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
          $sql = "SELECT title, title_tag from #__seoboss_metadata WHERE item_id=".$db->quote(JRequest::getVar('id'))." AND
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
	   }else{
	       $db->setQuery("SELECT frontpage_meta,
	               frontpage_title,
	               frontpage_meta_title,
	               frontpage_keywords,
	               frontpage_description
	               FROM #__seoboss_settings LIMIT 0,1");
	       $settings = $db->loadObject();

	       if ($settings->frontpage_meta==0){
	           if ($settings->frontpage_title){
	               $replaced = 0;
	               $body = preg_replace("/<title[^>]*>.*<\\/title>/i",
	                       '<title>'.htmlspecialchars($settings->frontpage_title).'</title>', $body, 1, $replaced);
	           }
	           if ($settings->frontpage_meta_title){
	               $body = $this->setMetatag($body, "title", $settings->frontpage_meta_title);
	           }
	           if ($settings->frontpage_keywords){
	               $body = $this->setMetatag($body, "keywords", $settings->frontpage_keywords);
	           }
	           if ($settings->frontpage_description){
	               $body = $this->setMetatag($body, "description", $settings->frontpage_description);
	           }
	       }
	       elseif ($settings->frontpage_meta==1){
	           jimport('joomla.version');
	           $version = new JVersion();
	           if ($version->RELEASE == "1.5"){
	               $model = JBModel::getInstance("frontpage", "contentModel", array());
	               $featuredItems = $model->getData();
	           }else{
	               $model = JBModel::getInstance("featured", "contentModel", array());
	               $featuredItems = $model->getItems();
	           }
	           $firstItem = $featuredItems[0];
	           if ($firstItem){
	               if ($firstItem->metakey){
	                   $body = $this->setMetatag($body, "keywords", $firstItem->metakey);
	               }
	               if ($firstItem->metadesc){
	                   $body = $this->setMetatag($body, "description", $firstItem->metadesc);
	               }
	           }
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
	  $db->setQuery("SELECT `id` FROM `#__seoboss_urls` where `url`=".$db->quote($url));
	  return $db->loadResult();
	}

	public function createURL($url){
	  if (empty($url)){
	    $url = "/";
	  }
	  $db = JFactory::getDBO();
	  $db->setQuery("INSERT INTO `#__seoboss_urls` (`url`) VALUES (".$db->quote($url).")");
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
