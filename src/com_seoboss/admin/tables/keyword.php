<?php
/*------------------------------------------------------------------------
# SEO Boss Pro
# ------------------------------------------------------------------------
# author    JoomBoss
# copyright Copyright (C) 2012 Joomboss.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomboss.com
# Technical Support:  Forum - http://joomboss.com/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class TableKeyword extends JTable {
  var $id = null;
  var $name = null;
  var $published = null;
  var $url = null;

  function __construct(&$db) {
    parent::__construct( '#__seoboss_keywords', 'id', $db );
  }
}
?>
