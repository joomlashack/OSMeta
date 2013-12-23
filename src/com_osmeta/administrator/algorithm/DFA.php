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

// No direct access
defined('_JEXEC') or die('Restricted access');

class DFA{
	private $state=1;
	private $buffer;
	private $currentPos;
	private $result;

	private $keywords = array();
	private $omitTags = array('TITLE', 'STRONG', 'B');
	private $encoding = null;
	private $statesGraph = array(
	1=>array(
	   '<'=>array(2, 'startSpecial'),
	   'DEFAULT'=>1
	),
	2=>array(
	   '!'=>3,
	   'S'=>'S1',
	   's'=>'S1',
	   'ALPHA'=>array(4, 'startTagName'),
	   '/'=>10,
	   'DEFAULT'=>1
	),
	3=>array(
	   'ALPHA'=>5,
	   '-'=>6,
	   'DEFAULT'=>1
	),
	4=>array(
	   'ALPHANUM'=>4,
	   ':'=>4,
	   'SPACE'=>array(11, 'endTagName'),
	   '>'=>array(1, array('endTagName', 'tag', 'endSpecial')),
	   '/'=>16,
	   'DEFAULT'=>1
	),
	5=>array(
	  '>'=>array(1,'endSpecial'),
	   'DEFAULT'=>5
	),
	6=>array(
	   '-'=>7,
	   'DEFAULT'=>1
	),
	7=>array(
	   '-'=>8,
	   'DEFAULT'=>7
	),
	8=>array(
	   '-'=>9,
	   'DEFAULT'=>7
	),
	9=>array(
	   '>'=>array(1, 'endSpecial'),
	   'DEFAULT'=>7
	),
	10=>array(
	   'ALPHA'=>array(13, 'startTagName'),
	   'SPACE'=>14,
	   'DEFAULT'=>1
	),
	11=>array(
	   '>'=>array(1, array('tag', 'endSpecial')),
	   '/'=>16,
	   'DEFAULT'=>11
	),

	13=>array(
	   'ALPHANUM'=>13,
	   '>'=>array(1, array('endTagName', 'tagEnd', 'endSpecial')),
	   'SPACE'=>array(15, 'endTagName'),
	   'DEFAULT'=>1
	),

	14=>array(
	   'SPACE'=>14,
	   'ALPHA'=>array(13, 'startTagName'),
	   'DEFAULT'=>1
	),
	15=>array(
	   '>'=>array(1, array('tagEnd', 'endSpecial')),
	   'DEFAULT'=>1
	),
	16=>array(
	   '>'=>array(1, 'endSpecial'),
	   'SPACE'=>17,
	   'DEFAULT'=>11
	),
	17=>array(
	   'SPACE'=>17,
	   '>'=>array(1, 'endSpecial'),
	   'DEFAULT'=>11
	),

	'S1'=>array(
	   'C'=>'S2',
	   'c'=>'S2',
	   'ALPHANUM'=>4,
	   'DEFAULT'=>1
	),

    'S2'=>array(
       'R'=>'S3',
       'r'=>'S3',
       'ALPHANUM'=>4,
       'DEFAULT'=>1
   ),

    'S3'=>array(
       'I'=>'S4',
       'i'=>'S4',
       'ALPHANUM'=>4,
       'DEFAULT'=>1
   ),

    'S4'=>array(
       'P'=>'S5',
       'p'=>'S5',
       'ALPHANUM'=>4,
       'DEFAULT'=>1
   ),

    'S5'=>array(
       'T'=>'S6',
       't'=>'S6',
       'ALPHANUM'=>4,
       'DEFAULT'=>1
   ),

    'S6'=>array(
       'SPACE'=>'S7',
       '>'=>'S8',
       'ALPHANUM'=>4,
       'DEFAULT'=>1
   ),

    'S7'=>array(
       '>'=>'S8',
       'DEFAULT'=>'S7'
   ),

    'S8'=>array(
       '<'=>'S9',
       'DEFAULT'=>'S8'
   ),

    'S9'=>array(
       '/'=>'S10',
       'SPACE'=>'S17',
       'DEFAULT'=>'S8'
   ),

    'S10'=>array(
       'S'=>'S11',
        's'=>'S11',
       'SPACE'=>'S18',
       'DEFAULT'=>'S8'
   ),
    'S11'=>array(
       'C'=>'S12',
        'c'=>'S12',
       'DEFAULT'=>'S8'
   ),
    'S12'=>array(
       'R'=>'S13',
        'r'=>'S13',
       'DEFAULT'=>'S8'
   ),
    'S13'=>array(
       'I'=>'S14',
        'i'=>'S14',
       'DEFAULT'=>'S8'
   ),
    'S14'=>array(
       'P'=>'S15',
        'p'=>'S15',
       'DEFAULT'=>'S8'
   ),
    'S15'=>array(
       'T'=>'S16',
        't'=>'S16',
       'DEFAULT'=>'S8'
   ),
    'S16'=>array(
       '>'=>array(1, 'endSpecial'),
        'SPACE'=>'S19',
       'DEFAULT'=>'S8'
   ),
    'S17'=>array(
       '/'=>'S10',
        'SPACE'=>'S17',
       'DEFAULT'=>'S8'
   ),
    'S18'=>array(
       'S'=>'S11',
    's'=>'S11',
        'SPACE'=>'S18',
       'DEFAULT'=>'S8'
   ),
    'S19'=>array(
       '>'=>array(1, 'endSpecial'),
       'DEFAULT'=>'S19'
   )


	);

	private $hilight_tag = "strong";
	private $hilight_class = "strong";

	public function hilight($str,
	   $keywords = array() ,
	   $omitTags = array(),
	   $hilight_tag="strong",
	   $hilight_class="",
	   $encoding=null){
		$this->keywords = array();
		foreach($keywords as $keyword){
                  $keyword = trim($keyword);
                  $this->keywords[] = array(
                    'keyword'=>$keyword,
                    'length'=>$encoding?mb_strlen($keyword, $encoding):strlen($keyword),
                    'upper'=>$encoding?mb_strtoupper($keyword, $encoding):strtoupper($keyword)
                 );
		}
		foreach($omitTags as $key=>$value){
		    $omitTags[$key] = strtoupper($value);
		}
		$this->omitTags = array_merge($this->omitTags, $omitTags);
		$this->encoding = $encoding;
		$this->buffer = $str;
		$this->currentPos = 0;
		$this->state=1;

		$this->hilight_tag = $hilight_tag;
		$this->hilight_class = $hilight_class;

		$this->startPos = 0;
        $this->sectionStart = 0;
        $this->sectionEnd = 0;

        $this->result = "";
        $chars = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
		foreach($chars as $char){
                  $this->nextState($char);
		}
		return $this->result;
	}

	private function nextState($char){
		foreach($this->statesGraph[$this->state] as $token=>$action){
			if ($token == $char ||
			($token=='ALPHA' && ctype_alpha($char)) ||
			($token=='ALPHANUM' && ctype_alnum($char)) ||
			($token=='SPACE' && ctype_space($char)) ||
			$token == 'DEFAULT'){
				$this->state = is_array($action)?$action[0]:$action;
				if (is_array($action) && isset($action[1])){
					if (is_array($action[1])){
						foreach($action[1] as $functionName){
							eval('$this->'.$functionName.'();');
						}
					}else{
						eval('$this->'.$action[1].'();');
					}
				}
			break;
			}
		}
		$this->currentPos++;
	}

	private $startPos = 0;
	private $sectionStart = 0;
	private $sectionEnd = 0;

	private function startSpecial(){
		$this->sectionStart = $this->currentPos;
	}
    private function endSpecial(){
        $this->sectionEnd = $this->currentPos+1;
        $replaceContent = $this->encoding?
            mb_substr($this->buffer, $this->startPos, $this->sectionStart - $this->startPos, $this->encoding):
            substr($this->buffer, $this->startPos, $this->sectionStart - $this->startPos);
        $this->result .= $this->performReplace($replaceContent).
           ($this->encoding?mb_substr($this->buffer, $this->sectionStart , $this->sectionEnd  - $this->sectionStart, $this->encoding):
                            substr($this->buffer, $this->sectionStart , $this->sectionEnd  - $this->sectionStart));
        $this->startPos = $this->sectionEnd;
    }

  private function performReplace($content){
          $origContent =  $content;
    	if (in_array($this->closedTag, $this->omitTags)){
    		return $content;
    	}
      $keywordsToReplace = array();

          foreach($this->keywords as $keywordData){
            $keyword = $keywordData['keyword'];
            $keywordLength = $keywordData['length'];
            $upperKeyword = $keywordData['upper'];
    	   $upper = $this->encoding?$content:strtoupper($content);
    	   $upperKeyword = $this->encoding?$keyword:strtoupper($keyword);
    	   $keywordsPositions = array();
    	   $currentPosition = 0;
    	   $searchPositions = array();
    	   while(($currentPosition = $this->encoding?@mb_strpos($upper, $upperKeyword, $currentPosition, $this->encoding):
    	      @strpos($upper, $upperKeyword, $currentPosition))!==false){
           $keywordsToReplace[] = array($keyword, $currentPosition,  $currentPosition + $keywordLength);
    	    $currentPosition+=$keywordLength;
    	   }
    	   //$searchPositions = array_reverse($searchPositions);
    	   //foreach($searchPositions as $currentPosition){
    	   //	$content = $this->encoding? (mb_substr($content, 0, $currentPosition).'<'.$this->hilight_tag.(empty($this->hilight_class)?"":' class="'.$this->hilight_class.'"').'>'.mb_substr($content, $currentPosition, mb_strlen($keyword)).'</'.$this->hilight_tag.'>'.
    	   //	mb_substr($content, $currentPosition+ mb_strlen($keyword))):
    	   //	   (substr($content, 0, $currentPosition).'<'.$this->hilight_tag.(empty($this->hilight_class)?"":' class="'.$this->hilight_class.'"').'>'.substr($content, $currentPosition, strlen($keyword)).'</'.$this->hilight_tag.'>'.
         //   substr($content, $currentPosition+ strlen($keyword)));
    	   //}
    	}


      usort($keywordsToReplace, "DFA::dfa_cmp");
      $keywordsToReplaceFiltered = array();
      $pos = 0;
      while($pos < count($keywordsToReplace)){
        while($pos+1 < count($keywordsToReplace) && $keywordsToReplace[$pos][1] == $keywordsToReplace[$pos+1][1]){
          $pos++;
        }
        $startPosition = $keywordsToReplace[$pos][1];
        $endPosition = $keywordsToReplace[$pos][2];
        while($pos+1 < count($keywordsToReplace) && $keywordsToReplace[$pos+1][1] < $endPosition){
          if ($keywordsToReplace[$pos+1][2] > $endPosition){
            $endPosition = $keywordsToReplace[$pos+1][2];
          }
          $pos++;
        }
        $keywordsToReplaceFiltered[] = array($startPosition, $endPosition);
        $pos++;
      }
      $searchPositions = array_reverse($keywordsToReplaceFiltered);
      foreach($searchPositions as $currentPosition){
      	$content = $this->encoding? (mb_substr($content, 0, $currentPosition[0],$this->encoding).'<'.$this->hilight_tag.(empty($this->hilight_class)?"":' class="'.$this->hilight_class.'"').'>'.mb_substr($content, $currentPosition[0], $currentPosition[1] - $currentPosition[0], $this->encoding).'</'.$this->hilight_tag.'>'.
        	mb_substr($content, $currentPosition[1], null, $this->encoding)):
      	   (substr($content, 0, $currentPosition[0]).'<'.$this->hilight_tag.(empty($this->hilight_class)?"":' class="'.$this->hilight_class.'"').'>'.substr($content, $currentPosition[0], $currentPosition[1] - $currentPosition[0]).'</'.$this->hilight_tag.'>'.
         substr($content, $currentPosition[1]));
      }
      if ($origContent !== $content){
      //  print $origContent." - ".$content."\n";
      }
    	return $content;
    }

    public static function dfa_cmp($a, $b)
    {
      if ($a[1] == $b[1]) {
        if ($a[2]==$b[2]){
          return 0;
        }
        return ($a[2] < $b[2]) ? -1 : 1;
      }
      return ($a[1] < $b[1]) ? -1 : 1;
    }

    private $tagNameStartPosition = 0;
    private $tagName = '';

    private $currentTag = '';
    private $closedTag = '';

    private function startTagName(){
    	$this->tagNameStartPosition = $this->currentPos;
    }

    private function endTagName(){
        $this->tagName = $this->encoding?mb_substr($this->buffer, $this->tagNameStartPosition, $this->currentPos - $this->tagNameStartPosition, $this->encoding):
        substr($this->buffer, $this->tagNameStartPosition, $this->currentPos - $this->tagNameStartPosition);
    }

    private function tag(){
     $this->currentTag = strtoupper($this->tagName);
    }

    private function tagEnd(){
     $this->currentTag = "";
     $this->closedTag = strtoupper($this->tagName);
    }
}
