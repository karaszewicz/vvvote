<?php
require_once 'dbBase.php';
class DbAuth extends DbBase {
	
	function __construct($dbInfos) {
		$dbtables =
		array('up_credentials' /* Table name */ => array(
				array('name' => 'electionID'    , 'digits' => '100'), /* colunm definition */
				array('name' => 'voterId'       , 'digits' => '100'),
				array('name' => 'up_credentials', 'digits' => '100')
		));
		parent::__construct($dbInfos, $dbtables, true);
	}
	
	/**
	 * Sets the list of voters and credentials
	 * @param array $voterlist[number]['electionId']['voterID']['secret']
	 */
	function importVoterListFromArray($voterlist) { // TODO make a method in dbBase to import an array
		print_r($voterlist);
		$tname = $this->prefix . 'up_credentials';
		$sql  = "insert into $tname (electionId, voterId, up_credentials) values (:electionId, :voterId, :secret)";
		$stmt = $this->connection->prepare($sql);
		foreach ($voterlist as $voter) {
			$stmt->execute($voter);
			//	print "<br>\n";
			//	print_r($voter);
			//	print_r($stmt->errorInfo());
				
		}
	}
	
	function checkCredentials($electionId, $voterId, $secret) { // TODO use $this->load
		$tname = $this->prefix . 'up_credentials';
		$sql  = "select up_credentials FROM $tname WHERE (electionId = :electionId AND voterId = :voterId)";
		$stmnt = $this->connection->prepare($sql);
		$stmnt->bindValue(':electionId', $electionId);
		$stmnt->bindValue(':voterId'   , $voterId);
		$stmnt->execute();
		// print_r($stmnt);
		$secretFromDb = $stmnt->fetch();
		// print "<br>\n secretFromDb: ";
		// print_r($secretFromDb);
		if ($secretFromDb === false)  {
			return false;
		}
		if ($secretFromDb['up_credentials'] === $secret) {
			return true;
		}
		return false;
	}
	
	
}

?>