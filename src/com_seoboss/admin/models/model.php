<?php
/**
* @package		Login Boss
* @copyright	JoomBoss team
* @license		GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

if (version_compare(JVERSION, "3.0", "ge")){
  class JBModel extends JModelLegacy{}
}else{
  class JBModel extends JModel{}
}
