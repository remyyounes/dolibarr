<?php
/* Copyright (C) 2003-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005      Simon Tosser         <simon@kornog-computing.com>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *	\file       htdocs/product/stock/fiche.php
 *	\ingroup    stock
 *	\brief      Page fiche entrepot
 */

require("../main.inc.php");

require_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/stock.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/html.formcompany.class.php");

require_once(DOL_DOCUMENT_ROOT."/stockoperation/class/stockentry.class.php");
require_once(DOL_DOCUMENT_ROOT."/stockoperation/class/stockentry_line.class.php");

$langs->load("products");
$langs->load("stocks");
$langs->load("companies");

$action=GETPOST('action');
$object = new Societe($db);
$sortfield = GETPOST("sortfield");
$sortorder = GETPOST("sortorder");
$id = GETPOST('id');
$facid = GETPOST('facid');
if (! $sortfield) $sortfield="p.ref";
if (! $sortorder) $sortorder="DESC";
$parentmodule = "societe";
$customfields_table = "stockentry";

$stockentry_module = 'stockentry';
$stockentry_subtable = 'line';

$fournid = GETPOST('cf_fk_societe');
$mesg = '';


include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
$customfields = new CustomFields($db, $parentmodule, $customfields_table);
$stockEntry = new Stockentry($db);
$stockEntryLine = new Stockentry_line($db);
$stockEntry->id = $id;
if($id){
    $stockEntry->fetch($id);
}
if($facid){
    $stockEntryLine->fetch($facid);
}


/*
 * Actions
 */

// Ajout entrepot
if ($action == 'add' && $user->rights->stock->creer)
{
    $stockEntry->getPostValues();
    $val = $stockEntry->validateFields();
    $id  = $stockEntry->create($user);
    $stockEntry->numerodossier = "(PROV".$id.")";
    $stockEntry->update($user);
}

if($action == 'addinvoice'){
    $stockEntryLine->getPostValues();
    $stockEntryLine->validateFields();
    $stockEntryLine->fk_stockentry = $id;
    $facid = $stockEntryLine->create($db);
}

//if ($action == 'confirm_delete' && $_REQUEST["confirm"] == 'yes' && $user->rights->stockoperation->supprimer)
if ($action == 'delete' && $user->rights->stockoperation->supprimer)
{
	$result=$stockEntry->delete($user);
	if ($result > 0)
	{
		header("Location: ".DOL_URL_ROOT.'/stockoperation/fiche.php?action=liste');
		exit;
	}
	else
	{
		$mesg='<div class="error">'.$object->error.'</div>';
		$action='';
	}
}

if ($action == 'deletefacture' && $user->rights->stockoperation->supprimer)
{
    $result=$stockEntryLine->delete($user);
    if ($result <= 0)
    {
        $mesg='<div class="error">'.$object->error.'</div>';
        $action='';
    }
}


 
if ($action == 'update' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
	//update
    $stockEntry->getPostValues();
	$val = $stockEntry->validateFields();
	$upd = $stockEntry->update();
	 
	//display status message from update/create
	if ($upd > 0){
	    $mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
	}else{
	    $mesg = '<div class="error">'.$object->error.'</div>';
	}
}

if ($action == 'updatefacture' && $_POST["cancel"] <> $langs->trans("Cancel"))
{
    //update
    $stockEntryLine->getPostValues();
    $val = $stockEntryLine->validateFields();
    $upd = $stockEntryLine->update();

    //display status message from update/create
    if ($upd > 0){
        $mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
    }else{
        $mesg = '<div class="error">'.$object->error.'</div>';
    }
}



if ($_POST["cancel"] == $langs->trans("Cancel"))
{
	$action = '';
	$_GET["id"] = $id;
}



/*
 * View
 */

$productstatic=new Product($db);
$form=new Form($db);
$help_url='EN:Module_Stocks_En|FR:Module_Stock|ES:M&oacute;dulo_Stocks';
llxHeader("",$langs->trans("WarehouseCard"),$help_url);


if($action == 'liste'){
    print_fiche_titre($langs->trans("stockentries"));
    $stockEntry->printList();
    //LEFT JOIN ".MAIN_DB_PREFIX."product_fournisseur_price as pfp ON p.rowid = pfp.fk_product
}
elseif ($action == 'create')
{
	print_fiche_titre($langs->trans("NewStockEntry"));
	$stockEntry->printCreateForm();
}
else
{
	if ($id)
	{
		dol_htmloutput_mesg($mesg);
		/*
		 * Affichage fiche
		 */
		$head = stock_prepare_head($object);

		dol_fiche_head($head, 'card', $langs->trans("Warehouse"), 0, 'stock');
		
		//print ref
		print '<table class="border" style="width:100%"><tr><td style="width:15%">'.$langs->trans('ref').'</td><td>'.$stockEntry->numerodossier.'</td></tr></table>';
		
		if($action == 'edit'){
		    $stockEntry->printEditForm();
		}else{
		    $stockEntry->printDataSheet();
		}
		print '</div>';


		/* ************************************************************************** */
		/*                                                                            */
		/* Barre d'action                                                             */
		/*                                                                            */
		/* ************************************************************************** */

		print "<div class=\"tabsAction\">\n";

		if ($action <> 'edit')
		{
			if ($user->rights->stock->creer)
			print "<a class=\"butAction\" href=\"fiche.php?action=edit&id=".$id."\">".$langs->trans("Modify")."</a>";
			else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";

			if ($user->rights->stock->supprimer)
			print "<a class=\"butActionDelete\" href=\"fiche.php?action=delete&id=".$id."\">".$langs->trans("Delete")."</a>";
			else
			print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
			
			if ($user->rights->stockoperation->creer)
			print "<a class=\"butAction\" href=\"fiche.php?action=createinvoice&id=".$id."\">".$langs->trans("AddFacture")."</a>";
			
		}

		print "</div>";

		/* ************************************************************************************ */
		/*                                                                            			*/
		/* Affichage du formulaire d'ajout de facture la liste des produits de l'entrepot       */
		/*                                                                            			*/
		/* ************************************************************************************ */
		print '<br>';

		if ($action == 'createinvoice')
		{
		    print_fiche_titre($langs->trans("NewStockEntryInvoice"));
		    $stockEntryLine->fk_societe = $stockEntry->fk_societe;
		    $stockEntryLine->mode_calcul = $stockEntry->mode_calcul;
		    $stockEntryLine->numeroconteneur = $stockEntry->numeroconteneur;
		    $hidden_fields = array(array('id',$id));
		    $stockEntryLine->printCreateForm('addinvoice',$hidden_fields);

		}
		/* ************************************************************************** */
		/*                                                                            */
		/* Affichage de la liste des factures du dossier	                          */
		/*                                                                            */
		/* ************************************************************************** */
		
		$stockEntryLine->fk_stockentry = $id;
		$stockEntryLine->printList(" WHERE l.fk_stockentry='".$id."' ");
		
		/* ************************************************************************** */
		/*                                                                            */
		/* Affichage unique des factures du dossier	            		              */
		/*                                                                            */
		/* ************************************************************************** */
		
		print '<br>';
		print_fiche_titre($langs->trans("StockEntryFacture"));
	if ($action == 'showfacture'){
		    $stockEntryLine->fetch($facid);
		    $stockEntryLine->printDataSheet();
		    
		    /* ************************************************************************** */
		    /*                                                                            */
		    /* Barre d'action                                                             */
		    /*                                                                            */
		    /* ************************************************************************** */
		    
		    print "<div class=\"tabsAction\">\n";
		    
		    if ($action <> 'edit')
		    {
		        if ($user->rights->stock->creer)
		        print "<a class=\"butAction\" href=\"fiche.php?action=editfacture&id=".$id."&facid=".$facid."\">".$langs->trans("Modify")."</a>";
		        else
		        print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Modify")."</a>";
		    
		        if ($user->rights->stock->supprimer)
		        print "<a class=\"butActionDelete\" href=\"fiche.php?action=deletefacture&id=".$id."&facid=".$facid."\">".$langs->trans("Delete")."</a>";
		        else
		        print "<a class=\"butActionRefused\" href=\"#\">".$langs->trans("Delete")."</a>";
		        	
		    }
		}
		
		
		if ($action == 'editfacture'){
		    $stockEntryLine->fetch($facid);
		    $hidden_fields = array(array('id',$id),array('facid',$facid));
		    $stockEntryLine->printEditForm('updatefacture',$hidden_fields);
		}
		
		
		print '<br>';
	}
}


$db->close();

llxFooter();
?>
