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
$mainframe = JFactory::getApplication();
$mainframe->registerEvent( 'onAfterRender',
'pluginSeoBossRender' );

function pluginSeoBossRender(){
    JLoader::register('JBModel', JPATH_ADMINISTRATOR."/components/com_seoboss/models/model.php");
	$app = JFactory::getApplication();

    if($app->getName() != 'site') {
       return true;
    }

    $queryData = $_REQUEST;
    ksort($queryData);
    $url = http_build_query($queryData);
    
    $buffer = JResponse::getBody();
    //Metatags processing
    require_once JPATH_ADMINISTRATOR."/components/com_seoboss/classes/MetatagsContainerFactory.php";
    $buffer =  MetatagsContainerFactory::processBody( $buffer, $url );
    $db = JFactory::getDBO();
    $db->setQuery ("SELECT sa_enable, sa_users from  #__seoboss_settings " );
    $settings = $db->loadObject();
    if($settings->sa_enable == "1"){
    $user = JFactory::getUser();
    $sa_users = explode(",", $settings->sa_users);
    if( in_array($user->username, $sa_users)){
    $metadata = MetatagsContainerFactory::getMetadata( $url );
    //insert the SEO Boss Metatags Anywhere feature
    $buffer = preg_replace("/<\\/head[^>]*>/i", '<link rel="stylesheet" href="'.JURI::base().'components/com_seoboss/css/anywhere.css" type="text/css" />$0', $buffer);
    $buffer = preg_replace("/<body[^>]*>/i", '$0
        <script language="javascript">
        function toggleSeobossAnywhere(){
        if( document.getElementById("seobossAnywhereForm").style.display==\'none\'){
          document.getElementById("seobossAnywhereForm").style.display = \'block\';
        }else{
        document.getElementById("seobossAnywhereForm").style.display = \'none\';
        }
        }
        </script>
                <div id="seobossAnywhereForm">
        <form method="POST" action="'.JURI::base().'">
        <ol>
        <li>
          <label for="seoboss_title">Title</label>
          <input type="text" name="seoboss_title" id="seoboss_title" value="'.(isset($metadata['title_tag'])?htmlspecialchars($metadata['title_tag']):'').'"
        />
        </li>
        <li>
          <label for="seoboss_meta_title">Meta Title</label>
          <input type="text" name="seoboss_meta_title" id="seoboss_meta_title" value="'.(isset($metadata['metatitle'])?htmlspecialchars($metadata['metatitle']):'').'"
        />
        </li>
        <li>
          <label for="seoboss_meta_keywords">Meta Keywords</label>
          <input type="text" name="seoboss_meta_keywords" id="seoboss_meta_keywords" value="'.(isset($metadata['metakeywords'])?htmlspecialchars($metadata['metakeywords']):'').'"
        />
        </li>
        <li>
          <label for="seoboss_meta_description">Meta Description</label>
          <input type="text" name="seoboss_meta_description" id="seoboss_meta_description" value="'.(isset($metadata['metadescription'])?htmlspecialchars($metadata['metadescription']):'').'"
        />
        </li>
        <li>
      <input type="submit" value="Save" />
      <input type="submit" value="Cancel" onclick="toggleSeobossAnywhere();return false;" />    
   </li>
</ol>     
        <input type="hidden" name="option" value="com_seoboss"/>
        <input type="hidden" name="task" value="saveMetadata"/>
        <input type="hidden" name="url" value="'.$url.'"/>
        </form>   
        </div>
        <a id="seoboss_anywhere_toggle_link" href="#" onclick="toggleSeobossAnywhere();return false;">SEO Boss Anywhere</a>
', $buffer);
    }
    }
	 //Redirect processing
	require_once JPATH_ADMINISTRATOR."/components/com_seoboss/classes/RedirectFactory.php";
	$redirect =  new RedirectFactory();
	$buffer = $redirect->Redirect($buffer);
	//
	$db = JFactory::getDBO();
	//set default metatags
	$db->setQuery ("SELECT `name`, `value` from  #__seoboss_default_tags" );
	$defaultMetaTags = $db->loadObjectList();
    foreach($defaultMetaTags as $metaTag){
	    preg_match("/<meta[\\s]+name[\\s]*=[\\s]*\"".$metaTag->name."\"[\\s]+content[\\s]*=[\\s]*\"[^\"]*\"[\\s]*\\/>/i", $buffer, $match);
	    if($match && isset($match[0])){
	        $buffer = str_replace($match[0], "<meta name=\"".$metaTag->name."\" content=\"".$metaTag->value."\"/>", $buffer);
	    }else{
	        $buffer = str_replace("<head>", "<head>\n"."<meta name=\"".$metaTag->name."\" content=\"".$metaTag->value."\"/>", $buffer);
	    }
	}
    //Retreive settings
    
    $db->setQuery ("SELECT hilight_keywords, hilight_tag, hilight_class, hilight_skip from  #__seoboss_settings " );
    $settings = $db->loadObject();
    if($settings->hilight_keywords){
	    preg_match("/<meta\\sname=\"keywords\"\\scontent=\"([^\"]*)\"/i", $buffer, $match);
            if($match && isset($match[1])){
                $keywordsString = $match[1];
	    	$keywords = explode(",", $keywordsString);
	    	require_once JPATH_ADMINISTRATOR."/components/com_seoboss/algorithm/DFA.php";
	        $dfa = new DFA();
	        $omitTags = array('title', 'textarea', 'style', 'script');
            if($settings->hilight_skip){
                $omitTags = array_merge($omitTags, explode(",", $settings->hilight_skip));
            }
                $encoding=null;
                if(function_exists('mb_detect_encoding')){
                  $encoding = mb_detect_encoding($keywordsString);
                  if($encoding!='UTF-8'){
                    $encoding = null;
                  }
                }
	        $buffer = $dfa->hilight($buffer, $keywords, $omitTags, $settings->hilight_tag, $settings->hilight_class, $encoding );
	    }
    }
	JResponse::setBody($buffer);
}

