<?php
/**
 * @category   Joomla Component
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

require_once JPATH_COMPONENT . '/views/view.php';

/**
 * Metatags Manager Default View
 *
 * @since  1.0.0
 */
class OSMetaViewOSMeta extends OSView
{
    /**
     * Class constructor method
     *
     * @param mix $config Configuration set
     *
     * @access  public
     * @since   1.0.0
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->_addPath('template', $this->_basePath . '/views/osmeta/tmpl');
    }

    /**
     * Method to display the view
     *
     * @param string $tpl Template file
     *
     * @access  public
     *
     * @return void
     */
    public function display($tpl = null)
    {
        JToolBarHelper::title(JText::_('COM_OSMETA_META_TAGS_MANAGER'));

        JToolBarHelper::apply("save");

        if (version_compare(JVERSION, '3.0', '>=')) {
            $iconShuffle = 'shuffle';
            $iconEdit = 'pencil-2';
        } else {
            $iconShuffle = 'refresh';
            $iconEdit = 'edit';
        }

        JToolBarHelper::custom(
            'copyBrowserTitleToKeywords',
            $iconShuffle,
            '',
            JText::_('COM_OSMETA_COPY_ITEM_TITLE_TO_KEYWORDS'),
            true
        );

        JToolBarHelper::custom(
            'copyItemTitleToSearchEngineTitle',
            $iconShuffle,
            '',
            JText::_('COM_OSMETA_COPY_ITEM_TITLE_TO_TITLE'),
            true
        );

        JToolBarHelper::custom(
            'generateDescriptions',
            $iconEdit,
            '',
            JText::_('COM_OSMETA_GENERATE_DESCRIPTIONS'),
            true
        );

        JToolBarHelper::custom(
            'clearBrowserTitles',
            'remove',
            '',
            JText::_('COM_OSMETA_CLEAR_BROWSER_TITLES'),
            true
        );

        JToolBarHelper::cancel("cancel");

        $doc = JFactory::getDocument();
        $doc->addStylesheet('../media/com_osmeta/admin/css/main.css');

        parent::display($tpl);
    }
}
