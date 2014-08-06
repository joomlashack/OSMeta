<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Metatags Container Factory Class
 *
 * @since  1.0
 */
class OSMetatagsContainerFactory
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
            require_once $features[$type]["file"];

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
        $containerName = false;
        $container = false;

        if ($component === 'com_content') {
            $containerName = 'OSArticleMetatagsContainer';
        } elseif ($component === 'com_categories') {
            $containerName = 'OSArticleCategoryMetatagsContainer';
        }

        if ($containerName) {
            $file = JPATH_ADMINISTRATOR . "/components/com_osmeta/classes/" . $containerName . ".php";
            require_once $file;
            $container = new $containerName;
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
                require_once 'OSHomeMetatagsContainer.php';

                $homeMetadata = OSHomeMetatagsContainer::getMetatags();
                if ($homeMetadata->source !== 'default') {

                    $metadata['metatitle'] = @$homeMetadata->metaTitle;
                    $metadata['metadescription'] = @$homeMetadata->metaDesc;
                    $metadata['metakeywords'] = @$homeMetadata->metaKey;
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

            // Process meta keywords tag
            if ($metadata && $metadata["metakeywords"]) {
                $replaced = 0;
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+keywords[\\\"\\\']+[^>]*>/i",
                    '<meta name="keywords" content="' . htmlspecialchars($metadata["metakeywords"]) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        "<head>\n  <meta name=\"keywords\" content=\"" . htmlspecialchars($metadata["metakeywords"]) . '" />',
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
        if (self::$features == null) {
            $features  = array();

            $directoryName = dirname(dirname(__FILE__)) . '/features';
            $db = JFactory::getDBO();
            $db->setQuery(
                "SELECT component FROM " .
                "#__osmeta_meta_extensions " .
                "WHERE available=1 AND enabled=1"
            );
            $items = $db->loadObjectList();

            foreach ($items as $item) {
                include $directoryName . "/" . $item->component . ".php";
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
        $app = JFactory::getApplication();
        $lang = JFactory::getLanguage();
        $config = JFactory::getConfig();

        $menu = $app->getMenu();
        $defaultMenu = $menu->getDefault($lang->getTag());
        $defaultMenuLink = $defaultMenu->link;
        $sefEnabled = (bool)$config->get('sef');
        $sefRewriteEnabled = (bool)$config->get('sef_rewrite');

        $frontPage = $menu->getActive() == $menu->getDefault($lang->getTag());


        $path = JURI::getInstance()->getPath();
        $uri = JRequest::getURI();

        if ($sefEnabled) {
            $defaultMenuLink = JRoute::_($defaultMenu->link);
            $defaultMenuLink = preg_replace('/(index\.php)?[\/]?component\/content\//', '', $defaultMenuLink);
        }

        if ($frontPage) {
            if ($sefEnabled) {
                if ($sefRewriteEnabled) {
                    if (substr($uri, -1) === '/') {
                        if (substr($defaultMenuLink, -1) !== '/') {
                            $defaultMenuLink .= '/';
                        }
                    }
                }

                if (substr_count($uri, '/index.php')) {
                    if (!substr_count($defaultMenuLink, '/index.php')) {
                        $defaultMenuLink .= '/index.php';
                    }
                } else {
                    if (substr_count($uri, 'index.php')) {
                        if (!substr_count($defaultMenuLink, 'index.php')) {
                            $defaultMenuLink .= 'index.php';
                        }
                    }
                }

                $defaultMenuLink = str_replace('//', '/', $defaultMenuLink);

                $frontPage = ($uri === $path) && ($uri === $defaultMenuLink);
            } else {
                $frontPage = substr_count($uri, $defaultMenuLink) > 0;

                if (!$frontPage) {
                    $frontPage = $uri === $path;
                }
            }
        }

        return $frontPage;
    }
}
