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
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/makinalib/class/MakinaLib.class.php");
require_once("./class/societe_remise_prod.class.php");
require_once("./class/societe_remise_prod_log.class.php");

// Load traductions files requiredby by page
$langs->load("products");

// Get parameters
$myparam = isset($_GET["myparam"])?$_GET["myparam"]:'';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}


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
		$prodId = $_POST['cr_fk_product'];
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
	$soc_rem->setDiscountType($typeRemise);
	$soc_rem->setDiscountValue($remiseValue);
	$finalPrice = $soc_rem->getDiscountPrice($prodId);


	$soc_rem->qte = $_POST['cr_qte'];
	if($soc_rem->qte <=0){
		$soc_rem->qte=0;
	}
	if($finalPrice > 0 || $catremise == "categorie"){
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

$product = new Product($db);
if ($_GET["ref"]) $result = $product->fetch('',$_GET["ref"]);
if ($_GET["id"]) $result = $product->fetch($_GET["id"]);
/*
 if($product->id > 0 ){
 $productGamme= new Product_gamme($db);
 $productGamme->fetch($product->id,1);
 }
 */
llxHeader("","",$langs->trans("CardProduct".$product->type));

$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
$picto=($product->type==1?'service':'product');
dol_fiche_head($head, 'tabRemiseProduit', $titre, 0, $picto);

$html = new Form($db);
print '<table class="border" width="100%">';

// Reference
print '<tr>';
print '<td width="15%">'.$langs->trans("Ref").'</td><td colspan="2">';
print $html->showrefnav($product,'ref','',1,'ref');
print '</td>';
print '</tr>';

// Libelle
print '<tr><td>'.$langs->trans("Label").'</td><td colspan="2">'.$product->libelle.'</td>';
print '</tr>';


//ALL FIELDS
/*
 $gammeListing = GammeListing::singleton($db);
 $gammeListing->load_cache_gamme();

 foreach($productGamme->editable_fields as $field)
 {
 print '<tr><td>'.$langs->trans("pg_".$field).'</td><td colspan="2">'.$gammeListing->cache_gamme[strtolower($productGamme->$field)].'</td>';
 print '</tr>';
 }
 */
//End View
print '</table>';
print "</div>\n";


/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

if (empty($_GET["action"]) || $_GET["action"]=='update' || $_GET["action"]=='disable' || $_GET["action"]=='activate' || $_GET["action"]=="delete")
{
	print "\n<div class=\"tabsAction\">\n";

	if ($user->rights->societe->creer)
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/societeremiseproduit/remiseproduit.php?action=edit&catremise=product&amp;id='.$product->id.'">'.$langs->trans("Creer une remise sur ce produit").'</a>';
	}

	print "\n</div>\n";
}





/*
 * Edition des remises
 */
if ($_GET["action"] == 'edit')
{

	print_fiche_titre($langs->trans("Edition de la remise authorisee"),'','');

	$socRemProd = new Societe_remise_prod($db);

	$catremise = 'product'; //$_GET['catremise'];
	$priceLevel = 1;


	// START INCLUDES
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/scriptaculous/lib/prototype.js"></script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/scriptaculous/src/scriptaculous.js?load=effects"></script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/modalbox.js"></script>';
	//echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/fastinit.js"></script>';
	echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/tablekit.js"></script>';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.DOL_URL_ROOT.'/makinalib/css/tablekit.css">';
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.DOL_URL_ROOT.'/makinalib/css/modalbox.css">';
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



	print '<form action="remiseproduit.php?id='.$product->id.'" method="post" onSubmit="return isValidDiscount()">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update">';
	print '<input type="hidden" name="id" value="'.$product->id.'">';
	print '<input type="hidden" name="catremise" value="'.$catremise.'">';
	print '<input type="hidden" name="socPriceLevel" value="'.$priceLevel.'">';
	print '<input type="hidden" name="cr_fk_product" id="cr_fk_product" value="'.$product->id.'">';
	print '<table class="border" width="100%">';


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
	print '<select class="flat" name="'.$keysearch.$HTMLN.'" id="'.$keysearch.$HTMLN.'" onChange="setTypeRemise(this.value)" >';
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
			var prodId = "'.$_GET['id'].'";
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


/* ************************************************************************** */
/*                                                                            */
/* Log			                                                              */
/*                                                                            */
/* ************************************************************************** */

if ($product->id > 0)
{
	print "<br>";
	$soc_remise_prod_log = new Societe_remise_prod_log($db);
	$soc_remise_prod_log->fetch($product->id ,1);
	$soc_remise_prod_log->printLogTable();
}


if ($mesg) print $mesg;

// End of page
$db->close();
llxFooter('$Date: 2009/03/09 11:28:12 $ - $Revision: 1.8 $');


?>
