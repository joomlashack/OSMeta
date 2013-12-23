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
// No direct access
defined('_JEXEC') or die('Restricted access');

class Pinger{
    private $GOOGLE_PING_URL="http://blogsearch.google.com/ping/RPC2";
    private $GOOGLE_METHOD_NAME="weblogUpdates.extendedPing";
    function RPC($URL, $methodName){
        $parse = parse_url($URL);
        if (!isset($parse['host'])) return false;
        $host = $parse['host'];
        $port = isset($parse['port'])?$parse['port']:80;
        $uri  = isset($parse['path'])?$parse['path']:'/';

        $fp=fsockopen($host,$port,$errno,$errstr);
        if (!$fp)
        {
            return array(-1,"Cannot open connection: $errstr ($errno)<br />\n");
        }


        $data = "<?xml version=\"1.0\"?>\r\n".
            "  <methodCall>\r\n".
            "    <methodName>$methodName</methodName>\r\n".
            "    <params>\r\n";
        for($i = 2 ; $i < func_num_args(); $i++){
            $data .= "      <param>\r\n".
                "        <value>".htmlspecialchars(func_get_arg($i))."</value>\r\n".
                "      </param>\r\n";
        }

        $data .="    </params>\r\n".
            "  </methodCall>";

        $len  = strlen($data);
        $out  = "POST $uri HTTP/1.0\r\n";
        $out .= "User-Agent: Joomla! Ping/1.0\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-Type: text/xml\r\n";
        $out .= "Content-length: $len\r\n\r\n";
        $out .= $data;

        fwrite($fp, $out);
        $response = '';
        while(!feof($fp)){
            $response.=fgets($fp, 128);
        }
        fclose($fp);
        $lines=explode("\r\n",$response);
        $firstline=$lines[0];
        if (!ereg("HTTP/1.[01] 200 OK",$firstline))
        {
            return array(-1,$firstline);
        }
        while($lines[0]!='') array_shift($lines);
        array_shift($lines);
        $lines=strip_tags(implode(' ',$lines));

        $n=preg_match(
            '|<member>\s*<name>flerror</name>\s*<value>\s*<boolean>([^<]*)</boolean>\s*</value>\s*</member>|i',
            $response, $matches);
        if (0==$n)
        {
            return array(-1,$lines);
        }
        $flerror=$matches[1];

        $n=preg_match(
            '|<member>\s*<name>message</name>\s*<value>([^<]*)</value>\s*</member>|i',
            $response, $matches);
        if (0==$n)
        {
            return array(-1,$lines);
        }
        $message=$matches[1];
        return array($flerror,$message);
    }

    function pingGoogle($siteName, $siteUrl, $pageUrl, $rssUrl){
        $this->RPC($this->GOOGLE_PING_URL, $this->GOOGLE_METHOD_NAME,$siteName, $siteUrl, $pageUrl, $rssUrl);
    }
}
