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
require_once(DOL_DOCUMENT_ROOT."/core/class/canvas.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/extrafields.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");

$langs->load("products");
$langs->load("productunits@productunits");
$langs->load("other");
if ($conf->stock->enabled) $langs->load("stocks");
if ($conf->facture->enabled) $langs->load("bills");

$mesg=''; $error=0; $errors=array();

$id=GETPOST('id');
$ref=GETPOST('ref');
$action=(GETPOST('action') ? GETPOST('action') : 'view');
$confirm=GETPOST('confirm');
$socid=GETPOST("socid");
if ($user->societe_id) $socid=$user->societe_id;
$currentmodule = "product";
$customfields_table = "units";
    
$object = new Product($db);
$extrafields = new ExtraFields($db);

foreach ($_POST as $key=>$value) { // Generic way to fill all the fields to the object (particularly useful for triggers and customfields)
    $object->$key = $value;
}

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$object->getCanvas($id,$ref);
$canvas = $object->canvas?$object->canvas:GETPOST("canvas");
if (! empty($canvas))
{
    require_once(DOL_DOCUMENT_ROOT."/core/class/canvas.class.php");
    $objcanvas = new Canvas($db,$action);
    $objcanvas->getCanvas('product','card',$canvas);
}

// Security check
$value = $ref?$ref:$id;
$type = $ref?'ref':'rowid';
$result=restrictedArea($user,'produit|service',$value,'product','','',$type, $objcanvas);

/*******************************************************************
 * ACTIONS
 ********************************************************************/

if ($_POST["action"] == 'update' && ! $_POST["cancel"] )
{
    $object->fetch($id);
    //update
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    $customfields = new CustomFields($db, $currentmodule, $customfields_table);
    $upd = $customfields->create($object);
     
    //display status message from update/create
    if ($upd > 0){
        $mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
    }else{
        $mesg = '<div class="error">'.$object->error.'</div>';
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

if ($id > 0)
{
    $object->fetch($id);


    $head=product_prepare_head($object, $user);
    $titre=$langs->trans("CardProduct".$object->type);
    $picto=($object->type==1?'service':'product');
    dol_fiche_head($head, 'productunits', $titre, 0, $picto);

    if ($action == 'edit')
    {
        print '<form action="productunits.php?id='.$id.'" method="post">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="id" value="'.$id.'">';
    }

    // En mode visu
    print '<table class="border" width="100%"><tr>';

    // Ref
    print '<td>'.$langs->trans("Ref").'</td><td colspan="3">';
    print $form->showrefnav($object,'ref','',1,'ref');
    print '</td>';

    print '</tr>';

    // Label
    print '<tr><td>'.$langs->trans("Label").'</td><td colspan="3">'.$object->libelle.'</td>';

    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    include_once(DOL_DOCUMENT_ROOT.'/customfields/lib/customfields.lib.php');

    $customfields = new CustomFields($db, $currentmodule, $customfields_table);
    $rights = 1;
    $idvar = "id";
    /*
    if($action == "edit"){
        customfields_print_creation_form($currentmodule, $id,$customfields_table);
    }else{
        customfields_print_main_form($currentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    }
    */
    $fields_data = customfields_load_main_form($currentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    printCustomForm($fields_data);
    
    
    
    if ($action == 'edit')
    {
        print '<tr><td colspan="4" align="center"><input type="submit" class="button" value="'.$langs->trans("Save").'">';
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td></tr>';
    }

    print "</table>";
    if ($action == 'edit'){
        print "</form>";
    }

}

print '</div>';


/* ************************************************************************** */
/*                                                                            */
/* Barre d'action                                                             */
/*                                                                            */
/* ************************************************************************** */

if (empty($action) || $action != 'edit')
{
    print "\n<div class=\"tabsAction\">\n";

    if ($user->rights->societe->creer)
    {
        print '<a class="butAction" href="'.DOL_URL_ROOT.'/productunits/productunits.php?action=edit&amp;id='.$id.'">'.$langs->trans("Editer les Unites Produit").'</a>';
    }

    print "\n</div>\n";
}

if ($mesg)
{
    print $mesg;
}

// End of page
$db->close();
llxFooter();

function printCustomForm($fields_data){
    print '<tr>';
    print '<td width="15%">'.$fields_data['libelle_unca']['label'].'</td>'.'<td width="15%">'.$fields_data['libelle_unca']['data'].'</td>';
    print '<td width="15%">'.$fields_data['libelle_uncv']['label'].'</td>'.'<td width="15%">'.$fields_data['libelle_uncv']['data'].'</td>';
    print '</tr><tr>';
    print '<td>'.$fields_data['weight']['label'].'</td>'.'<td>'.$fields_data['weight']['data'].$fields_data['weight_unit']['data'].'</td>';
    print '<td>'.$fields_data['publiable']['label'].'</td>'.'<td>'.$fields_data['publiable']['data'].'</td>';
    print '</tr><tr>';
    print '<td>'.$fields_data['volume']['label'].'</td>'.'<td>'.$fields_data['volume']['data'].$fields_data['volume_unit']['data'].'</td>';
    print '<td>'.$fields_data['reprise']['label'].'</td>'.'<td>'.$fields_data['reprise']['data'].'</td>';
    print '</tr><tr>';
    print '<td>'.$fields_data['cfa']['label'].'</td>'.'<td>'.$fields_data['cfa']['data'].'</td>';
    print '<td>'.$fields_data['cvs']['label'].'</td>'.'<td>'.$fields_data['cvs']['data'].'</td>';
    print '</tr><tr>';
    print '<td>'.$fields_data['libelle_fk_marque']['label'].'</td>'.'<td>'.$fields_data['libelle_fk_marque']['data'].'</td>';
    print '<td>'.$fields_data['delailivraison']['label'].'</td>'.'<td>'.$fields_data['delailivraison']['data'].'</td>';
    print '</tr><tr>';
    print '<td>'.$fields_data['libelle_fk_fabricant']['label'].'</td>'.'<td>'.$fields_data['libelle_fk_fabricant']['data'].'</td>';
    print '<td>'.$fields_data['delaifabrication']['label'].'</td>'.'<td>'.$fields_data['delaifabrication']['data'].'</td>';
    print'</tr>';
}


?>
