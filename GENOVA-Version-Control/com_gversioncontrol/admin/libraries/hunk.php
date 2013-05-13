<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
 
// ensure a valid entry point
defined('_JEXEC') or die('Restricted Access');

/**
 * Hunk class represents a hunk of a diff of two texts
 *
 */
class Hunk extends JObject{
	public $startTo;  		// start line of first text
	public $endTo;    		// end line of first text
	public $rangeTo;  		// range between start and end line

	public $startFrom;		// start line of second text
	public $endFrom;		// end line of second text
	public $rangeFrom;		// range between start and end line

	public $action;		// action of hunk: add / delete / change

	public $strTo;			// array content of first text 
	public $strFrom;		// array content of second text

	/**
	 * Constructor
	 *
	 * @return	object		
	 */
	public function __construct() {
		$this->strTo = array();
		$this->strFrom = array();
	}

	/**
	 * Set attributes of hunk object
	 * 
	 * Set attributes of hunk object
	 *
	 * @param 	string		add, change or delete
	 * @param 	int			start line from first file
	 * @param 	int			end line from first file
	 * @param 	int			start line from second file
	 * @param 	int			end line from second file
	 * @return	void
	 */
	public function setHunkDetails($action, $startTo, $endTo, $startFrom, $endFrom) 
	{
		$this->action = $action;
		$this->startTo = $startTo;
		$this->startFrom = $startFrom;
		$this->endTo = $endTo;
		$this->endFrom = $endFrom;
		
		$this->getRangeTo();
		$this->getRangeFrom();
	}

	/**
	 * Compute range between start and end line from first text
	 * 
	 * @return	int
	 */
	public function getRangeTo()
	{
		// check how many lines of diffs
		if(empty($this->endTo)) {
			$this->rangeTo = 1;
		}
		else {
			$this->rangeTo = (int)($this->endTo) - (int)($this->startTo) + 1;
		}
		return $this->rangeTo;
	}
	
	/**
	 * Compute range between start and end line from second text
	 * 
	 * @return	int
	 */	
	public function getRangeFrom()
	{
		// check how many lines of diffs
		if(empty($this->endFrom)) {
			$this->rangeFrom = 1;
		}
		else {
			$this->rangeFrom = (int)($this->endFrom) - (int)($this->startFrom) + 1;
		}
		return $this->rangeFrom;
	}
	
	/**
	 * Set strTo variable
	 * 
	 * @param 	string		StrTo value
	 * @return	void
	 */
	public function setStrTo($value) 
	{
		$this->strTo = $value;
	}

	/**
	 * Set strFrom variable
	 *
	 * @param 	string		StrFrom value 
	 * @return	void
	 */
	public function setStrFrom($value) 
	{
		$this->strFrom = $value;
	}
	
	/**
	 * Get strTo value
	 *
	 * @return	string		
	 */
	public function getStrTo()
	{
		return $this->strTo;		
	}
	
	/**
	 * Get strFrom value
	 *
	 * @return	string		
	 */
	public function getStrFrom()
	{
		return $this->strFrom;
	}
	
	/**
	 * Get the next line number to be evaluated
	 * 
	 * @return	int
	 */
	public function getLine()
	{
		switch ($this->action)
		{
			case 'add':
				return $this->startFrom + 1;
				break;
			case 'delete':
				return $this->startFrom;
				break;
			case 'change':
				return $this->startFrom;
				break;
			default:
				break;		
		}
		
	}
	
	/**
	 * Get action of hunk
	 *
	 * @return	string		
	 */
	public function getAction() 
	{
		return $this->action;
	}
	
	/**
	 * Set the action of the hunk
	 *
	 * @param 	string		add, change or delete
	 * @return	void		
	 */
	public function setAction($action) 
	{
		$this->action = $action;
	}
	
	/**
	 * Create Normal Format diff for first text range
	 *
	 * @return	string		
	 */
	public function toStrintRangeTo() 
	{
		if ($this->getRangeTo() == 1) {
			return $this->startTo;
		}
		else {
			return $this->startTo.",".$this->endTo;
		}
	}

	/**
	 * Create Normal Format diff for second text range
	 *
	 * @return	string		
	 */
	public function toStrintRangeFrom()
	{
		if ($this->getRangeFrom() == 1) {
			return $this->startFrom;
		}
		else {
			return $this->startFrom.",".$this->endFrom;
		}		
	}

	/**
	 * Create Normal Format diff for first text
	 *
	 * @return	string		
	 */
	public function toStringStrTo() 
	{
		$result = "";
		foreach ($this->strTo as $line) {
			$result .= "> ".$line."\n";
		}
		return $result;
	}

	/**
	 * Create Normal Format diff for second text
	 *
	 * @return	string		
	 */
	public function toStringStrFrom() 
	{
		$result = "";
		
		foreach ($this->strFrom as $line) {
			$result .= "< ".$line."\n";
		}
		return $result;		
	}
	
	/**
	 * Create Normal Format diff for this hunk
	 *
	 * @return	string		
	 */
	public function toString() {
		$result = "";
		switch ($this->action)
		{
			case 'add':
				$result .= $this->toStrintRangeFrom()."a".$this->toStrintRangeTo()."\n";  
				$result .= $this->toStringStrTo();
				break;
			case 'delete':
				$result .= $this->toStrintRangeFrom()."d".$this->toStrintRangeTo()."\n";  
				$result .= $this->toStringStrFrom();
				break;
			case 'change':
				$result .= $this->toStrintRangeFrom()."c".$this->toStrintRangeTo()."\n";
				$result .= $this->toStringStrFrom();
				$result .= "---\n";
				$result .= $this->toStringStrTo();
				break;
		}
		return $result;
	}

}