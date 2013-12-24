<?php
/**
 * @category   Joomla Content Plugin
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

$app = JFactory::getApplication();

$app->registerEvent('onAfterContentSave', 'pluginOSMeta_onAfterContentSave');
$app->registerEvent('onContentAfterSave', 'pluginOSMeta_onContentAfterSave');

function pluginOSMeta_onAfterContentSave($content, $isNew)
{
	$app = JFactory::getApplication();
	$input = $app->input;

	$option = $input->getCmd('option');


	if (is_object($content) && isset($content->id) && $content->id && isset($content->metakey) && $content->metakey)
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/classes/MetatagsContainerFactory.php';

		$container = MetatagsContainerFactory::getContainerByComponentName($option);
		$container->saveKeywords($content->metakey, $content->id);

		$articleOSMetadataInput = $app->input->get('osmeta-fields', '', 'array');

		$id = array($content->id);
		$title = array($articleOSMetadataInput['title']);
		$metaDesc = array($content->metadesc);
		$metaKey = array($content->metakey);
		$titleTag = array($articleOSMetadataInput['title_tag']);

		$container->saveMetatags($id, $title, $metaDesc, $metaKey, $titleTag);
	}
}

function pluginOSMeta_onContentAfterSave($context, $content, $isNew)
{
	pluginOSMeta_onAfterContentSave($content, $isNew);
}
