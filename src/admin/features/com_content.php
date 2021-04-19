<?php
/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2021 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSMeta.  If not, see <http://www.gnu.org/licenses/>.
 */

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
