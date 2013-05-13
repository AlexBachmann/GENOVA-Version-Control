<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
 
// ensure a valid entry point
defined('_JEXEC') or die('Restricted Access');

require_once JPATH_ADMINISTRATOR.'/components/com_gversioncontrol/libraries/hunk.php';

/**
 * Diff class represents a diff file of two texts
 *
 */
class Diff extends JObject{
	public $_hunk_list;	// list of hunks of a diff 
	public $_i;			// current hunk index
	
	/**
	 * Contructor
	 *
	 * @return	object		
	 */
	public function __construct() 
	{
		// initialize an array that contains the diff nodes
		$this->_hunk_list = array();
		$this->_i = 0;
	}

	/**
	 * Get the next hunk
	 *
	 * @return	object		
	 */
	public function getNextHunk()
	{
		if (isset($this->_hunk_list[$this->_i])) 
		{
			return $this->_hunk_list[$this->_i++];
		}
		return null;
	}
	
	/**
	 * Create a new hunk with add action
	 *
	 * Create a new hunk with add action and insert into object
	 * 
	 * @param 	int			Start line number from first text
	 * @param 	int			End line number from first text
	 * @param 	int			Start line number from second text
	 * @param 	int			End line number from second text
	 * @param 	string		Text
	 * @param 	int			Start line of content to insert to new hunk
	 * @return	object
	 */
	public function insertDiffAdd($startFrom, $endFrom, $startTo, $endTo, &$str, $i)
	{
		// initialization
		$value = array();

		$hunk = new Hunk();
		$hunk->setHunkDetails("add", $startTo, $endTo, $startFrom, $endFrom);
		$step = $hunk->getRangeTo();
		
		// get the diff content
		for ($j=1; $j<=$step; $j++) {
			$value[] = substr($str[$j+$i], 2);
		}
		$hunk->setStrTo($value);
		
		// add to this object
		$this->_hunk_list[] = $hunk;
		
		return $step + 1;
	}

	/**
	 * Create a new hunk with delete action
	 *
	 * Create a new hunk with delete action and insert into object
	 * 
	 * @param 	int			Start line number from first text
	 * @param 	int			End line number from first text
	 * @param 	int			Start line number from second text
	 * @param 	int			End line number from second text
	 * @param 	string		Text
	 * @param 	int			Start line of content to insert to new hunk
	 * @return	object
	 */
	public function insertDiffDelete($startFrom, $endFrom, $startTo, $endTo, &$str, $i)
	{
		// initialization
		$value = array();
		
		$hunk = new Hunk();
		$hunk->setHunkDetails("delete", $startTo, $endTo, $startFrom, $endFrom);
		$step = $hunk->getRangeFrom();
				
		// get the diff content
		for ($j=1; $j<=$step; $j++) {
			$value[] = substr($str[$j+$i], 2);
		}
		
		$hunk->setStrFrom($value);
			
		// add to this object
		$this->_hunk_list[] = $hunk;
		return $step + 1;
	}
	
	/**
	 * Create a new hunk with change action
	 *
	 * Create a new hunk with change action and insert into object
	 * 
	 * @param 	int			Start line number from first text
	 * @param 	int			End line number from first text
	 * @param 	int			Start line number from second text
	 * @param 	int			End line number from second text
	 * @param 	string		Text
	 * @param 	int			Start line of content to insert to new hunk
	 * @return	object
	 */
	public function insertDiffChange($startFrom, $endFrom, $startTo, $endTo, &$str, $i)
	{
		// initialization
		$value = array();

		$hunk = new Hunk();
		$hunk->setHunkDetails("change", $startTo, $endTo, $startFrom, $endFrom);
		$stepDel = $hunk->getRangeFrom();
		$stepAdd = $hunk->getRangeTo();
		
		// get the diff content
		for ($j=1; $j<=$stepDel; $j++) {
			$valueDel[] = substr($str[$j+$i], 2);
		}
	
		// get the diff content
		for ($j=1; $j<=$stepAdd; $j++) {
			$valueAdd[] = substr($str[$j+$i+$stepDel+1], 2);
		}
		
		$hunk->setStrFrom($valueDel);
		$hunk->setStrTo($valueAdd);
		
		// add to this object
		$this->_hunk_list[] = $hunk;	
		return $stepDel + $stepAdd + 2;
	}
	
	/**
	 * Create a new diff object from a diff in Normal Format
	 *
	 * @param 	string		Diff
	 * @return	object
	 */
	public function parseDiff($diff) {
		$diffArray = array();
		
		// Start parse diff string
		$diffStr = explode("\n",rtrim($diff));
		$i = 0;
		$imax = sizeof($diffStr);

		while (!empty($diffStr[$i]) && $i < $imax) {
			$line = $diffStr[$i];

			// regular expression to recognize Normal Format Diff
			preg_match("/([0-9]*)(,([0-9]*))?([acd])([0-9]*)(,([0-9]*))?/", $line, $regs);
			$delta = 0;
			
			// recover the action
			$action = $regs[4];
			
			//If only onle line has changed, the end values are also the starting values (Dff format does show that)
			if(!$regs[3]) $regs[3] = $regs[1];
			if(!isset($regs[7]) || !$regs[7]) $regs[7] = $regs[5];
			
			switch ($action)
			{
				// add
				case 'a':
					$next = $this->insertDiffAdd($regs[1], $regs[3], $regs[5], $regs[7], $diffStr, $i);
					break;
				
				// delete
				case 'd':
					$next = $this->insertDiffDelete($regs[1], $regs[3], $regs[5], $regs[7],  $diffStr, $i);
					break;
				
				// change
				case 'c':
					$next = $this->insertDiffChange($regs[1], $regs[3], $regs[5], $regs[7], $diffStr, $i);
					break;
					
				default:
					// error
					$next = 1;
					break;
			}
			$i = $i + $next;
		}
		return $this;
	}
	
	/**
	 * Insert a hunk into object
	 *
	 * @param 	object		hunk
	 * @return	void
	 */	
	public function insertHunk($hunk) {
		$this->_hunk_list[] = $hunk;
	}

	/**
	 * Converts all hunks objects to string
	 *
	 * @return	string
	 */	
	public function toString()
	{
		$result = "";
		foreach ($this->_hunk_list as $hunk)
		{
			$result .= $hunk->toString();
		}
		return $result;
	}
}
