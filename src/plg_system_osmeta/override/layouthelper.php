<?php
/**
 * @category   Joomla Class Override
 * @package    Osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Helper to render a JLayout object, storing a base path
 *
 * @package     Joomla.Libraries
 * @subpackage  Layout
 * @see         http://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since       3.1
 */
class JLayoutHelper
{
	/**
	 * A default base path that will be used if none is provided when calling the render method.
	 * Note that JLayoutFile itself will defaults to JPATH_ROOT . '/layouts' if no basePath is supplied at all
	 *
	 * @var    string
	 * @since  3.1
	 */
	public static $defaultBasePath = '';

	/**
	 * Method to render the layout.
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   mixed   $options      Optional custom options to load. JRegistry or array format
	 *
	 * @return  string
	 *
	 * @since   3.1
	 */
	public static function render($layoutFile, $displayData = null, $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to JLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new JLayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->render($displayData);

		/* ######### Start class customization ##############
		 *
		 * Add some fields to the article edit form.
		 *
		 * It was not possible to inject fields using the form->load
		 * xml file, because the fields and fieldset were converted
		 * to a new tab, and it is not the desired behavior.
		 */
		if ($layoutFile === 'joomla.edit.global')
		{
			$app = JFactory::getApplication();
			$input = $app->input;
			$option = $input->getCmd('option');

			if (($option === 'com_content' || $option === 'com_categories')
				&& ($input->getCmd('view') === 'article' || $input->getCmd('view') === 'category')
				&& $input->getCmd('layout') === 'edit')
			{
				$id = $input->getInt('id');

				$lang = JFactory::getLanguage();
				$lang->load('com_osmeta');

				// Get the metadata information
				require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/classes/MetatagsContainerFactory.php';
				$container = MetatagsContainerFactory::getContainerByComponentName($option);
				$metadata = $container->getMetadata($id);

				// Load the complementary form fields
				$xml = file_get_contents(JPATH_ROOT . '/plugins/system/osmetarenderer/override/forms/metadata.xml');
				$form = new JForm('osmeta-metadata');
				$form->load($xml);
				$fieldset = $form->getFieldset();

				$html = '';

				foreach ($fieldset as $field)
				{
					$name = preg_replace('/(osmeta\-fields\[|\])/', '', $field->name);

					if ($name === 'item_type')
					{
						// Container type
						$field->value = $input->getCmd('option') === 'com_content' ? 1 : 4;
					}
					elseif (is_array($metadata) && isset($metadata[$name]))
					{
						// Metadata
						$field->value = $metadata[$name];
					}

					$html .= $field->getControlGroup();
				}

				$html .= '</fieldset>';

				// Add JS to move the Meta Description field to the Content Tab
				$html .= '
					<script>
						// Native JavaScript

						/*!
						  * domready (c) Dustin Diaz 2012 - License MIT
						  */
						!function(e,t){typeof module!="undefined"?module.exports=t():typeof define=="function"&&typeof define.amd=="object"?define(t):this[e]=t()}("domready",function(e){function p(e){h=1;while(e=t.shift())e()}var t=[],n,r=!1,i=document,s=i.documentElement,o=s.doScroll,u="DOMContentLoaded",a="addEventListener",f="onreadystatechange",l="readyState",c=o?/^loaded|^c/:/^loaded|c/,h=c.test(i[l]);return i[a]&&i[a](u,n=function(){i.removeEventListener(u,n,r),p()},r),o&&i.attachEvent(f,n=function(){/^c/.test(i[l])&&(i.detachEvent(f,n),p())}),e=o?function(n){self!=top?h?n():t.push(n):function(){try{s.doScroll("left")}catch(t){return setTimeout(function(){e(n)},50)}n()}()}:function(e){h?e():t.push(e)}})

						domready(function() {
							var metaDesc = document.getElementById("jform_metadesc");
							var fieldGroup = metaDesc.parentNode.parentNode;

							metaDesc.className += " span12";

							document.querySelector("#general div.span3 fieldset").appendChild(fieldGroup);
						});
					</script>';

				// Inject the field control groups to the end of rendered form
				$renderedLayout = preg_replace('/<\/fieldset>$/', $html, $renderedLayout);
			}
		}
		// ############ End class customization ###############

		return $renderedLayout;
	}
}
