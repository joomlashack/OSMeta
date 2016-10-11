<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2016 Open Source Training, LLC, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

$features['com_content:Article'] = array(
    'name'     => 'COM_OSMETA_ARTICLES',
    'priority' => 1,
    'class'    => 'Alledia\OSMeta\Free\Container\Component\Content',
    'params'   => array(
        array(
            'option' => 'com_content',
            'view'   => 'article'
        ),
        array(
            'option' => 'com_content',
            'view'   => 'frontpage'
        ),
        array(
            'option' => 'com_content',
            'view'   => 'featured'
        )
    )
);

$features['com_content:ArticleCategory'] = array(
    'name'     => 'COM_OSMETA_ARTICLE_CATEGORIES',
    'priority' => 1,
    'class'    => 'Alledia\OSMeta\Free\Container\Component\Categories',
    'params'   => array(
        array(
            'option' => 'com_content',
            'view' => 'category'
        )
    )
);
