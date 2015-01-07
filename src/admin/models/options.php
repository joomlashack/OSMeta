<?php
/**
 * @package   OSMeta
 * @contact   www.alledia.com, support@alledia.com
 * @copyright 2013-2014 Alledia.com, All rights reserved
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

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

        $options->max_description_length = 160;

        return $options;
    }
}
