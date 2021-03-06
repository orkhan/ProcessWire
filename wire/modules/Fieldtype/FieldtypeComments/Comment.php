<?php

/**
 * ProcessWire Fieldtype Comments > Comment
 *
 * Class that contains an individual comment.
 * 
 * ProcessWire 2.x 
 * Copyright (C) 2010 by Ryan Cramer 
 * Licensed under GNU/GPL v2, see LICENSE.TXT
 * 
 * http://www.processwire.com
 * http://www.ryancramer.com
 *
 */

class Comment extends WireData {

	/**	
	 * Status for Comment identified as spam
	 *
	 */
	const statusSpam = -2; 

	/**
	 * Status for Comment pending review
	 *
	 */
	const statusPending = 0; 

	/**
	 * Status for Comment that's been approved
	 *	
	 */
	const statusApproved = 1; 

	/**
	 * Max bytes that a Comment may use
	 *
	 */
	const maxCommentBytes = 20480; // 20k

	/**
	 * Previous Comment status, when it has been changed
	 *	
	 */ 
	protected $prevStatus; 

	/**	
	 * Construct a Comment and set defaults
	 *
	 */
	public function __construct() {
		$this->set('id', 0); 
		$this->set('text', ''); 
		$this->set('sort', 0); 
		$this->set('status', self::statusPending); 
		$this->set('created', time()); 
		$this->set('email', ''); 
		$this->set('cite', ''); 
		$this->set('ip', ''); 
		$this->set('user_agent', ''); 
		$this->set('created_users_id', $this->config->guestUserID); 
		$this->prevStatus = self::statusPending; 
	}

	public function get($key) {
		if($key == 'user' || $key == 'createdUser') {
			if(!$this->settings['created_users_id']) return $this->users->get($this->config->guestUserID); 
			return $this->users->get($this->settings['created_users_id']); 
		}
		return parent::get($key); 
	}

	public function set($key, $value) {

		if(in_array($key, array('id', 'status', 'pages_id', 'created', 'created_users_id'))) $value = (int) $value; 
			else if($key == 'text') $value = $this->cleanCommentString($value); 
			else if($key == 'cite') $value = str_replace(array("\r", "\n", "\t"), ' ', substr(strip_tags($value), 0, 128)); 
			else if($key == 'email') $value = $this->sanitizer->email($value); 
			else if($key == 'ip') $value = filter_var($value, FILTER_VALIDATE_IP); 
			else if($key == 'user_agent') $value = str_replace(array("\r", "\n", "\t"), ' ', substr(strip_tags($value), 0, 255)); 

		// save the state so that modules can identify when a comment that was identified as spam 
		// is then set to not-spam, or when a misidentified 'approved' comment is actually spam
		if($key == 'status') $this->prevStatus = $this->status; 

		return parent::set($key, $value); 
	}

	/**
	 * Clean a comment string by issuing several filters
	 *
	 */
	public function cleanCommentString($str) {
		$str = strip_tags(trim($str)); 
		if(strlen($str) > self::maxCommentBytes) $str = substr($str, 0, self::maxCommentBytes); 
		$str = str_replace("\r", "\n", $str); 
		$str = preg_replace('{\n\n\n+}', "\n\n", $str); 
		return $str; 
	}

	/**
	 * String value of a Comment is it's database ID
	 *
	 */
	public function __toString() {
		return "{$this->id}"; 
	}

	/**
	 * Returns true if the comment is approved and thus appearing on the site
	 *
	 */
	public function isApproved() {
		return $this->status >= self::statusApproved; 
	}

}



