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

        JToolBarHelper::custom('metatags_copy_keywords_to_title', 'apply', '',
            JText::_('COM_OSMETA_COPY_KEYWORDS_TO_TITLE'), true);

        JToolBarHelper::custom('metatags_copy_title_to_keywords', 'apply', '',
            JText::_('COM_OSMETA_COPY_TITLE_TO_KEYWORDS'), true);

        JToolBarHelper::custom('metatags_copy_item_title_to_keywords', 'apply', '',
            JText::_('COM_OSMETA_COPY_ITEM_TITLE_TO_KEYWORDS'), true);

        JToolBarHelper::custom('metatags_copy_item_title_to_title', 'apply', '',
            JText::_('COM_OSMETA_COPY_ITEM_TITLE_TO_TITLE'), true);

        JToolBarHelper::custom('metatags_generare_descriptions', 'apply', '',
            JText::_('COM_OSMETA_GENERATE_DESCRIPTIONS'), true);

        JToolBarHelper::custom('metatags_clear_browser_titles', 'apply', '',
            JText::_('COM_OSMETA_CLEAR_BROWSER_TITLES'), true);

        JToolBarHelper::save("metatags_save");
        JToolBarHelper::cancel("metatags_cancel");

        parent::display($tpl);
    }
}
