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
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

// Load traductions files required by by page
$langs->load("companies");
$action		= (GETPOST('action') ? GETPOST('action') : 'view');
$confirm	= GETPOST('confirm');
$socid		= GETPOST("socid");
$customfields_table = "customerblocking";

if ($user->societe_id) $socid=$user->societe_id;

$object = new Societe($db);
foreach ($_POST as $key=>$value) { // Generic way to fill all the fields to the object (particularly useful for triggers and customfields)
    $object->$key = $value;
}

// Get object canvas (By default, this is not defined, so standard usage of dolibarr)
$object->getCanvas($socid);
$canvas = $object->canvas?$object->canvas:GETPOST("canvas");
if (! empty($canvas))
{
    require_once(DOL_DOCUMENT_ROOT."/core/class/canvas.class.php");
    $objcanvas = new Canvas($db, $action);
    $objcanvas->getCanvas('thirdparty', 'card', $canvas);
}

// Security check
$result = restrictedArea($user, 'societe', $socid, '', '', '', '', $objcanvas);

// Initialize technical object to manage hooks of thirdparties. Note that conf->hooks_modules contains array array
//include_once(DOL_DOCUMENT_ROOT.'/core/class/hookmanager.class.php');
//$hookmanager=new HookManager($db);
//$hookmanager->callHooks(array('thirdpartyblocking'));

/*******************************************************************
 * ACTIONS
 ********************************************************************/

if ($_POST["action"] == 'update' && ! $_POST["cancel"] )
{
    $object->fetch($socid);
    //update
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    $currentmodule = "societe";
    $customfields = new CustomFields($db, $currentmodule, $customfields_table);
    $upd = $customfields->create($object, 0, 1);
     
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

if ($socid > 0)
{
    $object->fetch($socid);

    /*
     * Affichage onglets
     */
    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($object);
    dol_fiche_head($head, 'customerblocking', $langs->trans("ThirdParty"),0,'company');

    if ($action == 'edit')
    {
        print '<form action="customerblocking.php?socid='.$socid.'" method="post">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="id" value="'.$socid.'">';

    }

    print '<table class="border" width="100%">';
    print '<tr><td>'.$langs->trans('Name').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($object,'socid','',1,'rowid','nom');
    print '</td></tr>';

    print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$object->prefix_comm.'</td></tr>';

    if ($object->client)
    {
        print '<tr><td>';
        print $langs->trans('CustomerCode').'</td><td colspan="3">';
        print $object->code_client;
        if ($object->check_codeclient() <> 0) print ' <font class="error">('.$langs->trans("WrongCustomerCode").')</font>';
        print '</td></tr>';
    }

    if ($object->fournisseur)
    {
        print '<tr><td>';
        print $langs->trans('SupplierCode').'</td><td colspan="3">';
        print $object->code_fournisseur;
        if ($object->check_codefournisseur() <> 0) print ' <font class="error">('.$langs->trans("WrongSupplierCode").')</font>';
        print '</td></tr>';
    }

    // Insert hooks
    //    $parameters=array();
    //    $reshook=$hookmanager->executeHooks('formObjectOptions',$parameters,$object,$action);    // Note that $action and $object may have been modified by hook
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    include_once(DOL_DOCUMENT_ROOT.'/customfields/lib/customfields.lib.php');
    $currentmodule = "societe";
    $customfields = new CustomFields($db, $currentmodule, $customfields_table);
    $rights = 1;
    $idvar = "socid";
    if($action == "edit"){
        customfields_print_creation_form($currentmodule, $socid,$customfields_table);
    }else{
        customfields_print_main_form($currentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    }
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
        print '<a class="butAction" href="'.DOL_URL_ROOT.'/customerblocking/customerblocking.php?action=edit&amp;socid='.$socid.'">'.$langs->trans("Editer les parametres de blocage").'</a>';
    }

    print "\n</div>\n";
}

if ($mesg)
{
    print $mesg;
}

/* ************************************************************************** */
/*                                                                            */
/* Log			                                                              */
/*                                                                            */
/* ************************************************************************** */
if ($socid > 0)
{
    print "</br>";
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    include_once(DOL_DOCUMENT_ROOT.'/customfields/lib/customfields.lib.php');
    $currentmodule = "societe";
    $customfields = new CustomFields($db, $currentmodule, $customfields_table);
    $rights = 1;
    $idvar = "socid";
    customfields_print_log($currentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    
}

// End of page
$db->close();
llxFooter();


?>
