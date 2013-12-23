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

function com_content_get_url(&$article, $isNew){
    $url = null;
    if ($article->state == 1){
        $slug=$article->alias?$article->id.":".$article->alias:$article->id;

        $app    = JApplication::getInstance('site');
        $router = $app->getRouter();
        if (!class_exists('ContentHelperRoute')) {
            JLoader::import('components.com_content.helpers.route',JPATH_SITE);
        }
        $uri = $router->build(ContentHelperRoute::getArticleRoute($slug, $article->catid, $article->sectionid));
        $url = $uri->toString();
        if (strpos($url, "/administrator") === 0){
            $url = substr($url, strlen("/administrator"));
        }
    }
    return $url;
}

function com_content_get_rss_url(){
    return "/index.php?format=feed&type=rss";
}
