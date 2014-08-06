<?php
/**
 * @category   Joomla Component
 * @package    com_osmeta
 * @author     JoomBoss
 * @copyright  2012, JoomBoss. All rights reserved
 * @copyright  2013 Open Source Training, LLC. All rights reserved
 * @contact    www.ostraining.com, support@ostraining.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version    1.0.2
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('cms.model.legacy');

/**
 * Model Options
 *
 * @since  1.0
 */
class OSModelOptions extends JModelLegacy
{
    /**
     * Get Options (fixed options, for now)
     *
     * @access	public
     * @since   1.0
     *
     * @return Object
     */
    public function getOptions()
    {
        $options = new stdClass;

        $options->max_description_length = 255;

        return $options;
    }
}
