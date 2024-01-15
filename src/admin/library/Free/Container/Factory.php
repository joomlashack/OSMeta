<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2024 Joomlashack.com. All rights reserved
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

use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\CMS\Filesystem\Folder;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

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
    protected $metadata = null;

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Method to get container by ID
     *
     * @param string $type
     *
     * @return ?AbstractContainer
     */
    public function getContainerById(string $type): ?AbstractContainer
    {
        $features  = $this->getFeatures();
        $className = $features[$type]['class'] ?? null;

        if ($className && class_exists($className)) {
            return new $className();
        }

        return null;
    }

    /**
     * @param ?string $queryString
     *
     * @return ?AbstractContainer
     * @throws \Exception
     */
    public function getContainerByRequest(?string $queryString = null): ?AbstractContainer
    {
        $app                   = JoomlaFactory::getApplication();
        $resultFeatureId       = null;
        $resultFeaturePriority = -1;
        $features              = $this->getFeatures();

        parse_str((string)$queryString, $query);

        // @TODO: There has to be a better way to do this!
        foreach ($features as $featureId => $feature) {
            $success = true;

            $params = $feature['params'] ?? [];
            foreach ($params as $param) {
                $success = true;

                foreach ($param as $key => $value) {
                    if ($query) {
                        if ($value !== null) {
                            $success = $success && (isset($query[$key]) && $query[$key] == $value);

                        } else {
                            $success = $success && isset($query[$key]);
                        }

                    } elseif ($value !== null) {
                        $success = $success && ($app->input->getCmd($key) == $value);

                    } else {
                        $success = $success && ($app->input->getCmd($key) !== null);
                    }
                }

                if ($success) {
                    $resultFeatureId = $featureId;
                    break 2;
                }
            }

            $featurePriority = $feature['priority'] ?? 0;

            if ($success && $featurePriority > $resultFeaturePriority) {
                $resultFeatureId = $featureId;
            }
        }

        return $resultFeatureId ? $this->getContainerById($resultFeatureId) : null;
    }

    /**
     * Method to get container by component name
     *
     * @param string $component Component Name
     *
     * @return ?AbstractContainer
     */
    public function getContainerByComponentName(string $component): ?AbstractContainer
    {
        $component = ucfirst(str_replace('com_', '', $component));
        $className = "\\Alledia\\OSMeta\\Free\\Container\\Component\\{$component}";

        if (class_exists($className)) {
            $container = new $className();
        }

        return $container ?? null;
    }

    /**
     * @param string $body
     * @param string $queryString
     *
     * @return string
     * @throws \Exception
     */
    public function processBody(string $body, string $queryString): string
    {
        if ($container = $this->getContainerByRequest($queryString)) {
            $this->metadata = $container->getMetadataByRequest($queryString);

            $this->processMetadata($this->metadata, $queryString);

            // Meta title
            if ($this->metadata && empty($this->metadata['metatitle']) == false) {
                $replaced = 0;

                $config = JoomlaFactory::getConfig();

                $metaTitle           = $this->metadata['metatitle'];
                $configSiteNameTitle = $config->get('sitename_pagetitles');
                $configSiteName      = $config->get('sitename');
                $siteNameSeparator   = '-';
                $browserTitle        = '';

                // Check the site name title global setting
                switch ($configSiteNameTitle) {
                    case 1:
                        $stringToAppend = "{$configSiteName} {$siteNameSeparator} ";

                        if (preg_match('#^' . preg_quote($stringToAppend) . '#', $metaTitle) == false) {
                            // Add site name before
                            $browserTitle = $stringToAppend;
                        }

                        $browserTitle .= $metaTitle;
                        break;

                    case 2:
                        $browserTitle   = $metaTitle;
                        $stringToAppend = " {$siteNameSeparator} {$configSiteName}";

                        if (preg_match('#' . preg_quote($stringToAppend) . '$#', $metaTitle) == false) {
                            // Add site name after
                            $browserTitle .= $stringToAppend;
                        }
                        break;

                    default:
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
                    "/<meta[^>]*name\s*=\s*[\"']+title[\"']+[^>]*>/i",
                    '<meta name="title" content="' . htmlspecialchars($metaTitle) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        sprintf("<head>\n  <meta name=\"title\" content=\"%s\" />", htmlspecialchars($metaTitle)),
                        $body,
                        1
                    );
                }
            } elseif ($this->metadata) {
                $body = preg_replace(
                    "/<meta[^>]*name\s*=\s*[\"']+title[\"']+[^>]*>/i",
                    '',
                    $body,
                    1
                );
            }

            // Meta description
            if (
                $this->metadata
                && isset($this->metadata['metadescription'])
                && empty($this->metadata['metadescription']) == false
            ) {
                $replaced = 0;

                $metaDescription = $this->metadata['metadescription'];

                // Meta description tag
                $body = preg_replace(
                    "/<meta[^>]*name\s*=\s*[\"']+description[\"']+[^>]*>/i",
                    '<meta name="description" content="' . htmlspecialchars($metaDescription) . '" />',
                    $body,
                    1,
                    $replaced
                );

                if ($replaced != 1) {
                    $body = preg_replace(
                        '/<head>/i',
                        sprintf(
                            "<head>\n  <meta name=\"description\" content=\"%s\" />",
                            htmlspecialchars($metaDescription)
                        ),
                        $body,
                        1
                    );
                }
            }
        }

        return $body;
    }

    /**
     * Method to get all available features
     *
     * @access  public
     *
     * @return array
     */
    public function getFeatures(): array
    {
        if ($this->features === null) {
            $path  = JPATH_ADMINISTRATOR . '/components/com_osmeta/features';
            $files = Folder::files($path, '.php');

            $features = [];
            foreach ($files as $file) {
                include $path . '/' . $file;
            }

            $this->features = $features;
        }

        return $this->features;
    }

    /**
     * Process the metada
     *
     * @param array  $metadata
     * @param string $queryString
     *
     * @return void
     */
    protected function processMetadata(array &$metadata, string $queryString): void
    {
    }
}
