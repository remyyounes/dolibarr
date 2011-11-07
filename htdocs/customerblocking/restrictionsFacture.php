<?php
	include_once(DOL_DOCUMENT_ROOT."/customerblocking/class/societe_blocage.class.php");
	$customerBlocking = new Societe_blocage($db);
	$customerBlocking->fetch($socid, 1);
	$customerBlockedStatus = $customerBlocking->getSocieteBlockingStatus();
?>