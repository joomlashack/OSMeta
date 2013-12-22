<?php
/**
 * @category  Joomla Component
 * @package   osmeta
 * @author    JoomBoss
 * @copyright 2012, JoomBoss. All rights reserved
 * @copyright 2013 Open Source Training, LLC. All rights reserved
 * @contact   www.ostraining.com, support@ostraining.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version   1.0.0
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

class FileList{

    public function __construct($listPath){
        $this->files = file($listPath);
        if ($this->files === false){
            $this->files = array();
        }
        $this->path = $listPath;
    }

    public function addFile($path){
        if (!in_array($filePath, $this->files)){
            $this->files[] = $path;
        }
    }

    public function removeFile($path){
        $key = array_search($path, $this->files);
        if ($key !== FALSE){
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
