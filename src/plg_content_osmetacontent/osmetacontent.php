<?php
/**
 * @category   Joomla Content Plugin
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$app->registerEvent('onAfterContentSave', 'pluginOSMeta_onAfterContentSave');
$app->registerEvent('onContentAfterSave', 'pluginOSMeta_onContentAfterSave');
$app->registerEvent('onContentPrepareForm', 'pluginOSMeta_onContentPrepareForm');

function pluginOSMeta_onAfterContentSave($content, $isNew)
{
    $app = JFactory::getApplication();
    $input = $app->input;

    $option = $input->getCmd('option');

    if (is_object($content) && isset($content->id) && $content->id && isset($content->metakey) && $content->metakey) {
        require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/classes/MetatagsContainerFactory.php';

        $container = MetatagsContainerFactory::getContainerByComponentName($option);
        $container->saveKeywords($content->metakey, $content->id);

        // Joomla 3.x Backward Compatibility
        if (version_compare(JVERSION, '3.0', '<')) {
            $articleOSMetadataInput = json_decode($content->metadata);
            $id = array($content->id);
            $title = array($articleOSMetadataInput->metatitle);
            $metaDesc = array($content->metadesc);
            $metaKey = array($content->metakey);
            $titleTag = array($articleOSMetadataInput->title_tag);
        } else {
            $articleOSMetadataInput = $app->input->get('osmeta-fields', '', 'array');
            $id = array($content->id);
            $title = array($articleOSMetadataInput['title']);
            $metaDesc = array($content->metadesc);
            $metaKey = array($content->metakey);
            $titleTag = array($articleOSMetadataInput['title_tag']);
        }

        $container->saveMetatags($id, $title, $metaDesc, $metaKey, $titleTag);
    }
}

function pluginOSMeta_onContentAfterSave($context, $content, $isNew)
{
    pluginOSMeta_onAfterContentSave($content, $isNew);
}

function pluginOSMeta_onContentPrepareForm($form, $data)
{
    /*
     * Inject the metadata fields for Joomla 2.5
     *
     * For Joomla 3.0, look at: plugins/system/osmetarenderer/osmetarenderer.php,
     * into the onAfterInitialise event.
     */
    // Joomla 3.x Backward Compatibility
    if (version_compare(JVERSION, '3.0', '<')) {
        $app = JFactory::getApplication();

        if ($app->getName() === 'administrator' && $form->getName() === 'com_content.article') {
            jimport('joomla.filesystem.file');

            $lang = JFactory::getLanguage();
            $lang->load('com_osmeta');

            $xml = JFile::read(JPATH_ROOT . '/plugins/content/osmetacontent/forms/metadata.xml');
            $form->load($xml);

            // Add Javascript code to sort the fields
            $doc = JFactory::getDocument();
            $doc->addScriptDeclaration('
                    // Native JavaScript

                    /*!
                      * domready (c) Dustin Diaz 2012 - License MIT
                      */
                    !function (e,t) {typeof module!="undefined"?module.exports=t():typeof define=="function"&&typeof define.amd=="object"?define(t):this[e]=t()}("domready",function (e) {function p(e) {h=1;while (e=t.shift())e()}var t=[],n,r=!1,i=document,s=i.documentElement,o=s.doScroll,u="DOMContentLoaded",a="addEventListener",f="onreadystatechange",l="readyState",c=o?/^loaded|^c/:/^loaded|c/,h=c.test(i[l]);return i[a]&&i[a](u,n=function () {i.removeEventListener(u,n,r),p()},r),o&&i.attachEvent(f,n=function () {/^c/.test(i[l])&&(i.detachEvent(f,n),p())}),e=o?function (n) {self!=top?h?n():t.push(n):function () {try {s.doScroll("left")} catch (t){return setTimeout(function () {e(n)},50)}n()}()}:function (e) {h?e():t.push(e)}})

                    domready(function () {
                        var metaTitle = document.getElementById("jform_metadata_metatitle");
                        var titleTag = document.getElementById("jform_metadata_title_tag");
                        var fieldGroup = metaTitle.parentNode.parentNode;

                        fieldGroup.insertBefore(titleTag.parentNode, fieldGroup.firstChild);
                        fieldGroup.insertBefore(metaTitle.parentNode, fieldGroup.firstChild);
                    });
                '
            );
        }
    }
}
