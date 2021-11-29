<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSMeta.
 *
 * OSMeta is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSMeta is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSMeta.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\OSMeta\Free\Container;

use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') or die();

class Factory
{
    /**
     * Class instance
     *
     * @var self
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $features = null;

    /**
     * @var array
     */
    public $metadataByQueryMap = [];

    /**
     * @var array
     */
    protected $metadata = null;

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Method to get container by ID
     *
     * @param string $type Container Type
     *
     * @return mixed
     */
    public function getContainerById($type)
    {
        $features  = $this->getFeatures();
        $container = 'com_content:Article';

        if (isset($features[$type])) {
            if (class_exists($features[$type]['class'])) {
                $container = new $features[$type]['class']();
            }
        }

        return $container;
    }

    /**
     * @param string $queryString Query String
     *
     * @return mixed
     * @throws \Exception
     */
    public function getContainerByRequest($queryString = null)
    {
        $app                   = \Joomla\CMS\Factory::getApplication();
        $params                = [];
        $resultFeatureId       = null;
        $resultFeaturePriority = -1;

        if ($queryString != null) {
            parse_str($queryString, $params);
        }

        $features = $this->getFeatures();
        foreach ($features as $featureId => $feature) {
            $success = true;

            if (isset($feature['params'])) {
                foreach ($feature['params'] as $paramsArray) {
                    $success = true;

                    foreach ($paramsArray as $key => $value) {
                        if ($queryString != null) {
                            if ($value !== null) {
                                $success = $success && (isset($params[$key]) && $params[$key] == $value);
                            } else {
                                $success = $success && isset($params[$key]);
                            }
                        } elseif ($value !== null) {
                            $success = $success && ($app->input->getCmd($key) == $value);
                        } else {
                            $success = $success && ($app->input->getCmd($key) !== null);
                        }
                    }

                    if ($success) {
                        $resultFeatureId = $featureId;
                        break;
                    }
                }
            }

            $featurePriority = $feature['priority'] ?? 0;

            if ($success && $featurePriority > $resultFeaturePriority) {
                $resultFeatureId       = $featureId;
                $resultFeaturePriority = $featurePriority;
            }
        }

        return $this->getContainerById($resultFeatureId);
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
        $className = "\\Alledia\\OSMeta\\Free\\Container\\Component\\{$component}";

        if (class_exists($className)) {
            $container = new $className();
        }

        return $container;
    }

    /**
     * @param string $queryString Query string
     *
     * @return array
     * @throws \Exception
     */
    public function getMetadata($queryString)
    {
        $result = [];

        if (isset($this->metadataByQueryMap[$queryString])) {
            $result = $this->metadataByQueryMap[$queryString];
        } else {
            $container = $this->getContainerByRequest($queryString);

            if ($container != null) {
                $result                                 = $container->getMetadataByRequest($queryString);
                $this->metadataByQueryMap[$queryString] = $result;
            }
        }

        return $result;
    }

    /**
     * @param string $body        Body buffer
     * @param string $queryString Query string
     *
     * @return string
     * @throws \Exception
     */
    public function processBody($body, $queryString)
    {
        $container = $this->getContainerByRequest($queryString);

        if ($container != null && is_object($container)) {
            $this->metadata = $container->getMetadataByRequest($queryString);

            $this->processMetadata($this->metadata, $queryString);

            // Meta title
            if ($this->metadata && isset($this->metadata['metatitle']) && !empty($this->metadata['metatitle'])) {
                $replaced = 0;

                $config = \Joomla\CMS\Factory::getConfig();

                $metaTitle           = $this->metadata['metatitle'];
                $configSiteNameTitle = $config->get('sitename_pagetitles');
                $configSiteName      = $config->get('sitename');
                $siteNameSeparator   = '-';
                $browserTitle        = '';

                // Check the site name title global setting
                if ($configSiteNameTitle == 1) {
                    $stringToAppend = "{$configSiteName} {$siteNameSeparator} ";

                    // Check if the sitename is already there
                    if (!preg_match('#^' . preg_quote($stringToAppend) . '#', $metaTitle)) {
                        // Add site name before
                        $browserTitle = $stringToAppend;
                    }

                    $browserTitle .= $metaTitle;
                } elseif ($configSiteNameTitle == 2) {
                    $browserTitle   = $metaTitle;
                    $stringToAppend = " {$siteNameSeparator} {$configSiteName}";

                    // Check if the sitename is already there
                    if (!preg_match('#' . preg_quote($stringToAppend) . '$#', $metaTitle)) {
                        // Add site name after
                        $browserTitle .= $stringToAppend;
                    }
                } else {
                    // No site name
                    $browserTitle = $metaTitle;
                }

                // Process the window title tag
                $body = preg_replace(
                    '/<title[^>]*>[^<]*<\/title>/i',
                    '<title>' . htmlspecialchars($browserTitle) . '</title>',
                    $body,
                    1,
                    $replaced
                );

                // Process the meta title
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\"\\\']+title[\"\\\']+[^>]*>/i",
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
                    "/<meta[^>]*name[\\s]*=[\\s]*[\"\\\']+title[\"\\\']+[^>]*>/i",
                    '',
                    $body,
                    1,
                    $replaced
                );
            }

            // Meta description
            if ($this->metadata && isset($this->metadata['metadescription']) && !empty($this->metadata['metadescription'])) {
                $replaced = 0;

                $metaDescription = $this->metadata['metadescription'];

                // Meta description tag
                $body = preg_replace(
                    "/<meta[^>]*name[\\s]*=[\\s]*[\"\\\']+description[\"\\\']+[^>]*>/i",
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
     * @param string $query    Query
     * @param string $metadata Metadata
     *
     * @return void
     * @throws \Exception
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
            $features = [];

            $path  = JPATH_SITE . '/administrator/components/com_osmeta/features';
            $files = Folder::files($path, '.php');

            $features = [];
            foreach ($files as $file) {
                include $path . '/' . $file;
            }

            foreach ($features as $key => $value) {
                $class = $value['class'];

                if (class_exists($class)) {
                    if (!$class::isAvailable()) {
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
     * @param array  $metadata    The metadata
     * @param string $queryString Request query string
     *
     * @return void
     */
    protected function processMetadata(&$metadata, $queryString)
    {
    }
}
