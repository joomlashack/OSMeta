<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

namespace Alledia\OSMeta\Free\Container;

use JRequest;
use JFactory;
use JURI;
use JRoute;
use JFolder;

// No direct access
defined('_JEXEC') or die();

/**
 * Metatags Container Factory Class
 *
 * @since  1.0
 */
abstract class Factory
{
    /**
     * Features cache
     *
     * @var     array
     * @access  private
     * @since   1.0
     */
    private static $features = null;

    /**
     * Metadata By Query Map
     *
     * @var     array
     * @access  public
     * @since   1.0
     */
    public static $metadataByQueryMap = array();

    /**
     * Method to get container by ID
     *
     * @param string $type Container Type
     *
     * @access  public
     *
     * @return mixed
     */
    public static function getContainerById($type)
    {
        $features = self::getFeatures();
        $container = 'com_content:Article';

        if (isset($features[$type])) {
            eval('$container = new ' . $features[$type]["class"] . '();');
        }

        return $container;
    }

    /**
     * Method to get container by Request
     *
     * @param string $queryString Query String
     *
     * @access  public
     *
     * @return mixed
     */
    public static function getContainerByRequest($queryString = null)
    {
        $params = array();
        $resultFeatureId = null;
        $resultFeaturePriority = -1;

        if ($queryString != null) {
            parse_str($queryString, $params);
        }

        $features = self::getFeatures();
        foreach ($features as $featureId => $feature) {
            $success = true;

            if (isset($feature["params"])) {
                foreach ($feature["params"] as $paramsArray) {
                    $success = true;

                    foreach ($paramsArray as $key => $value) {
                        if ($queryString != null) {
                            if ($value !== null) {
                                $success = $success && (isset($params[$key]) && $params[$key] == $value);
                            } else {
                                $success = $success && isset($params[$key]);
                            }
                        } else {
                            if ($value !== null) {
                                $success = $success && (JRequest::getCmd($key) == $value);
                            } else {
                                $success = $success && (JRequest::getCmd($key, null) !== null);
                            }
                        }
                    }

                    if ($success) {
                        $resultFeatureId = $featureId;
                        break;
                    }
                }
            }

            $featurePriority = isset($feature['priority']) ? $feature['priority'] : 0;

            if ($success && $featurePriority > $resultFeaturePriority) {
                $resultFeatureId = $featureId;
                $resultFeaturePriority = $featurePriority;
            }
        }

        return self::getContainerById($resultFeatureId);
    }

    /**
     * Method to get container by component name
     *
     * @param string $component Component Name
     *
     * @access  public
     *
     * @return Object
     */
    public static function getContainerByComponentName($component)
    {
        $container = false;

        $component = ucfirst(str_replace('com_', '', $component));
        $className = "Alledia\OSMeta\Free\Container\Component\\{$component}";

        if (class_exists($className)) {
            $container = new $className;
        }

        return $container;
    }

    /**
     * Method to get metadata from the container
     *
     * @param string $queryString Query string
     *
     * @access  public
     *
     * @return array
     */
    public static function getMetadata($queryString)
    {
        $result = array();

        if (isset(self::$metadataByQueryMap[$queryString])) {
            $result = self::$metadataByQueryMap[$queryString];
        } else {
            $container = self::getContainerByRequest($queryString);

            if ($container != null) {
                $result = $container->getMetadataByRequest($queryString);
                self::$metadataByQueryMap[$queryString] = $result;
            }
        }

        return $result;
    }

    /**
     * Method to process the body, injecting the metadata
     *
     * @param string $body        Body buffer
     * @param string $queryString Query string
     *
     * @access  public
     *
     * @return string
     */
    public static function processBody($body, $queryString)
    {
        $container = self::getContainerByRequest($queryString);

        if ($container != null && is_object($container)) {
            $metadata = $container->getMetadataByRequest($queryString);

            if (static::isFrontPage()) {

                $homeMetadata = AbstractHome::getMetatags();
                if ($homeMetadata->source !== 'default') {

                    $metadata['metatitle'] = @$homeMetadata->metaTitle;
                    $metadata['metadescription'] = @$homeMetadata->metaDesc;
                }
            }

            // Process meta title tag
            if ($metadata && $metadata["metatitle"]) {
                $replaced = 0;

                // Process the window title tag
                $body = preg_replace(
                    "/<title[^>]*>[^<]*<\/title>/i",
                    '<title>' . htmlspecialchars($metadata["metatitle"]) . '</title>',
                    $body,
                    1,
                    $replaced
                );

                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
                    '<meta name="title" content="' . htmlspecialchars($metadata["metatitle"]) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        "<head>\n  <meta name=\"title\" content=\"" . htmlspecialchars($metadata["metatitle"]) . '" />',
                        $body,
                        1
                    );
                }
            } elseif ($metadata) {
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
                    '',
                    $body,
                    1,
                    $replaced
                );
            }

            // Process meta description tag
            if ($metadata && $metadata["metadescription"]) {
                $replaced = 0;
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+description[\\\"\\\']+[^>]*>/i",
                    '<meta name="description" content="' . htmlspecialchars($metadata["metadescription"]) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        "<head>\n  <meta name=\"description\" content=\"" . htmlspecialchars($metadata["metadescription"]) . '" />',
                        $body,
                        1
                    );
                }
            }
        }

        return $body;
    }

    /**
     * Method to set the metadata by request
     *
     * @param string $query    Query
     * @param string $metadata Metadata
     *
     * @access  public
     *
     * @return void
     */
    public static function setMetadataByRequest($query, $metadata)
    {
        $container = self::getContainerByRequest($query);

        if ($container != null) {
            $container->setMetadataByRequest($query, $metadata);
        }
    }

    /**
     * Method to get all available features
     *
     * @access  public
     *
     * @return array
     */
    public static function getFeatures()
    {
        if (empty(self::$features)) {
            $features  = array();

            jimport('joomla.filesystem.folder');

            $path    = JPATH_SITE . '/administrator/components/com_osmeta/features';
            $files = JFolder::files($path, '.php');

            $features = array();

            foreach ($files as $file) {
                include $path . '/' . $file;
            }

            // Check what features are enabled
            foreach ($features as $key => $value) {
                $class = $value['class'];
                if (! $class::isAvailable()) {
                    unset($features[$key]);
                }
            }

            self::$features = $features;
        }

        return self::$features;
    }

    /**
     * Check if the user is on the front page, not only the default menu
     *
     * @access protected
     *
     * @return boolean
     */
    protected static function isFrontPage()
    {
        $app    = JFactory::getApplication();
        $lang   = JFactory::getLanguage();
        $menu   = $app->getMenu();

        $isFrontPage = $menu->getActive() == $menu->getDefault($lang->getTag());

        // The page for featured articles can be treated as front page as well, so let's filter that
        if ($isFrontPage) {
            $defaultMenu     = $menu->getDefault($lang->getTag());
            $defaultMenuLink = $defaultMenu->link;

            $sefEnabled = (bool) JFactory::getConfig()->get('sef');
            $uri        = JRequest::getURI();

            // Compare the current URI with the default menu URI
            if ($sefEnabled) {
                $router = $app::getRouter();
                $defaultMenuLinkURI = JURI::getInstance($defaultMenuLink);
                $router->parse($defaultMenuLinkURI);
                $defaultMenuLinkRouted = JRoute::_($defaultMenuLink);

                if (version_compare(JVERSION, '3.0', '<')) {
                    $defaultMenuLinkRouted = str_replace('?view=featured', '', $defaultMenuLinkRouted);
                }

                $isFrontPage = $defaultMenuLinkRouted === $uri;
            } else {
                $path = JURI::getInstance()->getPath();

                $isFrontPage = (substr_count($uri, $defaultMenuLink) > 0) || ($uri === $path);
            }
        }

        return $isFrontPage;
    }
}
