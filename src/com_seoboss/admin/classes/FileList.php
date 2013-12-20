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

jimport('joomla.filesystem.file');

class FileList{
    
    public function __construct($listPath){
        $this->files = file($listPath);
        if($this->files === false){
            $this->files = array();
        }
        $this->path = $listPath;
    }
    
    public function addFile($path){
        if(!in_array($filePath, $this->files)){
            $this->files[] = $path;
        }
    }
    
    public function removeFile($path){
        $key = array_search($path, $this->files);
        if( $key !== FALSE ){
            unset($this->files[$key]);
        }
    }
    
    public function save(){
        JFile::write($this->path, implode("\n", $this->files));
    }
    
    public function getFiles(){
        return $this->files;
    }
    private $files = array();
    private $path = "";
}
?>