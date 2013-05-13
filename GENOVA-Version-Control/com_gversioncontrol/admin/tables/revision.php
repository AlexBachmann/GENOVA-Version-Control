<?php
/**
 * @package GENOVA Version Control
 * @author Alexander Bachmann
 * @copyright (C) 2013 - Alexander Bachmann
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' ); 

class GVersionControlTableRevision extends JTable {
	public function __construct($db){
		parent::__construct('#__gvc_revisions', 'id', $db);
	}
	public function check(){
		if(!$this->time){
			$this->time = JFactory::getDate();
		}
		if($this->time instanceof JDate){
			$this->time = $this->time->toMySQL();
		}
		if(!$this->item_id) return false;
		if(!$this->context) return false;
		if(!$this->user){
			$this->user = JFactory::getUser();
		}
		if($this->user instanceof JUser){
			$this->user = $this->user->id;
		}
		
		if(!is_string($this->payload)){
			$this->payload = serialize($this->payload);
		}
		$this->length = $this->getPayloadSize($this->payload);
		if(!$this->payload_hash){
			$this->payload_hash = $this->getPayloadHash($this->payload);
		}
		return true;
	}
	public function load($keys = null, $reset = true ){
		if(parent::load($keys, $reset)){
			$this->user = JFactory::getUser($this->user);
			$this->time = JFactory::getDate($this->time);
			$this->payload = unserialize($this->payload);
			return true;
		}
		return false;
	}
	public function save($src = null, $orderingFilter = '', $ignore = ''){
		if(!$this->check()) return false;
		if(!$this->store()) return false;
		return true;
	}
	public function getPayloadHash($payload){
		if(!is_string($payload)){
			$payload = serialize($payload);
		}
		return sha1($payload);
	}
	protected function getPayloadSize($payload){
		switch(is_string($payload)){
			case true: 
				$payload_str = $payload;
				$payload = unserialize($payload);
				break;
			default:
				$payload_str = serialize($payload);
		}
		$empty_payload = clone($payload);

		$vars = array_keys(get_object_vars($empty_payload));
		foreach($vars as $var){
			$empty_payload->$var = '';
		}
		return strlen($payload_str) - strlen(serialize($empty_payload));
	}
}