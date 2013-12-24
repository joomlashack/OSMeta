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
$app->registerEvent('onAfterRoute', 'pluginOSMeta_onAfterInitialise');

function pluginOSMetaRender()
{
	$app = JFactory::getApplication();

	if ($app->getName() !== 'site')
	{
		return true;
	}

	$queryData = $_REQUEST;
	ksort($queryData);
	$url = http_build_query($queryData);

	$buffer = JResponse::getBody();

	// Metatags processing on the front
	require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/MetatagsContainerFactory.php";
	$buffer = MetatagsContainerFactory::processBody($buffer, $url);

	JResponse::setBody($buffer);
}

function pluginOSMeta_onAfterInitialise()
{
	$app = JFactory::getApplication();

	if ($app->getName() !== 'administrator')
	{
		return true;
	}

	/*
	 * Inject the metadata fields for Joomla 3.0
	 *
	 * For Joomla 2.5, look at: plugins/content/osmeta_content/osmeta_content.php,
	 * into the onContentPrepareForm event.
	 */
	// Joomla 3.x Compatibility
	if (version_compare(JVERSION, '3.0', '>='))
	{
		// Override the native JLayoutHelper to inject and manipulate fields on the article form
		JLoader::register('JLayoutHelper', JPATH_ROOT . '/plugins/system/osmeta_renderer/override/layouthelper.php', true);
	}
}
