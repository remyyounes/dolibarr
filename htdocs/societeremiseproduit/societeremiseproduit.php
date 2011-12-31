<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
echo "td"; * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *   	\file       dev/skeletons/skeleton_page.php
 *		\ingroup    mymodule othermodule1 othermodule2
 *		\brief      This file is an example of a php page
 *		\version    $Id: skeleton_page.php,v 1.8 2009/03/09 11:28:12 eldy Exp $
 *		\author		Put author name here
 *		\remarks	Put here some comments
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/makinalib/class/MakinaLib.class.php");
require_once("./class/societe_remise_prod.class.php");
require_once("./class/societe_remise_prod_log.class.php");
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");

$action = isset($_GET["action"])?$_GET["action"]:$_POST["action"];

// Load traductions files required by by page
$langs->load("companies");
$langs->load("societeremiseproduit@societeremiseproduit");
// Security check
$socid = isset($_GET["socid"])?$_GET["socid"]:$_POST["socid"];
if ($user->societe_id) $socid=$user->societe_id;
$result = restrictedArea($user, 'societe', $socid);


/*******************************************************************
* ACTIONS
********************************************************************/

if ($_GET["action"] == 'activate' || $_GET["action"] == 'disable' )
{
	$remise = new Societe_remise_prod($db);
	$active = ($_GET['action'] == 'activate')?1:0;
	
	$remise->fetch($_GET['remiseId']);
	$remise->active = $active;
	if( ! $remise->update($user)  ){
   		$mesg = '<div class="error">'.$remise->error.'</div>';
	}
}

if ($_GET["action"] == 'delete')
{
	$remise = new Societe_remise_prod($db);
	$remise->fetch($_GET['remiseId']);
	if( ! $remise->delete($user)  ){
   		$mesg = '<div class="error">'.$remise->error.'</div>';
	}
}
	

if ($_POST["action"] == 'update' && ! $_POST["cancel"] )
{
	
	$typeRemises = array();
	$typeRemises['taux'] = 'txrem';
	$typeRemises['coeff'] = 'cfrem';
	$typeRemises['level'] = 'productlevel';
	$typeRemises['prix'] = 'prem';
	$typeRemise = $_POST['typeselectRemise'];

	$active = $_POST['cr_active'];
	$catremise = $_POST['catremise'];
	
	if($catremise=="product"){
		$prodId = $_POST['idprod'];
	}else{
		$prodId = $_POST['cr_fk_categorie'];
	}
	$prodType = $_POST['prodType'];
	$socPriceLevel = $_POST['socPriceLevel'];
	$prodOrCatVar = 'fk_'.$catremise;
	$socOrCatVar = 'fk_'."soc";
	$remiseVar =  $typeRemises[$typeRemise];
	$remiseValue = $_POST["cr_".$remiseVar];
	
	
	
	$soc_rem = new Societe_remise_prod($db);
	$soc_rem->setDiscountValue($remiseValue);
	$soc_rem->setDiscountType($typeRemise);
	$finalPrice = $soc_rem->getDiscountPrice($prodId);
	
	$soc_rem->qte = $_POST['cr_qte'];
	if($soc_rem->qte <=0){
		$soc_rem->qte=0;
	}
	if($finalPrice > 0 || $catremise == "categorie"){
		$soc_rem->$socOrCatVar = $socid;
		$soc_rem->$prodOrCatVar = $prodId;
		$soc_rem->active = $active;
		$soc_rem->$remiseVar=$remiseValue;
		$soc_rem->datec=time();
		if($_POST["cr_datedmonth"]){
			$soc_rem->dated=dol_mktime(12,0,0, $_POST["cr_datedmonth"], $_POST["cr_datedday"], $_POST["cr_datedyear"]);
		}
		if($_POST["cr_datefmonth"]){
    		$soc_rem->datef=dol_mktime(12,0,0, $_POST["cr_datefmonth"], $_POST["cr_datefday"], $_POST["cr_datefyear"]);
		}
    	$soc_rem->fk_user_author = $user->id;
    	if($catremise == "product"){
    		$soc_rem->prem2 = $finalPrice;
    	}
    	if($soc_rem->create($user) > 0){
    		$mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
    	}else{
    		$mesg = '<div class="error">'.$soc_rem->error.'</div>';
    	}
	}else{
		$mesg = '<div class="error">'.$soc_rem->error.'</div>';
	}
}

/***************************************************
* PAGE
****************************************************/

/*
 * View
 */

$form = new Form($db);

llxHeader();


if ($socid > 0)
{
    $societe = new Societe($db, $socid);
    $societe->fetch($socid);
	$socRemProd = new Societe_remise_prod($db);
	$socRemProd->fetch($socid,1);

	/*
	 * Affichage onglets
	 */
    if ($conf->notification->enabled) $langs->load("mails");
    
	$head = societe_prepare_head($societe);

	dol_fiche_head($head, 'tabSocieteRemiseProduit', $langs->trans("ThirdParty"),0,'company');
	print "<form method=\"post\" action=\"".DOL_URL_ROOT."/societe/socnote.php\">";
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';

	print '<table class="border" width="100%">';

	print '<tr><td width="20%">'.$langs->trans('Name').'</td>';
	print '<td colspan="3">';
	print $form->showrefnav($societe,'socid','',1,'rowid','nom');
	print '</td></tr>';

    print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$societe->prefix_comm.'</td></tr>';

    if ($societe->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $societe->code_client;
        if ($societe->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($societe->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $societe->code_fournisseur;
        if ($societe->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }
    
	print "</table>";

	print '</form>';
}

print '</div>';

/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

if (empty($_GET["action"]) || $_GET["action"]=='update')
{
	print "\n<div class=\"tabsAction\">\n";

	if ($user->rights->societe->creer)
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/societeremiseproduit/societeremiseproduit.php?action=edit&catremise=product&amp;socid='.$socid.'">'.$langs->trans("Creer une remise sur un produit").'</a>';
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/societeremiseproduit/societeremiseproduit.php?action=edit&catremise=categorie&amp;socid='.$socid.'">'.$langs->trans("Creer une remise sur une categorie").'</a>';
	}

	print "\n</div>\n";
}



/*
 * Edition des Unites
 */
if ($_GET["action"] == 'edit')
{
	
	print_fiche_titre($langs->trans("Edition de la remise authorisee"),'','');
	
	if($socid){
		$socRemProd = new Societe_remise_prod($db);
		$socRemProd->fetch($socid,1);
	}
	
	$catremise = $_GET['catremise'];
	$priceLevel = $soc->price_level;
	if($priceLevel == ''){ $priceLevel = 1; }
	
	
	// START INCLUDES
	$baseUrl = DOL_URL_ROOT;
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/modalbox.js"></script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/tablekit.js"></script>';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.DOL_URL_ROOT.'/makinalib/css/tablekit.css">';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.DOL_URL_ROOT.'/makinalib/css/modalbox.css">';
	echo '<script language="javascript">var baseUrl = "'.$baseUrl.'";</script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/productsearch.js"></script>';
	// END INCLUDES
	
	$typeRemises = array();
	$typeRemises[0] = "-- ".$langs->trans("Types de Remise")." --";
	$typeRemises['taux'] = $langs->trans('cr_txrem');
	$typeRemises['coeff'] = $langs->trans('cr_cfrem');
	if($conf->global->PRODUIT_MULTIPRICES){
		$typeRemises['level'] = $langs->trans('cr_productlevel');
	}
	if($catremise == "product"){
		$typeRemises['prix'] = $langs->trans('cr_prem');
	}
	
	$keysearch = "type";
	$HTMLN = 'selectRemise';
	
	if($catremise == "product"){
		$prodId_field ='cr_fk_product';
	}else{
		$prodId_field = 'cr_fk_categorie';
	}
	
	
	
	print '<form action="societeremiseproduit.php?socid='.$socid.'" method="post" onSubmit="return isValidDiscount()">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$socid.'">';
	print '<input type="hidden" name="catremise" value="'.$catremise.'">';
	print '<input type="hidden" name="socPriceLevel" value="'.$priceLevel.'">';
	print '<table class="border" width="100%">';
	
	if( $catremise == "product" ){

		print "<tr><td width='15%'>".$langs->trans("cr_fk_product").':</td><td>';
		//	echo '<a href="'.DOL_URL_ROOT.'/makinalib/productSearch.php?price_level='.$societe->price_level.'" title="Products" onclick="Modalbox.show(this.href, {title: this.title, width: 850, height: 500, beforeHide: function(){ window.serializedForm = Form.serialize(\'form\'); }, afterHide: function() { updateProductInfo(serializedForm) } }); return false;">'.$langs->trans('selectProduct').'</a>';
		echo ' <input type="hidden" id="'.$prodId_field.'" name="'.$prodId_field.'">';
		$modalBoxButtonCaption = "Select Product ";
		$modalBoxTitle= "Products";
		$modalBoxHref = DOL_URL_ROOT.'/makinalib/productSearch.php?price_level='.$societe->price_level;
		$modalBoxAfterHide  = "afterHide:  function(){ }";
		$modalBoxBeforeHide = "beforeHide: function(){ window.serializedForm = Form.serialize('form'); }";
		$modalBoxOpen = 'onclick="Modalbox.show(\''.$modalBoxHref.'\', {title: \''.$modalBoxTitle.'\', width: 850, height: 500, '.$modalBoxBeforeHide.', '.$modalBoxAfterHide.'}); return false;"';
		echo ' <input type="button" style="padding:1px;" value="'.$modalBoxButtonCaption.'" '.$modalBoxOpen.'> ';
		echo ' <div style="display:inline;" id="ajdynfieldidprod"><select class="flat" id="idprod" name="idprod"></select></div>';
		//$form->select_produits($socRemProd->fk_product,'cr_fk_product','',$conf->product->limit_size,$priceLevel);
	}else{
		echo "<tr><td width='15%'>".$langs->trans("cr_fk_categorie").':</td><td>';
		echo $form->select_all_categories(0,$socRemProd->fk_categorie,'cr_fk_categorie',$conf->product->limit_size);
	}
	print "</td></tr>";
	
	foreach($socRemProd->editable_fields as $f){
		if($catremise!= "product" && $f == "qte"){
			continue;
		}
		$f_html = "cr_".$f;
		echo $socRemProd->form_inputField($f,$f_html);
	}
	
	
	
	print "<tr><td>".$langs->trans("Remise").':</td>';
	print '<td>';
	print ajax_indicator($HTMLN,'working');	// Indicator is et here
	print '<select name="'.$keysearch.$HTMLN.'" id="'.$keysearch.$HTMLN.'" onChange="setTypeRemise(this.value)" >';
	foreach($typeRemises as $key=>$val)
	{
		echo '<option value="'.$key.'">'.$val.'</option>';
	}
	print '</select>';
	$htmlname = "CalculatedPrice";
	$ks = "price_adjust";
	$url = DOL_URL_ROOT.'/societeremiseproduit/ajaxremises.php';
	echo '<script type="text/javascript">
			var ks = "'.$ks.'";
			var url = "'.$url.'";
			var htmlname = "CalculatedPrice";
			var prodId = "0";
			var prodRef = "";
			var prodType = "'.$catremise.'";
			var prodId_field = "'.$prodId_field.'";
			var typeRemise = "0";
			var typeRemises = new Array();
			typeRemises["taux"] = "'.('cr_txrem').'";
			typeRemises["coeff"] = "'.('cr_cfrem').'";
			typeRemises["level"] = "'.('cr_productlevel').'";
			typeRemises["prix"] = "'.('cr_prem').'";
			var SelectProduct = "'.$langs->trans('SelectProduct').'";
			var SelectDiscountType = "'.$langs->trans('SelectDiscountType').'";
			var SetDiscountValue = "'.$langs->trans('SetDiscountValue').'";
			var priceLevel = "'.$priceLevel.'";
		</script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/societeremiseproduit/js/remise.js"></script>';
	$options = '&catremise='.$catremise;
	
	print ajax_updater($HTMLN,$keysearch,DOL_URL_ROOT.'/societeremiseproduit/ajaxremises.php',$options ,'','true');	// Indicator is '' to disable it as it is alreay output
	print '</td></tr>';
				
	//buttons
	print '<tr><td colspan="2" align="center"><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input  onclick="this.form.onsubmit=null; return true;" type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td></tr>';
	
	//end of edition
	print '</form>';
	print '</table>';
	
	print '<div id="formError" style="text-align:center; width:100%"></div>';
	
}

if ($mesg)
{
	print $mesg;
}

print "<br>";

/* ************************************************************************** */
/*                                                                            */
/* Log			                                                              */
/*                                                                            */
/* ************************************************************************** */
if ($socid > 0)
{
	print "<br>";
	$soc_remise_prod_log = new Societe_remise_prod_log($db);
	$soc_remise_prod_log->fetch($socid);
	$soc_remise_prod_log->printLogTable(1);
}



// End of page
$db->close();
llxFooter('$Date: 2009/03/09 11:28:12 $ - $Revision: 1.8 $');


?>
