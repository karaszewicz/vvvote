<?php
require_once 'dbBase.php';

/**
 * errorno starts at 3000
 * @author r
 *
 */
class DbSharedPasswAuth extends DbBase {

	function __construct($dbInfos) {
		$dbtables =
		array('sp_credentials' /* Table name */ => array(
				array('name' => 'electionId'    , 'digits' => '100'), /* colunm definition */
				array('name' => 'sp_credentials', 'digits' => '100')
		));
		parent::__construct($dbInfos, $dbtables, true);
	}

	function checkCredentials($electionId, $voterId, $secret) {
		$secretFromDb = $this->load(array('electionId' => $electionId), 'sp_credentials', 'sp_credentials');
		if (! (count($secret) === 1)) return false;
		if ($secretFromDb[0] === $secret) {
			return true;
		}
		return false;
	}
	
	function newElection($electionId, $secret) {
		$exists = $this->load(array('electionId' => $electionId), 'sp_credentials', 'electionId');
		if (isset($exists[0])) { 
			WrongRequestException::throwException(3000, 'election ID already used', $electionId);
		}
		$saved = $this->save(array('electionId' => $electionId, 'sp_credentials' => $secret), 'sp_credentials');
		if (! $saved) {
			WrongRequestException::throwException(3001, 'internal server error; election not saved', $electionId);
		}
		return $saved;
	}
	


}


?>