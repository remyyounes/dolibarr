<?php

///require('../masters.inc.php');
require_once("../master.inc.php");

// This is to make Dolibarr working with Plesk
set_include_path($_SERVER['DOCUMENT_ROOT'].'/htdocs');

// Add real path in session
$realpath='';
if ( preg_match('/^([^.]+)\/htdocs\//i', realpath($_SERVER["SCRIPT_FILENAME"]), $regs))	$realpath = isset($regs[1])?$regs[1]:'';

// Init session. Name of session is specific to Dolibarr instance.
$sessionname='DOLSESSID_'.md5($_SERVER["SERVER_NAME"].$_SERVER["DOCUMENT_ROOT"].$realpath);
$sessiontimeout='DOLSESSTIMEOUT_'.md5($_SERVER["SERVER_NAME"].$_SERVER["DOCUMENT_ROOT"].$realpath);
if (! empty($_COOKIE[$sessiontimeout])) ini_set('session.gc_maxlifetime',$_COOKIE[$sessiontimeout]);
session_name($sessionname);
session_start();


require_once(DOL_DOCUMENT_ROOT."/caisse/class/dolibarrCaisseAdapter.class.php");
$dolCaisseAdapter = new DolibarrCaisseAdapter($db);
$response = array();
$error = "";
$action = $_POST['action'];

if ($action == "login"){
	$res = $dolCaisseAdapter->dolibarrLogin($_POST['username'], $_POST['password']);
	if($res > 0){
		$response['status'] = "ok";
		$response['userData'] = $res;
		$response['defaultClient'] = $dolCaisseAdapter->getDefaultClientData();
	}else{
		$error = "Probleme lors de l'authentification";
		$response['status'] = "Error";
	}
}elseif ($action == "logout"){
	session_destroy();
	$response['status'] = "ok";
}


if ( $_SESSION['uid'] <= 0 )
{
	$response = array();
	$response['action'] = "loginRequired";
	$response['token'] = $_SESSION['newtoken'];
	echo json_encode($response);
	exit;
}




require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");
require_once(DOL_DOCUMENT_ROOT.'/core/modules/facture/modules_facture.php');
require_once(DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/discount.class.php');
require_once(DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions2.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/lib/invoice.lib.php');
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
if ($conf->projet->enabled)   require_once(DOL_DOCUMENT_ROOT.'/projet/class/project.class.php');
if ($conf->projet->enabled)   require_once(DOL_DOCUMENT_ROOT.'/lib/project.lib.php');
if ($conf->makinalib->enabled) {
	require_once(DOL_DOCUMENT_ROOT.'/makinalib/class/makinaProduct.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/makinalib/class/makinaClient.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/makinalib/class/makinaUser.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/makinalib/class/productDiscount.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/makinalib/class/FactureLigneExtra.class.php');
}
if ($conf->unitesproduit->enabled){

	require_once(DOL_DOCUMENT_ROOT.'/unitesproduit/class/product_base.class.php');
	require_once(DOL_DOCUMENT_ROOT.'/unitesproduit/class/c_unitemesure.class.php');
}
require_once(DOL_DOCUMENT_ROOT."/makina/class/productDiscount.class.php");


$user = new MakinaUser($db);
$user->fetch($_SESSION['uid']);
$user->getRights();

if($action == 'newTicket'){
	$facture = $dolCaisseAdapter->createTicket($_POST['clientId'], null);
	$response['ticketId'] = $facture->id;
	$response['ticketNumber'] = $facture->ref;

}elseif($action == "getClientList"){
	$clients = $dolCaisseAdapter->getClients();
	$response["clients"] = $clients;

}elseif($action == "getClientTickets"){
	$tickets = $dolCaisseAdapter->getClientTickets($_POST['clientId']);
	$response['tickets'] = $tickets;

}elseif($action == "loadTicket"){
	$items = $dolCaisseAdapter->loadticket($_POST['ticketId']);
	$response['items'] = $items;
	$response['ticketId'] = $_POST['ticketId'];
	$response['facnumber'] = $_POST['facnumber'];

}elseif($action == "saveItem"){
	$item = $_POST['item'];
	if($item['rowid'] <= 0){
		$response['saveAction'] = "create";
		$res = $dolCaisseAdapter->addTicketItem($_POST['ticketId'],$_POST['item'],$_POST['clientId']);
	}else{
		$response['saveAction'] = "update";
		$res = $dolCaisseAdapter->updateTicketItem($_POST['ticketId'],$_POST['item'], $_POST['clientId']);
	}
	$response['item'] = $res['item'];
	$response['itemStatus'] = $res['status'];
	$response['ticketId'] = $_POST['ticketId'];

}elseif($action == "getProducts"){
	$items = $dolCaisseAdapter->getProducts($_POST['filters']);
	$response['items'] = $items;

}elseif($action == "getItem"){
	$item = $dolCaisseAdapter->getItem($_POST['params']);
	$response['item'] = $item;

}elseif ($action == "deleteItem"){
	$res = $dolCaisseAdapter->removeTicketItem($_POST['ticketId'], $_POST['item']);
	if($res){
		$response['itemStatus'] = "ok";
	}else{
		$response['itemStatus'] = "error";
		$error .= "Probleme lors de la suppresion de ligne";
	}
	$response['item'] = $_POST['item'];

}elseif ($action == "deleteTicket"){
	$res = $dolCaisseAdapter->deleteTicket($_POST['ticketId']);
	if($res){
		$response['ticketStatus'] = "ok";
	}else{
		$response['ticketStatus'] = "error";
		$error .= "Probleme lors de la suppresion de ticket";
	}
	$response['ticketId'] = $_POST['ticketId'];

}elseif($action == "getEncaissementInfo"){
	$res = $dolCaisseAdapter->getEncaissementInfo($_POST['ticketId'], $_POST['clientId']);
	if($res){
		$response['ticketStatus'] = "ok";
		$response['clientInfo'] = $res->clientInfo;
		$response['ticketInfo'] = $res->ticketInfo;
		$response['historique'] = $res->historique;
		$response['historique_num'] = $res->historique_num;
		$response['warning'] = $res->warning;
	}else{
		$response['ticketStatus'] = "error";
		$error .= "Probleme lors de l'encaissement";
	}
	$response['ticketId'] = $_POST['ticketId'];
}elseif($action == "pay"){
	$res = $dolCaisseAdapter->payTicket($_POST['ticketId'], $_POST);
	if($res){
		$response['ticketStatus'] = "ok";
		$response['ticketNumber'] = $res;
	}else{
		$response['ticketStatus'] = "error";
		$error .= "Probleme lors de l'encaissement";
	}
	$response['ticketId'] = $_POST['ticketId'];
}
$response['action'] = $action;
$response['error'] = $error;
//clean html output, only show json
ob_clean();
echo json_encode($response);


?>