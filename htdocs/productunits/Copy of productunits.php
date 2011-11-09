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
require_once("./class/product_base.class.php");
require_once("./class/c_unitemesure.class.php");
require_once("./class/c_marque.class.php");
require_once("./class/c_fabricant.class.php");
require_once(DOL_DOCUMENT_ROOT."/makinalib/class/MakinaLib.class.php");




// Load traductions files requiredby by page
$langs->load("products");

// Get parameters
$myparam = isset($_GET["myparam"])?$_GET["myparam"]:'';

// Protection if external user
if ($user->societe_id > 0)
{
	//accessforbidden();
}

//List of Product_base Fields 
$nonUnitFields = array('nbp','publiable','reprise','marque','fabricant','numeroserie');
/*******************************************************************
* ACTIONS
********************************************************************/

if ($_POST["action"] == 'update_unites' && ! $_POST["cancel"] && ($user->rights->produit->creer || $user->rights->service->creer))
{
	$product = new Product($db);
	$productUnits = new Product_base($db);
	$result = $product->fetch($_GET["id"]);
	$res = $productUnits->fetch($_GET["id"],1);
	$productUnits->fk_product=$_GET['id'];
	
	//Fetch all post values and store them in out Product_base Object for update
	$productUnits->editableFields[]="poidsa_units";
	$productUnits->editableFields[]="volumea_units";
	
	foreach($productUnits->editableFields as $field)
	{
		$productUnits->$field=$_POST["pu_".$field];
		if(in_array($field,$productUnits->units)){
			$productUnits->$field = MakinaLib::numberFormat($productUnits->$field,4);//,$thou,$pnt,$curr1,$curr2);		
		}
		
		if($productUnits->$field == ""){
			$productUnits->$field = 0;
		}
	}

	//create or update
	if($productUnits->id > 0){
		$upd = $productUnits->update($user);
	}else{
		$upd = $productUnits->create($user);
	}
	
	//display status message from update/create
	if ($upd > 0)
	{
		$mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
	}
	else
	{
		$mesg = '<div class="error">'.$productUnits->error.'</div>';
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
if($product->id > 0 ){
	$productUnits = new Product_base($db);
	$productUnits->fetch($product->id,1);
}
llxHeader("","",$langs->trans("CardProduct".$product->type));

$head=product_prepare_head($product, $user);
$titre=$langs->trans("CardProduct".$product->type);
$picto=($product->type==1?'service':'product');
dol_fiche_head($head, 'tabComplement', $titre, 0, $picto);

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

print '</table>';

print '<table class="border" width="100%">';
	
	//$unmValue = "";
	//if($c_unitemesure->code != ""){	$unmValue=$c_unitemesure->code.' - '. $c_unitemesure->libelle;}
	$boolean = array('publiable','reprise','numeroserie');
	
	print "<tr>";
	
	$langs->load("other");
	$i = 0;
	foreach($productUnits->editableFields as $f){
		$i++;
		
		if( in_array($f, $productUnits->boolean) ){
			print '<td width="15%">'.$langs->trans("pu_".$f).'</td><td width="35%">'.yn($productUnits->$f).'</td>';
		}elseif ( in_array($f, $productUnits->unite) ){
			$c_unitemesure = new C_unitemesure($db);
			$c_unitemesure->fetch($productUnits->$f);
			$unmValue = "";
			if($c_unitemesure->code != ""){	$unmValue=$c_unitemesure->code.' - '. $c_unitemesure->libelle;}
			print '<td width="15%">'.$langs->trans("pu_".$f).'</td><td width="35%">'.$unmValue.'</td>';
		}elseif($f == "fk_fabricant"){
		    $code = new C_fabricant($db);
			$code->fetch($productUnits->$f);
			print '<td>'.$langs->trans("pu_".$f).'</td><td>'.$code->printCode().'</td>';
		}elseif($f == "fk_marque"){
		    $code = new C_marque($db);
			$code->fetch($productUnits->$f);
			print '<td>'.$langs->trans("pu_".$f).'</td><td>'.$code->printCode().'</td>';
		}elseif($f == "volumea"){
			print '<td width="15%">'.$langs->trans("pu_".$f).'</td><td width="35%">'.$productUnits->$f." ".measuring_units_string($productUnits->volumea_units,"volume").'</td>';
		}elseif($f == "poidsa"){
			print '<td width="15%">'.$langs->trans("pu_".$f).'</td><td width="35%">'.$productUnits->$f." ".measuring_units_string($productUnits->poidsa_units,"weight").'</td>';
		}else{
			print '<td width="15%">'.$langs->trans("pu_".$f).'</td><td width="35%">'.$productUnits->$f.'</td>';
		}
//		measuring_units_string($product->weight_units,"weight");
		
		if($i % 2 ==0){
			print "</tr><tr>";
		}
	}
	print "</tr>";

//End View
print '</table>';


print "</div>\n";

/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

if (empty($_GET["action"]) || $_GET["action"]=='update_unites')
{
	print "\n<div class=\"tabsAction\">\n";

	if ($user->rights->produit->creer || $user->rights->service->creer)
	{
		print '<a class="butAction" href="'.DOL_URL_ROOT.'/unitesproduit/unites.php?action=edit_unites&amp;id='.$product->id.'">'.$langs->trans("Changer les Unites").'</a>';
	}

	print "\n</div>\n";
}

/*
 * Edition des Unites
 */
if ($_GET["action"] == 'edit_unites' && ($user->rights->produit->creer || $user->rights->service->creer))
{
	print_fiche_titre($langs->trans("Edition des Unites"),'','');
	
	$uniteMesure = new C_unitemesure($db);

	print '<form action="unites.php?id='.$product->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update_unites">';
	print '<input type="hidden" name="id" value="'.$product->id.'">';
	print '<table class="border" width="100%">';
	

	print "<tr>";
	$i = 0;
	foreach($productUnits->editableFields as $f){
		$i++;
		print $productUnits->form_inputField($f,"pu_".$f);
		if($i % 2 ==0){
			print "</tr><tr>";
		}
		
	}
	print "</tr>";
	
	//buttons
	print '<tr><td colspan="4" align="center"><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td></tr>';
	
	//end of edition
	print '</form>';
	print '</table>';
}



/* ************************************************************************** */
/*                                                                            */
/* JAVA SCRIPT                                                                */
/*                                                                            */
/* ************************************************************************** */


print '<script src="../makinalib/js/numbers.js"></script>';
print '<script src="../makinalib/js/keyboardNav.js"></script>';
print '<script type="text/javascript" language="JavaScript">';

print '
	function valueFormat(obj,dec) {
		var val = obj.value;
		val = val.replace(",",".").replace(" ","");
		obj.value = formatNumber(val,dec," ",",","","","","");
	}';
print '</script>';



if ($mesg) print $mesg;

// End of page
$db->close();
llxFooter('$Date: 2009/03/09 11:28:12 $ - $Revision: 1.8 $');


?>
