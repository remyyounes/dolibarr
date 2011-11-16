<?php
/* Copyright (C) 2007 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2011 Remy Younes <ryounes@gmail.com>
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
 *   	\file       taxesparafiscales/taxesparafiscales.php
 *		\ingroup    taxesparafiscales
 *		\brief      This file is the tab page for the taxesparafiscales module
 *		\version    $Id: taxesparafiscales.php,v 1.0 2011/11/14 11:28:12 remyyounes Exp $
 *		\author		Remy Younes
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/canvas.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/class/extrafields.class.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");

$module = 'taxesparafiscales';
$langs->load("products");
$langs->load($module.'@'.$module);
$langs->load("other");

$mesg=''; $error=0; $errors=array();

$id=GETPOST('id');
$ref=GETPOST('ref');
$action=(GETPOST('action') ? GETPOST('action') : 'view');
$confirm=GETPOST('confirm');
$socid=GETPOST("socid");
if ($user->societe_id) $socid=$user->societe_id;
$parentmodule = "product";
$customfields_table = "taxe";
    
$object = new Product($db);
$extrafields = new ExtraFields($db);

foreach ($_POST as $key=>$value) { // Generic way to fill all the fields to the object (particularly useful for triggers and customfields)
    $object->$key = $value;
}

// Security check
$value = $ref?$ref:$id;
$type = $ref?'ref':'rowid';
$result=restrictedArea($user,'produit|service',$value,'product','','',$type);

/*******************************************************************
 * ACTIONS
 ********************************************************************/

if ($_POST["action"] == 'update' && ! $_POST["cancel"] )
{
    $object->fetch($id);
    //update
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    $customfields = new CustomFields($db, $parentmodule, $customfields_table);
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
    dol_fiche_head($head, $module, $titre, 0, $picto);

    if ($action == 'edit')
    {
        print '<form action="'.$module.'.php?id='.$id.'" method="post">';
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

    $customfields = new CustomFields($db, $parentmodule, $customfields_table);
    $rights = 1;
    $idvar = "id";

    $fields_data = customfields_load_main_form($parentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    include('canvas/tpl/datasheet.tpl.php');
    
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
        print '<a class="butAction" href="'.DOL_URL_ROOT.'/'.$module.'/'.$module.'.php?action=edit&amp;id='.$id.'">'.$langs->trans("Editer les Unites Produit").'</a>';
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

?>
