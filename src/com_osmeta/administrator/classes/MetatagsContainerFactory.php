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

class MetatagsContainerFactory{
  public static function getContainerById($type){
    $features = MetatagsContainerFactory::getFeatures();
    $container = null;
    if (isset($features[$type])){
      require_once $features[$type]["file"];
      eval('$container = new '.$features[$type]["class"].'();');
    }
    if ($container==null){
      $container=MetatagsContainerFactory::getCommonContainer();
    }
    return $container;
  }

  public static function getContainerByRequest($queryString=null){
    $params = array();
    $resultFeatureId = null;
    $resultFeaturePriority = -1;

    if ($queryString!=null){
      parse_str($queryString, $params);
    }
    $features = MetatagsContainerFactory::getFeatures();
    foreach($features as $featureId=>$feature){
      $success = true;
      if (isset($feature["params"])){
        foreach($feature["params"] as $paramsArray){
          $success=true;
          foreach ($paramsArray as $key=>$value){
            if ($queryString!=null){
              if ($value!==null){
                $success = $success&&(isset($params[$key])&&$params[$key]==$value);
              }else{
                $success = $success&&isset($params[$key]);
              }
            }else{
              if ($value!==null){
                $success = $success&&(JRequest::getCmd($key)==$value);
              }else{
                $success = $success&&(JRequest::getCmd($key, null)!==null);
              }
            }
          }
          if ($success){
            $resultFeatureId = $featureId;
            break;
          }
        }
      }
      $featurePriority = isset($feature['priority'])?$feature['priority']:0;
      if ($success && $featurePriority > $resultFeaturePriority) {
        $resultFeatureId = $featureId;
        $resultFeaturePriority = $featurePriority;
      }
    }
    return self::getContainerById($resultFeatureId);
  }

  public static $metadataByQueryMap = array();

  public static function getMetadata($queryString){
    $result = array();
    if (isset(self::$metadataByQueryMap[$queryString])){
      $result = self::$metadataByQueryMap[$queryString];
    } else {
      $container = self::getContainerByRequest($queryString);
      if ($container != null){
        $result = $container->getMetadataByRequest($queryString);
        self::$metadataByQueryMap[$queryString] = $result;
      }
    }
    return $result;
  }

  public static function processBody($body, $queryString){
    $container = self::getContainerByRequest($queryString);
    if ($container != null){
      $metadata = $container->getMetadataByRequest($queryString);
      //process meta title tag
      if ($container->mustReplaceMetaTitle() && $metadata && $metadata["metatitle"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
            '<meta name="title" content="'.htmlspecialchars($metadata["metatitle"]).'" />', $body, 1, $replaced);
        if ($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"title\" content=\"".htmlspecialchars($metadata["metatitle"]).'" />', $body, 1);
        }
      }elseif ($metadata){
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",'', $body, 1, $replaced);
      }
      //process meta description tag
      if ($container->mustReplaceMetaDescription() && $metadata && $metadata["metadescription"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+description[\\\"\\\']+[^>]*>/i",
            '<meta name="description" content="'.htmlspecialchars($metadata["metadescription"]).'" />', $body, 1, $replaced);
        if ($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"description\" content=\"".htmlspecialchars($metadata["metadescription"]).'" />', $body, 1);
        }
      }
      //process meta keywords tag
      if ($container->mustReplaceMetaKeywords() && $metadata && $metadata["metakeywords"]){
        $replaced = 0;
        $body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+keywords[\\\"\\\']+[^>]*>/i",
            '<meta name="keywords" content="'.htmlspecialchars($metadata["metakeywords"]).'" />', $body, 1, $replaced);
        if ($replaced != 1){
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"keywords\" content=\"".htmlspecialchars($metadata["metakeywords"]).'" />', $body, 1);
        }
      }
      if ($container->mustReplaceTitle() && $metadata && $metadata["title_tag"]){
        $replaced = 0;
        $body = preg_replace("/<title[^>]*>.*<\\/title>/i",
            '<title>'.htmlspecialchars($metadata["title_tag"]).'</title>', $body, 1, $replaced);
      }



    }
    require_once(dirname(__FILE__)."/Canonical.php");
    $canonical = new OsmetaCanonicalURL();
    $canonical_url = $canonical->getCanonicalURL(substr($_SERVER["REQUEST_URI"], strlen(JURI::base(true))+1));
    if ($canonical_url != null){
      switch($canonical_url->action){
        case OsmetaCanonicalURL::$ACTION_CANONICAL:
          $replaced = 0;
          $location = JURI::base() . $canonical_url->canonical_url;
          $body = preg_replace("/<link[^>]*rel[\\s]*=[\\s]*[\\\"\\\']+canonical[\\\"\\\']+[^>]*>/i",
              '<link rel="canonical" href="'.htmlspecialchars($location).'" />', $body, 1, $replaced);
          if ($replaced != 1){
            $body = preg_replace('/<head>/i', "<head>\n  <link rel=\"canonical\" href=\"".htmlspecialchars($location)."\"/>", $body, 1);
          }
          break;
        case OsmetaCanonicalURL::$ACTION_NOINDEX:
          $body = preg_replace('/<head>/i', "<head>\n  <meta name=\"robots\" content=\"noindex\"/>", $body, 1);
          break;
        case OsmetaCanonicalURL::$ACTION_REDIRECT:
          $location = JURI::base() . $canonical_url->canonical_url;
          header ('HTTP/1.1 301 Moved Permanently');
          header ('Location: '.$location);
          exit;
          break;
      }
    }
    return $body;
  }

  public static function setMetadataByRequest($query, $metadata){
    $container = self::getContainerByRequest($query);
    if ($container != null){
      $container->setMetadataByRequest($query, $metadata);
    }
  }

        public static function getMenuContainer(){
          require_once "MenuItemMetatagsContainer.php";
          return new MenuItemMetatagsContainer();
        }
        public static function getCommonContainer(){
          require_once "CommonMetatagsContainer.php";
          return new CommonMetatagsContainer();
        }

	public static function getFeatures(){
    if (MetatagsContainerFactory::$features == null){
	    $features  = array();

	    $directoryName = dirname(dirname(__FILE__)).'/features';
	    $db = JFactory::getDBO();
	    $db->setQuery("SELECT component FROM
	        #__osmeta_meta_extensions
	        WHERE available=1 AND enabled=1");
	    $items = $db->loadObjectList();

	    foreach($items as $item){
          include $directoryName."/".$item->component.".php";
        }

        MetatagsContainerFactory::$features = $features;
	  }
      return MetatagsContainerFactory::$features ;
	}

	public static function refreshFeatures(){
	  $result = array();
	  $db = JFactory::getDBO();
	  $db->setQuery("SELECT component, available FROM #__osmeta_meta_extensions");
	  $extensions = $db->loadObjectList();
	  foreach($extensions as $extension){
	    $features = array();
	    require(dirname(__FILE__)."/../features/".$extension->component.".php");
	    $available = true;
	    foreach($features as $feature){
	        if (is_file(dirname(__FILE__)."/".$feature["file"])){
	          require_once(dirname(__FILE__)."/".$feature["file"]);
	          $container = new $feature["class"]();
	          $available = $available && $container->isAvailable();
	        }else{
	          $available = false;
	        }
	    }

	    $db->setQuery("UPDATE #__osmeta_meta_extensions SET available=".($available?1:0)."
	        WHERE component=".$db->quote($extension->component));
	    $db->query();
	    $result[$extension->component] = $available;
	  }
	  return $result;
	}

	public static function getJoomlaVersion(){
	    if (MetatagsContainerFactory::$version==null){
	        jimport("joomla.version");
	        $version = new JVersion();
	        MetatagsContainerFactory::$version = $version->RELEASE;
	    }
	    return MetatagsContainerFactory::$version;
	}

	public static function componentExists($name){
	  $db = JFactory::getDBO();
	  $sql = "";
	  if (self::getJoomlaVersion() == "1.5"){
	    $sql = "SELECT 1 FROM #__components where LOWER(name)=".$db->quote(strtolower($name));
	  }else{
	    $sql = "SELECT 1 FROM #__assets where LOWER(name)=".$db->quote(strtolower($name));
	  }
	  $db->setQuery($sql);
	  return $db->loadResult() == "1";
	}

	public static function getAllFeatures(){
	  $db=JFactory::getDBO();
	  $db->setQuery("SELECT name, component, description, enabled FROM
	      #__osmeta_meta_extensions WHERE available=1");
      $features = $db->loadAssocList();
      return $features;
	}

	public static function enableFeature($feature){
	  $db = JFactory::getDBO();
	  $db->setQuery("UPDATE #__osmeta_meta_extensions SET enabled=1 WHERE component=".$db->quote($feature));
	  $db->query();
	}

	public static function disableFeature($feature){
	  $db = JFactory::getDBO();
	  $db->setQuery("UPDATE #__osmeta_meta_extensions SET enabled=0 WHERE component=".$db->quote($feature));
	  $db->query();
	}

	private static $features = null;
	private static $version = null;
}
?>
