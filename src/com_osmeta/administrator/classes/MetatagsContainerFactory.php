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

/**
 * Metatags Container Factory Class
 *
 * @since  1.0.0
 */
class MetatagsContainerFactory
{
	/**
	 * Features cache
	 *
	 * @var     array
	 * @access  private
	 * @since   1.0.0
	 */
	private static $features = null;

	/**
	 * Metadata By Query Map
	 *
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public static $metadataByQueryMap = array();

	/**
	 * Method to get container by ID
	 *
	 * @param   string  $type  Container Type
	 *
	 * @access	public
	 *
	 * @return  mixed
	 */
	public static function getContainerById($type)
	{
		$features = self::getFeatures();
		$container = 'com_content:Article';

		if (isset($features[$type]))
		{
			require_once $features[$type]["file"];

			eval('$container = new ' . $features[$type]["class"] . '();');
		}

		return $container;
	}

	/**
	 * Method to get container by Request
	 *
	 * @param   string  $queryString  Query String
	 *
	 * @access	public
	 *
	 * @return  mixed
	 */
	public static function getContainerByRequest($queryString = null)
	{
		$params = array();
		$resultFeatureId = null;
		$resultFeaturePriority = -1;

		if ($queryString != null)
		{
			parse_str($queryString, $params);
		}

		$features = self::getFeatures();

		foreach ($features as $featureId => $feature)
		{
			$success = true;

			if (isset($feature["params"]))
			{
				foreach ($feature["params"] as $paramsArray)
				{
					$success = true;

					foreach ($paramsArray as $key => $value)
					{
						if ($queryString != null)
						{
							if ($value !== null)
							{
								$success = $success && (isset($params[$key]) && $params[$key] == $value);
							}
							else
							{
								$success = $success && isset($params[$key]);
							}
						}
						else
						{
							if ($value !== null)
							{
								$success = $success && (JRequest::getCmd($key) == $value);
							}
							else
							{
								$success = $success && (JRequest::getCmd($key, null) !== null);
							}
						}
					}

					if ($success)
					{
						$resultFeatureId = $featureId;
						break;
					}
				}
			}

			$featurePriority = isset($feature['priority']) ? $feature['priority'] : 0;

			if ($success && $featurePriority > $resultFeaturePriority)
			{
				$resultFeatureId = $featureId;
				$resultFeaturePriority = $featurePriority;
			}
		}

		return self::getContainerById($resultFeatureId);
	}

	/**
	 * Method to get metadata from the container
	 *
	 * @param   string  $queryString  Query string
	 *
	 * @access	public
	 *
	 * @return  array
	 */
	public static function getMetadata($queryString)
	{
		$result = array();

		if (isset(self::$metadataByQueryMap[$queryString]))
		{
			$result = self::$metadataByQueryMap[$queryString];
		}
		else
		{
			$container = self::getContainerByRequest($queryString);

			if ($container != null)
			{
				$result = $container->getMetadataByRequest($queryString);
				self::$metadataByQueryMap[$queryString] = $result;
			}
		}

		return $result;
	}

	/**
	 * Method to process the body, injecting the metadata
	 *
	 * @param   string  $body         Body buffer
	 * @param   string  $queryString  Query string
	 *
	 * @access	public
	 *
	 * @return  string
	 */
	public static function processBody($body, $queryString)
	{
		$container = self::getContainerByRequest($queryString);

		if ($container != null)
		{
			$metadata = $container->getMetadataByRequest($queryString);

			// Process meta title tag
			if ($metadata && $metadata["metatitle"])
			{
				$replaced = 0;
				$body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
					'<meta name="title" content="' . htmlspecialchars($metadata["metatitle"]) . '" />', $body, 1, $replaced
				);

				if ($replaced != 1)
				{
					$body = preg_replace('/<head>/i', "<head>\n  <meta name=\"title\" content=\"" . htmlspecialchars($metadata["metatitle"]) . '" />', $body, 1);
				}
			}
			elseif ($metadata)
			{
				$body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i", '', $body, 1, $replaced);
			}

			// Process meta description tag
			if ($metadata && $metadata["metadescription"])
			{
				$replaced = 0;
				$body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+description[\\\"\\\']+[^>]*>/i",
					'<meta name="description" content="' . htmlspecialchars($metadata["metadescription"]) . '" />', $body, 1, $replaced
				);

				if ($replaced != 1)
				{
					$body = preg_replace('/<head>/i', "<head>\n  <meta name=\"description\" content=\"" . htmlspecialchars($metadata["metadescription"]) . '" />', $body, 1);
				}
			}

			// Process meta keywords tag
			if ($metadata && $metadata["metakeywords"])
			{
				$replaced = 0;
				$body = preg_replace("/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+keywords[\\\"\\\']+[^>]*>/i",
					'<meta name="keywords" content="' . htmlspecialchars($metadata["metakeywords"]) . '" />', $body, 1, $replaced
				);

				if ($replaced != 1)
				{
					$body = preg_replace('/<head>/i', "<head>\n  <meta name=\"keywords\" content=\"" . htmlspecialchars($metadata["metakeywords"]) . '" />', $body, 1);
				}
			}

			if ($metadata && $metadata["title_tag"])
			{
				$replaced = 0;
				$body = preg_replace("/<title[^>]*>.*<\\/title>/i",
					'<title>' . htmlspecialchars($metadata["title_tag"]) . '</title>', $body, 1, $replaced
				);
			}
		}

		require_once dirname(__FILE__) . "/Canonical.php";

		$canonical = new OsmetaCanonicalURL;
		$canonical_url = $canonical->getCanonicalURL(substr($_SERVER["REQUEST_URI"], strlen(JURI::base(true)) + 1));

		if ($canonical_url != null)
		{
			switch ($canonical_url->action)
			{
				case OsmetaCanonicalURL::$ACTION_CANONICAL:
					$replaced = 0;
					$location = JURI::base() . $canonical_url->canonical_url;
					$body = preg_replace("/<link[^>]*rel[\\s]*=[\\s]*[\\\"\\\']+canonical[\\\"\\\']+[^>]*>/i",
						'<link rel="canonical" href="' . htmlspecialchars($location) . '" />', $body, 1, $replaced
					);

					if ($replaced != 1)
					{
						$body = preg_replace('/<head>/i', "<head>\n  <link rel=\"canonical\" href=\"" . htmlspecialchars($location) . "\"/>", $body, 1);
					}
					break;

				case OsmetaCanonicalURL::$ACTION_NOINDEX:
					$body = preg_replace('/<head>/i', "<head>\n  <meta name=\"robots\" content=\"noindex\"/>", $body, 1);
					break;

				case OsmetaCanonicalURL::$ACTION_REDIRECT:
					$location = JURI::base() . $canonical_url->canonical_url;
					header('HTTP/1.1 301 Moved Permanently');
					header('Location: ' . $location);
					exit;
					break;
			}
		}

		return $body;
	}

	/**
	 * Method to set the metadata by request
	 *
	 * @param   string  $query     Query
	 * @param   string  $metadata  Metadata
	 *
	 * @access	public
	 *
	 * @return  void
	 */
	public static function setMetadataByRequest($query, $metadata)
	{
		$container = self::getContainerByRequest($query);

		if ($container != null)
		{
			$container->setMetadataByRequest($query, $metadata);
		}
	}

	/**
	 * Method to get all available features
	 *
	 * @access	public
	 *
	 * @return  array
	 */
	public static function getFeatures()
	{
		if (self::$features == null)
		{
			$features  = array();

			$directoryName = dirname(dirname(__FILE__)) . '/features';
			$db = JFactory::getDBO();
			$db->setQuery("SELECT component FROM
					#__osmeta_meta_extensions
					WHERE available=1 AND enabled=1");
			$items = $db->loadObjectList();

			foreach ($items as $item)
			{
				include $directoryName . "/" . $item->component . ".php";
			}

			self::$features = $features;
		}

		return self::$features;
	}
}
