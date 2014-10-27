<?php
/**
 * Admin Tools - Help Admins In thier Task .
 * 
 * @package blesta
 * @subpackage blesta.plugins.Cloud_Backup
 * @copyright Copyright (c) 2005, Naja7host SARL.
 * @link http://www.naja7host.com/ Naja7host
 */
 
class Notes extends AdminUtilsModel {
	
	/**
	 * Initialize Clients
	 */
	public function __construct() {
		parent::__construct();
		Language::loadLang(array("clients"));
	}
	
	/**
	 * Partially constructs the query required by GetNotes 
	 *
	 * @return Record The partially constructed query Record object
	 */
	private function fetchNotes() {
		$fields = array(
			"client_notes.id", "client_notes.client_id", "client_notes.staff_id", "client_notes.title",
			"client_notes.description", "client_notes.stickied", "client_notes.date_added", "client_notes.date_updated"
		);
		
		$this->Record->select($fields)->from("client_notes");
		
		return $this->Record;
	}
	
	
	/**
	 * Get All the Notes 
	 *
	 * @return Record The partially constructed query Record object
	 */

	public function GetNotes($sort_by="id", $order="asc") {	
		$this->Record = $this->fetchNotes();	
		return $this->Record->
		order(array($sort_by=>$order))->fetchAll();
	}

	
	/**
	 * Returns the count of all notes 
	 *
	 * @return int The number of notes 
	 */
	public function getNoteListCount($sticky = null) {	
		
		$total_notes = $this->fetchNotes()->numResults() ;
		$sticky = $this->fetchNotes()->where("client_notes.stickied", "=", "1")->numResults();		
		$unsticky = $this->fetchNotes()->where("client_notes.stickied", "=", "0")->numResults() ;		
	
		// return $this->Record->numResults();		
		return array('total_notes'=> $total_notes  , 'total_sticky'=> $sticky  , 'total_unsticky'=> $unsticky  );		
	}	
	
	/**
	 * Returns the count of all notes 
	 *
	 * @return int The number of notes 
	 */
	public function getNoteListCountSticky($sticky = null) {	
		$this->Record = $this->fetchNotes();		
		return $this->Record->numResults();		
	}		
	/**
	 * Delete the given client note
	 *
	 * @param int $note_id The ID of the note to delete
	 */
	public function deleteNote($note_id) {
		$this->Record->from("client_notes")->where("id", "=", $note_id)->delete();
	}
	
	/**
	 * Retrieves a list of all sticked notes 
	 * @param int $max_limit The maximum number of recent stickied notes to retrieve (optional, default all)
	 * @return array A list of stdClass objects representing each note
	 */
	public function getAllStickyNotes($sort_by="id", $order="asc") {
		$fields = array(
		"client_notes.id", "client_notes.client_id", "client_notes.staff_id", "client_notes.title",
		"client_notes.description", "client_notes.stickied", "client_notes.date_added", "client_notes.date_updated"	);
		return $this->Record->select($fields)->
		from("client_notes")->
		where("stickied", "=", "1")->
		order(array($sort_by=>$order))->fetchAll();

	}
	
	/**
	 * Retrieves a list of all unsticked notes 
	 * @param int $max_limit The maximum number of recent stickied notes to retrieve (optional, default all)
	 * @return array A list of stdClass objects representing each note
	 */
	public function getAllUnStickyNotes($sort_by="id", $order="asc") {
			$fields = array(
			"client_notes.id", "client_notes.client_id", "client_notes.staff_id", "client_notes.title",
			"client_notes.description", "client_notes.stickied", "client_notes.date_added", "client_notes.date_updated"	);
			return $this->Record->select($fields)->
			from("client_notes")->
			where("stickied", "=", "0")->
			order(array($sort_by=>$order))->fetchAll();
	}	
	
	/**
	 * Sets the given note as unstickied
	 *
	 * @param int $note_id The note ID
	 */
	public function unstickNote($note_id) {
		$this->Record->where("id", "=", (int)$note_id)->set("stickied", "=", "0")->update("client_notes");
	}
	
	/**
	 * Sets the given note as unstickied
	 *
	 * @param int $note_id The note ID
	 */
	public function stickNote($note_id) {
		$this->Record->where("id", "=", (int)$note_id)->
			update("client_notes", array('stickied' => "1" ));		
	}	

}
?>