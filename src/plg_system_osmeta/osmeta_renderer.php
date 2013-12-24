<?php
/**
 * @category  Joomla Content Plugin
 * @package   Osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$app->registerEvent('onAfterRender', 'pluginOSMetaRender');

function pluginOSMetaRender()
{
	JLoader::register('JBModel', JPATH_ADMINISTRATOR . "/components/com_osmeta/models/model.php");
	$app = JFactory::getApplication();

	if ($app->getName() != 'site')
	{
	   return true;
	}

	$queryData = $_REQUEST;
	ksort($queryData);
	$url = http_build_query($queryData);

	$buffer = JResponse::getBody();

	$db = JFactory::getDBO();

	//Metatags processing
	require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/MetatagsContainerFactory.php";
	$buffer =  MetatagsContainerFactory::processBody($buffer, $url);

	require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/options.php';
	$model = OSModelOptions::getInstance('OSModelOptions');
	$settings = $model->getOptions();

	if ($settings->sa_enable == "1")
	{
		$user = JFactory::getUser();
		$sa_users = explode(",", $settings->sa_users);
		if (in_array($user->username, $sa_users))
		{
			$metadata = MetatagsContainerFactory::getMetadata($url);
			//insert the OSMeta Boss Metatags Anywhere feature
			$buffer = preg_replace("/<\\/head[^>]*>/i", '<link rel="stylesheet" href="' . JURI::base() . 'components/com_osmeta/css/anywhere.css" type="text/css" />$0', $buffer);
			$buffer = preg_replace("/<body[^>]*>/i", '$0
				<script language="javascript">
					function toggleOSMetaAnywhere()
					{
						if (document.getElementById("osmetaAnywhereForm").style.display==\'none\')
						{
							document.getElementById("osmetaAnywhereForm").style.display = \'block\';
						}
						else
						{
							document.getElementById("osmetaAnywhereForm").style.display = \'none\';
						}
					}
				</script>
				<div id="osmetaAnywhereForm">
					<form method="POST" action="' . JURI::base() . '">
						<ol>
							<li>
								<label for="osmeta_title">Title</label>
								<input type="text" name="osmeta_title" id="osmeta_title" value="' . (isset($metadata['title_tag'])?htmlspecialchars($metadata['title_tag']):'') . '"/>
							</li>
							<li>
								<label for="osmeta_meta_title">Meta Title</label>
								<input type="text" name="osmeta_meta_title" id="osmeta_meta_title" value="' . (isset($metadata['metatitle'])?htmlspecialchars($metadata['metatitle']):'') . '"/>
							</li>
							<li>
								<label for="osmeta_meta_keywords">Meta Keywords</label>
								<input type="text" name="osmeta_meta_keywords" id="osmeta_meta_keywords" value="' . (isset($metadata['metakeywords'])?htmlspecialchars($metadata['metakeywords']):'') . '"/>
							</li>
							<li>
								<label for="osmeta_meta_description">Meta Description</label>
								<input type="text" name="osmeta_meta_description" id="osmeta_meta_description" value="' . (isset($metadata['metadescription'])?htmlspecialchars($metadata['metadescription']):'') . '"/>
							</li>
							<li>
								<input type="submit" value="Save" />
								<input type="submit" value="Cancel" onclick="toggleOSMetaAnywhere();return false;" />
							</li>
						</ol>
						<input type="hidden" name="option" value="com_osmeta"/>
						<input type="hidden" name="task" value="saveMetadata"/>
						<input type="hidden" name="url" value="' . $url . '"/>
					</form>
				</div>
				<a id="osmeta_anywhere_toggle_link" href="#" onclick="toggleOSMetaAnywhere();return false;">OSMeta Anywhere</a>
		', $buffer);
		}
	}

	// Redirect processing
	require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/RedirectFactory.php";
	$redirect =  new RedirectFactory;
	$buffer = $redirect->Redirect($buffer);

	//set default metatags
	$db->setQuery ("SELECT `name`, `value` from  #__osmeta_default_tags");
	$defaultMetaTags = $db->loadObjectList();
	foreach ($defaultMetaTags as $metaTag)
	{
		preg_match("/<meta[\\s]+name[\\s]*=[\\s]*\"" . $metaTag->name . "\"[\\s]+content[\\s]*=[\\s]*\"[^\"]*\"[\\s]*\\/>/i", $buffer, $match);
		if ($match && isset($match[0]))
		{
			$buffer = str_replace($match[0], "<meta name=\"" . $metaTag->name . "\" content=\"" . $metaTag->value . "\"/>", $buffer);
		}
		else
		{
			$buffer = str_replace("<head>", "<head>\n<meta name=\"" . $metaTag->name . "\" content=\"" . $metaTag->value . "\"/>", $buffer);
		}
	}

	if ($settings->hilight_keywords)
	{
		preg_match("/<meta\\sname=\"keywords\"\\scontent=\"([^\"]*)\"/i", $buffer, $match);
		if ($match && isset($match[1]))
		{
			$keywordsString = $match[1];
			$keywords = explode(",", $keywordsString);

			require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/algorithm/DFA.php";

			$dfa = new DFA;
			$omitTags = array('title', 'textarea', 'style', 'script');
			if ($settings->hilight_skip)
			{
				$omitTags = array_merge($omitTags, explode(",", $settings->hilight_skip));
			}

			$encoding=null;
			if (function_exists('mb_detect_encoding'))
			{
				$encoding = mb_detect_encoding($keywordsString);
				if ($encoding!='UTF-8')
				{
					$encoding = null;
				}
			}

			$buffer = $dfa->hilight($buffer, $keywords, $omitTags, $settings->hilight_tag, $settings->hilight_class, $encoding);
		}
	}

	JResponse::setBody($buffer);
}
