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
use JText;

// No direct access
defined('_JEXEC') or die();

/**
 * Metatags Container Factory Class
 *
 * @since  1.0
 */
class Factory
{
    /**
     * Class instance
     *
     * @var Factory
     */
    private static $instance;

    /**
     * Features cache
     *
     * @var     array
     * @access  private
     * @since   1.0
     */
    private $features = null;

    /**
     * Metadata By Query Map
     *
     * @var     array
     * @access  public
     * @since   1.0
     */
    public $metadataByQueryMap = array();

    /**
     * A cache for the metadata
     *
     * @var array
     */
    protected $metadata;

    /**
     * Get the singleton instance of this class
     *
     * @return Factory The instance
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Method to get container by ID
     *
     * @param string $type   Container Type
     * @param array  $params Request params
     *
     * @access  public
     *
     * @return mixed
     */
    public function getContainerById($type, $params = null)
    {
        $features = $this->getFeatures();
        $container = 'com_content:Article';

        if (isset($features[$type])) {
            if (class_exists($features[$type]["class"])) {
                eval('$container = new ' . $features[$type]["class"] . '();');
            }
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
    public function getContainerByRequest($queryString = null)
    {
        $params = array();
        $resultFeatureId = null;
        $resultFeaturePriority = -1;

        if ($queryString != null) {
            parse_str($queryString, $params);
        }

        $features = $this->getFeatures();
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

        return $this->getContainerById($resultFeatureId, $params);
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
    public function getContainerByComponentName($component)
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
    public function getMetadata($queryString)
    {
        $result = array();

        if (isset($this->$metadataByQueryMap[$queryString])) {
            $result = $this->$metadataByQueryMap[$queryString];
        } else {
            $container = $this->getContainerByRequest($queryString);

            if ($container != null) {
                $result = $container->getMetadataByRequest($queryString);
                $this->$metadataByQueryMap[$queryString] = $result;
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
    public function processBody($body, $queryString)
    {
        $container = $this->getContainerByRequest($queryString);

        if ($container != null && is_object($container)) {
            $this->metadata = $container->getMetadataByRequest($queryString);

            $this->processMetadata($this->metadata, $queryString);

            // Meta title
            if ($this->metadata && isset($this->metadata["metatitle"]) && !empty($this->metadata["metatitle"])) {
                $replaced = 0;

                $config = JFactory::getConfig();

                $metaTitle           = $this->metadata["metatitle"];
                $configSiteNameTitle = $config->get('sitename_pagetitles');
                $configSiteName      = $config->get('sitename');
                $siteNameSeparator   = '-';
                $browserTitle        = '';

                // Check the site name title global setting
                if ($configSiteNameTitle == 1) {
                    // Add site name before
                    $browserTitle = $configSiteName . ' ' . $siteNameSeparator . ' ' . $metaTitle;
                } elseif ($configSiteNameTitle == 2) {
                    // Add site name after
                    $browserTitle = $metaTitle . ' ' . $siteNameSeparator . ' ' . $configSiteName;
                } else {
                    // No site name
                    $browserTitle = $metaTitle;
                }

                // Process the window title tag
                $body = preg_replace(
                    "/<title[^>]*>[^<]*<\/title>/i",
                    '<title>' . htmlspecialchars($browserTitle) . '</title>',
                    $body,
                    1,
                    $replaced
                );

                // Process the meta title
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
                    '<meta name="title" content="' . htmlspecialchars($metaTitle) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        "<head>\n  <meta name=\"title\" content=\"" . htmlspecialchars($metaTitle) . '" />',
                        $body,
                        1
                    );
                }
            } elseif ($this->metadata) {
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+title[\\\"\\\']+[^>]*>/i",
                    '',
                    $body,
                    1,
                    $replaced
                );
            }

            // Meta description
            if ($this->metadata && isset($this->metadata["metadescription"]) && !empty($this->metadata["metadescription"])) {
                $replaced = 0;

                $metaDescription = $this->metadata["metadescription"];

                // Meta description tag
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\\\"\\\']+description[\\\"\\\']+[^>]*>/i",
                    '<meta name="description" content="' . htmlspecialchars($metaDescription) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        "<head>\n  <meta name=\"description\" content=\"" . htmlspecialchars($metaDescription) . '" />',
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
    public function setMetadataByRequest($query, $metadata)
    {
        $container = $this->getContainerByRequest($query);

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
    public function getFeatures()
    {
        if (empty($this->features)) {
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

                if (class_exists($class)) {
                    if (! $class::isAvailable()) {
                        unset($features[$key]);
                    }
                }
            }

            $this->features = $features;
        }

        return $this->features;
    }

    /**
     * Process the metada
     *
     * @param  array  $metadata    The metadata
     * @param  string $queryString Request query string
     * @return void
     */
    protected function processMetadata(&$metadata, $queryString)
    {
    }
}
