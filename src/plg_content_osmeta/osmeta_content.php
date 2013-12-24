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

function pluginOSMeta_onAfterContentSave($article, $isNew)
{
	$file = JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/ArticleMetatagsContainer.php";

	if (is_object($article) && isset($article->id) && $article->id && isset($article->metakey) && $article->metakey && is_file($file))
	{
		require_once $file;

		$ac = new ArticleMetatagsContainer;
		$ac->saveKeywords($article->metakey, $article->id);
	}

	require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/options.php';
	$model = OSModelOptions::getInstance('OSModelOptions');
	$settings = $model->getOptions();

	$app = JFactory::getApplication();
	$articleOSMetadataInput = $app->input->get('article-osmeta-fields', '', 'array');
	$articleMetadataInput = $app->input->get('jform', '', 'array');

	$articleOSMetadataInput['description'] = $articleMetadataInput['metadesc'];

	if (!empty($articleOSMetadataInput))
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/metadata.php';

		$id = $app->input->get('id', 0, 'int');

		// Store the metadata information
		$model = OSModelMetadata::getInstance('OSModelMetadata');
		$metadata = $model->storeMetadata($id, $articleOSMetadataInput);
	}
}

function pluginOSMeta_onContentAfterSave($context, $article, $isNew)
{
	pluginOSMeta_onAfterContentSave($article, $isNew);
}
