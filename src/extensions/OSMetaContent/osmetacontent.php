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

use Alledia\Framework\Joomla\Extension;
use Alledia\Framework;
use Alledia\OSMeta;
use Joomla\CMS\Form\Form;

defined('_JEXEC') or die();

include_once JPATH_ADMINISTRATOR . '/components/com_osmeta/include.php';

jimport('joomla.filesystem.file');

if (defined('OSMETA_LOADED')) {
    class PlgContentOSMetaContent extends Extension\AbstractPlugin
    {
        /**
         * Event method onContentAfterSave, to store the meta data from the article form
         *
         * @access  public
         *
         * @return bool
         */
        public function onContentAfterSave($context, $content, $isNew)
        {
            if ($context === 'com_content.article' || $context === 'com_categories.category') {
                $app   = Framework\Factory::getApplication();
                $input = $app->input;

                $option = $input->getCmd('option');

                if (is_object($content) && isset($content->id)) {
                    if (class_exists('Alledia\OSMeta\Pro\Container\Factory')) {
                        $factory = OSMeta\Pro\Container\Factory::getInstance();
                    } else {
                        $factory = OSMeta\Free\Container\Factory::getInstance();
                    }

                    $container = $factory->getContainerByComponentName($option);
                    if (is_object($container)) {
                        $articleOSMetadataInput = json_decode($content->metadata);

                        $id       = array($content->id);
                        $title    = array($articleOSMetadataInput->metatitle);
                        $metaDesc = array($content->metadesc);
                        $alias    = array($content->alias);

                        $container->saveMetatags($id, $title, $metaDesc, $alias);
                    }
                }
            }

            return true;
        }

        /**
         * Event method onAfterContentSave, to store the meta data from the article form
         *
         * @access  public
         *
         * @return bool
         */
        public function onAfterContentSave($content, $isNew)
        {
            $this->onContentAfterSave('', $content, $isNew);

            return true;
        }

        /**
         * @param Form  $form
         * @param array $data
         *
         * @return void
         * @throws Exception
         */
        public function onContentPrepareForm($form, $data)
        {
            if ($form->getName() === 'com_content.article'
                || $form->getName() === 'com_categories.category'
                || $form->getName() === 'com_categories.categorycom_content'
            ) {
                Framework\Factory::getLanguage()->load('com_osmeta', OSMETA_ADMIN);

                $xml = file_get_contents(JPATH_ROOT . '/plugins/content/osmetacontent/forms/metadata3.xml');
                $js  = '
                        domready(function () {
                            // Browser title and Meta title fields
                            var metaTitle = document.getElementById("jform_metadata_metatitle");
                            var fieldGroup = metaTitle.parentNode.parentNode.parentNode;

                            fieldGroup.insertBefore(metaTitle.parentNode.parentNode, fieldGroup.firstChild);
                        });';

                $form->load($xml, true);

                // Add Javascript code to sort the fields
                $doc = Framework\Factory::getDocument();

                $doc->addScriptDeclaration(
                    '/*!
                          * domready (c) Dustin Diaz 2012 - License MIT
                          */
                        !function (e,t) {typeof module!="undefined"?module.exports=t():typeof define=="function"&&typeof define.amd=="object"?define(t):this[e]=t()}("domready",function (e) {function p(e) {h=1;while (e=t.shift())e()}var t=[],n,r=!1,i=document,s=i.documentElement,o=s.doScroll,u="DOMContentLoaded",a="addEventListener",f="onreadystatechange",l="readyState",c=o?/^loaded|^c/:/^loaded|c/,h=c.test(i[l]);return i[a]&&i[a](u,n=function () {i.removeEventListener(u,n,r),p()},r),o&&i.attachEvent(f,n=function () {/^c/.test(i[l])&&(i.detachEvent(f,n),p())}),e=o?function (n) {self!=top?h?n():t.push(n):function () {try {s.doScroll("left")} catch (t){return setTimeout(function () {e(n)},50)}n()}()}:function (e) {h?e():t.push(e)}})
                    '
                );
                $doc->addScriptDeclaration($js);
            }
        }
    }
}
