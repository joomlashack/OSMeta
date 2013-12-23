<?php
/**
 * @category  Joomla Content Plugin
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$app->registerEvent('onAfterContentSave', 'pluginOSMeta_onAfterContentSave');
$app->registerEvent('onContentAfterSave', 'pluginOSMeta_onContentAfterSave');

function pluginOSMeta_onAfterContentSave($article, $isNew)
{
	$file = JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/ArticleMetatagsContainer.php";
	if (is_object($article) && isset($article->id) && $article->id && isset($article->metakey) && $article->metakey && is_file($file))
	{
		require_once($file);
		$ac = new ArticleMetatagsContainer;
		$ac->saveKeywords($article->metakey, $article->id);
	}

	require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/options.php';
	$model = OSModelOptions::getInstance('OSModelOptions');
	$settings = $model->getOptions();

	if ($settings->enable_google_ping)
	{
		$className = get_class($article);

		require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/ExtensionsFactory.php";
		$extensions = ExtensionsFactory::getExtensions();

		if (is_array($extensions) && is_array($extensions['ping']))
		{
			foreach ($extensions['ping'] as $pingHandler)
			{
				if ($pingHandler['class'] == $className)
				{
					require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/" . $pingHandler['file'];

					$url = '';
					$rss = '';

					if (isset($pingHandler['function']) && function_exists($pingHandler['function']))
					{
						eval('$url=' . $pingHandler['function'] . '($article, $isNew);');
					}

					if (isset($pingHandler['rss_function']) && function_exists($pingHandler['rss_function']))
					{
						eval('$rss=' . $pingHandler['rss_function'] . '();');
					}

					if (!empty($url))
					{
						$db->setQuery("SELECT `domain` FROM `#__osmeta_settings`");
						$domainName = $db->loadResult();

						require_once JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/Pinger.php";
						$config = JFactory::getConfig();
						$pinger = new Pinger;
						$result = $pinger->pingGoogle(
							$config->get('config.sitename'),
							"http://$domainName",
							"http://{$domainName}$url",
							"http://{$domainName}$rss"
						);

						$db->setQuery("INSERT INTO #__osmeta_ping_status
								(`date`, `title`, `url`, `response_code`, `response_text`) VALUES (
								NOW(), " . $db->quote($article->title) . ", ".
								$db->quote($url) . ", " .
								$db->quote($result[0]) . "," .
								$db->quote($result[1]) . ")");
						$db->query();
					}

					break;
				}
			}
		}
	}
}

function pluginOSMeta_onContentAfterSave($context, $article, $isNew)
{
	pluginOSMeta_onAfterContentSave($article, $isNew);
}