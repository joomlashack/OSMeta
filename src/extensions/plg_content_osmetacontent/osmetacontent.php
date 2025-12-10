<?php

/**
 * @package   OSMeta
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2013-2026 Joomlashack.com. All rights reserved
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

use Alledia\Framework\Joomla\Extension\AbstractPlugin;
use Alledia\OSMeta\ContainerFactory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Categories\Administrator\Table\CategoryTable;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

$includePath = JPATH_ADMINISTRATOR . '/components/com_osmeta/include.php';
if ((is_file($includePath) && (include $includePath)) == false) {
    class_alias(CMSPlugin::class, AbstractPlugin::class);
}

// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class PlgContentOSMetaContent extends AbstractPlugin
{
    /**
     * @var CMSApplication
     */
    protected $app = null;

    /**
     * @param string                                $context
     * @param CategoriesTableCategory|CategoryTable $content
     *
     * @return bool
     * @throws Exception
     */
    public function onContentAfterSave($context, $content): bool
    {
        if (
            $this->isEnabled()
            && (
                $context === 'com_content.article'
                || $context === 'com_categories.category'
            )
        ) {
            $input = $this->app->input;

            $option    = $input->getCmd('option');
            $contentId = $content->id ?? null;

            if ($contentId) {
                if ($container = ContainerFactory::getInstance()->getContainerByComponentName($option)) {
                    $contentMetadata  = json_decode($content->metadata ?? '');
                    $contentMetatitle = $contentMetadata->metatitle ?? '';

                    $container->saveMetatags(
                        [$contentId],
                        [$contentMetatitle],
                        [$content->metadesc ?? ''],
                        [$content->alias ?? '']
                    );
                }
            }
        }

        return true;
    }

    /**
     * @param Form $form
     *
     * @return void
     * @throws Exception
     */
    public function onContentPrepareForm(Form $form): void
    {
        $formName = $form->getName();
        if (
            $formName == 'com_content.article'
            || $formName == 'com_categories.categorycom_content'
        ) {
            Factory::getLanguage()->load('com_osmeta', OSMETA_ADMIN);

            $formPath = JPATH_PLUGINS . '/content/osmetacontent/forms/metadata.xml';
            if (is_file($formPath)) {
                $form->load(file_get_contents($formPath));

                // Move our custom field to the top and hide the article core field
                $this->app->getDocument()->addScriptDeclaration(<<<JSCRIPT
window.addEventListener('DOMContentLoaded', function () {
    let metaTitle  = document.getElementById('jform_metadata_metatitle'),
        fieldGroup = metaTitle.parentNode.parentNode.parentNode;

    fieldGroup.insertBefore(metaTitle.parentNode.parentNode, fieldGroup.firstChild);
    
    let article_page_title = document.getElementById('jform_attribs_article_page_title');
    if (article_page_title) {
        article_page_title.closest('.control-group').style.display = 'none';
    }
});
JSCRIPT
                );
            }
        }
    }

    /**
     * @return bool
     */
    protected function isEnabled(): bool
    {
        return class_exists(ContainerFactory::class);
    }
}
