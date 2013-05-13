<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 
jimport('joomla.application.component.model');
require_once JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/libraries/diff.php';
require_once JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/libraries/hunk.php';

class GVersionControlModelCompare extends JModel {
	protected $oldRevisionText = null;
	protected $diffRevisionText = null;
	protected $oldRevisionLines = null;
	protected $diffRevisionLines = null;
	protected $diff = null;
	protected $diffText = null;
	protected $diffBlocks = null;
	
	public function __construct($config){
		if(empty($config['filter_fields'])){
			$config['filter_fields'] = array(
					'id',
					'title',
					'catid',
					'state',
					'access_level',
					'created',
					'user_name',
					'category_title',
					'language'
			);
		}
		parent::__construct($config);
		if(isset($config['oldRevision'])){
			$this->setOldRevision($config['oldRevision']);	
		}
		if(isset($config['diffRevision'])){
			$this->setDiffRevision($config['diffRevision']);
		}
	}
	public function setOldRevisionText($text){
		$this->oldRevisionText = $text;
	}
	public function setDiffRevisionText($text){
		$this->diffRevisionText = $text;
	}
	public function getOldRevisionText(){
		if(is_null($this->oldRevisionText)){
			throw new RuntimeException(JText::_('COM_VERSIONCONTROL_EXCEPTION_PROPERTY_NOT_SET'));
		}
		return $this->oldRevisionText;
	}
	public function getDiffRevisionText(){
		if(is_null($this->diffRevisionText)){
			throw new RuntimeException(JText::_('COM_VERSIONCONTROL_EXCEPTION_PROPERTY_NOT_SET'));
		}
		return $this->diffRevisionText;
	}
	/**
	 * Splits an HTML text along certain tags
	 * 
	 * This method takes an html text an returns an array
	 * of html blocks, split along certain html tags
	 * @param string $html
	 * @return array HTML blocks
	 */
	public static function htmlLines($html){
		$i = 0;
		$htmlLen = strlen($html);
		$buffer = "";
		$tag = "";
		$result = array();
		$tagType = null;
		$tagStart = array("p", "h1", "h2", "h3", "h4", "h5", "h6", "div", "table", "tr", "ul", "li", "blockquote");
		$tagEnd = array("/p", "/h1", "/h2", "/h3", "/h4", "/h5", "/h6", "/div", "/table", "/tr", "/ul", "/li", "/blockquote", "br","hr");
		while ($i<$htmlLen) {
			if (in_array($tagType, $tagEnd)){
				$buffer = trim($buffer);
				if (!empty($buffer)) {
					$result[] = $buffer;
				}
				$buffer = "";
			}
		
			while($i<$htmlLen && $html[$i]!='<')
			{
				$buffer = $buffer.$html[$i];
				$i++;
			}
			$i++;
			while($i<$htmlLen && $html[$i]!='>')
			{
				$tag = $tag.$html[$i];
				$i++;
			}
			$i++;
		
		
			// if not reach the end
			if ($i<=$htmlLen) {
				$tagType = self::htmlTagType($tag);
					
				if (in_array($tagType, $tagStart))
				{
					$buffer = trim($buffer);
					if (!empty($buffer)) {
						$result[] = $buffer;
					}
					$buffer = "<".$tag.">";
				} else {
					$buffer = $buffer . "<".$tag.">";
				}
			}
		
			$tag = "";
		}
		$buffer = trim($buffer);
		if (!empty($buffer)) {
			$result[] = $buffer;
		}
		return $result;
	}
	public function getOldRevisionLines(){
		if(!$this->oldRevisionLines){
			$this->oldRevisionLines = $this->htmlLines($this->getOldRevisionText());
		}
		return $this->oldRevisionLines;
	}
	public function getDiffRevisionLines(){
		if(!$this->diffRevisionLines){
			$this->diffRevisionLines = $this->htmlLines($this->getDiffRevisionText());
		}
		return $this->diffRevisionLines;
	}
	/**
	 * Return the Longest Commen Subsequence of two texts
	 * 
	 * To find out, how this works read: http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
	 * 
	 * @param array $x First Text, either string or an array, where the 
	 * the text is split along the lines. Each array element represents
	 * one line of the text
	 * @param array $y Second text, same format as $x
	 * @return array An array withe the LCS-Length Table, and the LCS vector table
	 */
	public static function getLCS($x, $y){
		$m = sizeof( $x ); // number of rows in text $x
		$n = sizeof( $y ); // number of rows in text $y
		
		$c = array(); // This will contain the LCS Length table http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
		$b = array(); // This will contain the backtrace vector table http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
		
		// init
		for ($i=1; $i <= $m; $i++) {
			$c[$i][0] = 0;
			$b[$i][0] = 0;
		}
		for ($j=0; $j <= $n; $j++) {
			$c[0][$j] = 0;
			$b[0][$j] = 0;
		}
		
		// build table
		for ( $i=1; $i<=$m; $i++ ) {
			for ( $j=1; $j<=$n; $j++ ) {
				if ($x[$i-1] == $y[$j-1]) {
					$c[$i][$j] = $c[$i-1][$j-1] + 1;
					$b[$i][$j] = 0;  // \      Vector direction top-left
				} else {
					if ($c[$i-1][$j] >= $c[$i][$j-1]) {
						$c[$i][$j] = $c[$i-1][$j];
						$b[$i][$j] = 1; // |   Vector direction top
					} else {
						$c[$i][$j] = $c[$i][$j-1];
						$b[$i][$j] = 2; // <-  Vector directon left
					}
				}
			}
		}
		return array('lenght' => $c, 'vectors' => $b);
	}
	public function getDiff(){
		if($this->diff === null){
			$x = $this->getOldRevisionLines();
			$y = $this->getDiffRevisionLines();
				
			// get the vectors table
			$lcs = $this->getLCS($x, $y);
			$b = $lcs['vectors'];
				
			//Size of content + 1
			//We add plus one the the size of the content, because
			//we need the getDiff algorthm to do an extra turn at the
			//end to clean up the remaining buffers of the last recursive loop
			$m = sizeof( $x ) +1;
			$n = sizeof( $y ) +1;
				
			// temporary variables used to compute diff
			$buffer_add = array();
			$buffer_del = array();
				
			// Return an object with a list of hunks
			$diff = $this->createDiff( $b, $x, $y, $m, $n, $buffer_del, $buffer_add );
				
				
			// string in to Normal Format Diff
			if (isset( $diff )) {
				$this->diff = $diff;
			}
			else {
				$this->diff =  null;
			}
		}
		return $this->diff;
	}
	/**
	 * Gets the Diff object for the two texts and returns
	 * a the changes in diff format
	 * 
	 * @return string Changes between the texts in Diff format
	 */
	public function getDiffToString(){
		if(!$this->diffText){
			$diff = $this->getDiff();
	
			// string in to Normal Format Diff
			if ($diff) {
				$this->diffString = $diff->toString();
			}
			else {
				$this->diffString =  "";
			}
		}
		return $this->diffString;
	}

	/**
	 * Recursive algorithm returning the Diff object 
	 * 
	 * For a detailed description of this algorith read the following 
	 * two articles:
	 * http://en.wikipedia.org/wiki/Longest_common_subsequence_problem
	 * http://en.wikipedia.org/wiki/Diff
	 * 
	 * @param array $vectors The LCS vectors
	 * @param array $x The first text as string of array of text blocks (e.g. lines of text)
	 * @param array $y The second text, formatted like $y
	 * @param integer $i LCS Table row
	 * @param integer $j LCS Table column 
	 * @param array $buffer_del Delete Buffer which remained from the previous recursive loop
	 * @param array $buffer_add Add Buffer which remained from the previous recursive loop
	 * @return Diff Diff Object or void, if there is no change
	 */
	protected function createDiff(&$vectors, &$x, &$y, $i, $j, &$buffer_del, &$buffer_add){
		if ( $i > 0 && $j == 0) 
		{
			$diff = $this->createDiff($vectors, $x, $y, $i-1, $j, $buffer_del, $buffer_add);
			$line = array("dst" => $i, "src" => $j, "content"=> $x[$i-1]);
			array_push($buffer_del,$line);
			return $diff;
		}
		else if ( $j > 0 && $i == 0) 
		{
			$diff = $this->createDiff($vectors, $x, $y, $i, $j-1, $buffer_del, $buffer_add);
			$line = array("dst" => $j, "src" => $i, "content"=> $y[$j-1]);
			array_push($buffer_add,$line);
			return $diff;
		} else if ($i==0 && $j ==0) { 
			return;
		}
		
		if (!isset($vectors[$i][$j]) || $vectors[$i][$j]  == 0)
		{
			$diff = $this->createDiff($vectors, $x, $y, $i-1, $j-1, $buffer_del, $buffer_add);
			$diff = $this->handleBuffers($diff, $buffer_del, $buffer_add);
			return $diff;
		
		} else if ($vectors[$i][$j] == 1)
		{
			$diff = $this->createDiff($vectors, $x, $y, $i-1, $j, $buffer_del, $buffer_add);
			$line = array("dst" => $i, "src" => $j, "content"=> $x[$i-1]);
			array_push($buffer_del,$line);
			return $diff;
		} else
		{
			$diff = $this->createDiff($vectors, $x, $y, $i, $j-1, $buffer_del, $buffer_add);
			$line = array("dst" => $j, "src" => $i, "content"=> $y[$j-1]);
			array_push($buffer_add,$line);
			return $diff;
		}
	}
	/**
	 * Converts Add and Delete Buffers into the corresponding Hunks
	 * and Adds them to the Diff object
	 * 
	 * If no diff object is passed, it creates it.
	 * @param Diff $diff
	 * @param array $buffer_del
	 * @param array $buffer_add
	 * @return Diff object containing the added and/or deleted lines
	 */
	protected function handleBuffers(&$diff = null, &$buffer_del = array(), &$buffer_add = array()){
		if (!empty($buffer_del) || !empty($buffer_add)) {
			$hunk = new Hunk();
			if (!empty($buffer_del) && !empty($buffer_add))
			{
				$xi = $buffer_del[0]["dst"];
				$xj = $buffer_del[sizeof($buffer_del)-1]["dst"];
				$yi = $buffer_add[0]["dst"];
				$yj = $buffer_add[sizeof($buffer_add)-1]["dst"];
		
				$hunk->setHunkDetails("change", $yi, $yj, $xi, $xj);
		
			}
			else if (!empty($buffer_del))
			{
				$xi = $buffer_del[0]["dst"] ;
				$xj = $buffer_del[sizeof($buffer_del)-1]["dst"];
				$yi = $buffer_del[0]["src"];
				$yj = null;
				$hunk->setHunkDetails("delete", $yi, $yj, $xi, $xj);
		
			} else if (!empty($buffer_add))
			{
				$xi = $buffer_add[0]["src"];
				$xj = null;
				$yi = $buffer_add[0]["dst"] ;
				$yj = $buffer_add[sizeof($buffer_add)-1]["dst"];
		
				$hunk->setHunkDetails("add", $yi, $yj, $xi, $xj);
			}
		
			$valueDel = array();
			foreach ($buffer_del as $row) {
				$valueDel[] = $row["content"];
			}
			$hunk->setStrFrom($valueDel);
		
			$buffer_del = array();
		
			$valueAdd = array();
			foreach ($buffer_add as $row) {
				$valueAdd[] = $row["content"];
			}
			$buffer_add = array();
			$hunk->setStrTo($valueAdd);
			if (!isset($diff))
				$diff = new Diff();
			$diff->insertHunk($hunk);
		}
		return $diff;
	}
	/**
	 * Get the name of html tag
	 *
	 * Find and get the html tag name from a given html tag
	 *
	 * @param 	string		html tag
	 * @return	string		tag name
	 */
	protected static function htmlTagType( $tag ){
		$i = 0;
		$tagName = "";
		$tagLen = strlen($tag);
		while ($i < $tagLen && !self::isAlphanum($tag[$i])) {
			if ($tag[$i] == '/') {
				$tagName = $tagName . '/';
			}
			$i++;
		}
		while ($i < $tagLen && self::isAlphanum($tag[$i])) {
			$tagName = $tagName . $tag[$i];
			$i++;
		}
		return $tagName;
	}
	/**
	 * Check if it is alpha numeric
	 *
	 * Given a string, check if it is alphanumeric
	 * using regular expressions
	 *
	 * @param 	string		string to check
	 * @return	bool
	 */
	protected static function isAlphanum( $str ){
		if (preg_match( '/[a-zA-Z0-9]/i', $str )) {
			return true;
		}
		return false;
	}
	public function getDiffBlocks(){
		if(!$this->diffBlocks){
			$lines = $this->getOldRevisionLines();
			$diff = $this->getDiff();
			$this->diffBlocks = self::createDiffBlocks($diff, $lines);
		}
		return $this->diffBlocks;
	}
	public static function createDiffBlocks(Diff $diff = null, $lines){
		$result = array();
		$ichange = 0;
		$iadd = 0;
		$idelete = 0;
		if(is_null($diff)){
			$buffer = array();
			foreach($lines as $line){
				$buffer[] = array('text' => $line);
			}
			if(!empty($buffer)){
				$result[] = array('type'=>'unmodified', 'from'=>$buffer, 'to'=>$buffer);
			}
		}else{
			$i = 0;
			$j = 0;
			$imax = sizeof($lines);
			
			$iTo = 0;
			$iFrom = 0;
			
			$bufferFrom = array();
			$bufferTo = array();
			
			while (($hunk = $diff->getNextHunk())!= null)
			{
				// reset buffer
				$bufferFrom = array();
				$bufferTo = array();
			
				$nextLine = $hunk->getLine() - 1;
				while ($i<$nextLine && $i < $imax) {
					$iTo++;
					$bufferTo[] = array('text'=>$lines[$i]);
			
					$iFrom++;
					$bufferFrom[] = array('text'=>$lines[$i]);
			
					$i++;
				}
				if(!empty($bufferFrom) || !empty($bufferTo)){
					$result[] = array('type'=>'unmodified', 'from'=>$bufferFrom, 'to'=>$bufferTo);
				}
				switch ($hunk->getAction())
				{
					case 'add':
						$iadd++;
						$bufferTo = array();
						$bufferFrom = array();
			
						foreach ($hunk->getStrTo() as $str) {
							$iTo++;
							$bufferTo[] = array('text'=>substr($str,0,-1).rtrim(substr($str,-1)));
						}
						$result[] = array('type'=>'add', 'from'=>$bufferFrom, 'to'=>$bufferTo);
						break;
					case 'delete':
						$idelete++;
						$bufferTo = array();
						$bufferFrom = array();
			
						$k = $i;
						foreach ($hunk->getStrFrom() as $str) {
							// remove correct line?
							if (strcmp(trim($str),trim($lines[$k]))!=0)
							{
								die ('error: delete content do no match');
							}
							$k++;
			
							$iFrom++;
							$bufferFrom[] = array('text'=>substr($str,0,-1).rtrim(substr($str,-1)));
						}
						$i += $hunk->getRangeFrom();
						$result[] = array('type'=>'delete', 'from'=>$bufferFrom, 'to'=>$bufferTo);
						break;
					case 'change':
						$ichange++;
						$bufferTo = array();
						$bufferFrom = array();
			
						$k = $i;
						foreach ($hunk->getStrFrom() as $str) {
							// remove correct line?
							if (strcmp(trim($str),trim($lines[$k]))!=0)
							{
								die ('error: delete content do no match');
							}
							$k++;
							$iFrom++;
							$bufferFrom[] =  array('text'=>substr($str,0,-1).rtrim(substr($str,-1)));
						}
			
						foreach ($hunk->getStrTo() as $str) {
							$iTo++;
							$bufferTo[] =  array('text'=>substr($str,0,-1).rtrim(substr($str,-1)));
						}
						$i += $hunk->getRangeFrom();
			
						$result[] = array('type'=>'change', 'from'=>$bufferFrom, 'to'=>$bufferTo);
						break;
					default:
						break;
				}
			}
			
			$bufferFrom = array();
			$bufferTo = array();
			
			while ($i<$imax) {
				$iTo++;
				$bufferTo[] = array('text'=>$lines[$i]);
				$iFrom++;
				$bufferFrom[] = array('text'=>$lines[$i]);
				$i++;
			}
			if(!empty($bufferFrom) || !empty($bufferTo)){
				$result[] = array('type'=>'unmodified', 'from'=>$bufferFrom, 'to'=>$bufferTo);
			}
			
		}
		$count = array(	'change'=>$ichange,
				'add'	=>$iadd,
				'delete'=>$idelete);
		
		return array('blocks' => $result, 'count' => $count);
	}
	/**
	 * Takes a Diff text and transforms it to a Diff Object
	 * 
	 * @param string $diff The Diff in text format
	 * @return Diff Diff Object
	 */
	public static function parseDiff($diff){
		$diff_obj = new Diff();
		$diff_obj->parseDiff($diff);
		return $diff_obj;
	}
	public static function applyDiffText($diff, $html, $format = 'array'){
		$diff = self::parseDiff($diff);
		return self::applyDiff($diff, $html, $format);
	}
	/**
	 * Applies a Diff object to a text, returning the new version
	 * @param Diff $diff The diff object containing the changes
	 * @param string/array $lines Text (either as an array of lines or as string)
	 * @param string $format The return (!!) format. Specifies if the new version is 
	 * returned as an array of lines or as a string
	 * @return string/array The new version, either as a string or an array
	 */
	public static function applyDiff(Diff $diff, $lines, $format = 'array'){
		if(!is_array($lines)){
			$lines = self::htmlLines($lines);
		}
		$result = array();
		$i = 0;
		$j = 0;
		$imax = sizeof($lines);
		
		while (($hunk = $diff->getNextHunk())!= null)
		{
			$nextLine = $hunk->getLine() - 1;
			while ($i<$nextLine && $i < $imax)
			{
				$result[] = $lines[$i];
				$i++;
			}
		
			switch ($hunk->getAction())
			{
				case 'add':
					foreach ($hunk->getStrTo() as $str) {
						$result[] = substr($str,0,-1).rtrim(substr($str,-1));// removes breakline
					}
					break;
				case 'delete':
					$k = $i;
					foreach ($hunk->getStrFrom() as $str)
					{
						if (strcmp(trim($str),trim($lines[$k]))!=0)
						{
							$flag = true;
						}
						$k++;
					}
					if ($flag)
					{
						die ('error: delete content do no match.');
					}
		
		
					$i += $hunk->getRangeFrom();
					break;
				case 'change':
		
					$k = $i;
					foreach ($hunk->getStrFrom() as $str)
					{
						if (strcmp(trim($str),trim($lines[$k]))!=0)
						{
							$flag = true;
						}
						$k++;
					}
					if ($flag) {
						die ('error: change content do no match.');
					}
		
		
					$i += $hunk->getRangeFrom();
		
					foreach ($hunk->getStrTo() as $str) {
						$result[] = substr($str,0,-1).rtrim(substr($str,-1));// removes breakline
					}
					break;
				default:
					break;
			}
		
		}
		while ($i<$imax) {
			$result[] =  $lines[$i++];
		}
		switch($format){
			case 'string':
				return implode('', $result);
				break;
			default:
				return $result;
				break;
		}
	}
}