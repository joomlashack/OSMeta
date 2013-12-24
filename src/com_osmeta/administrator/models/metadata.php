<?php
/**
 * @category   Joomla Component
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

require_once JPATH_ADMINISTRATOR . '/components/com_osmeta/models/model.php';

/**
 * Model Options
 *
 * @since  1.0.0
 */
class OSModelMetadata extends OSModel
{
	/**
	 * Get metadata values
	 *
	 * @param   int  $id  Item ID
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  array
	 */
	public function getMetadata($id = 0)
	{
		$data = array();

		if (!empty($id))
		{
			$db = JFactory::getDBO();
			$db->setQuery("SELECT * from #__osmeta_metadata
				WHERE item_id = " . $db->quote($id)
			);

			$data = $db->loadObject();
		}

		return $data;
	}

	/**
	 * Store metadata values
	 *
	 * @param   int    $id    Item ID
	 * @param   array  $data  Metadata Information
	 *
	 * @access	public
	 * @since   1.0.0
	 *
	 * @return  void
	 */
	public function storeMetadata($id = 0, $data = array())
	{
		if (!empty($id))
		{
			$db = JFactory::getDBO();

			// Check if we need to update or insert the information
			$db->setQuery("SELECT count(*) from #__osmeta_metadata
				WHERE item_id = " . $db->quote($id)
			);
			$count = $db->loadResult();

			if ($count > 0)
			{
				// UPDATE

				$fields = array();

				foreach ($data as $key => $value)
				{
					$fields[] = $db->quoteName($key) . ' = ' . $db->quote($value);
				}

				$sql = 'UPDATE #__osmeta_metadata SET ';
				$sql .= implode(',', $fields);
				$sql .= ' WHERE item_id = ' . $db->quote($id);
				$db->setQuery($sql);
			}
			else
			{
				// INSERT

				$fields = array();
				$values = array();

				foreach ($data as $key => $value)
				{
					$fields[] = $db->quoteName($key);
					$values[] = $db->quote($value);
				}

				$sql = 'INSERT INTO #__osmeta_metadata (' . $db->quoteName('item_id') . ',' . $db->quoteName('item_type') . ',';
				$sql .= implode(',', $fields);
				$sql .= ') VALUES (' . $db->quote($id) . ',1,';
				$sql .= implode(',', $values);
				$sql .= ')';

				$db->setQuery($sql);
			}


			$data = $db->execute();
		}
	}
}
