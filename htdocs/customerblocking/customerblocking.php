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
 *   	\file       customerblocking/customerblocking.php
 *		\ingroup    customerblocking
 *		\brief      This file is the tab page for the customerblocking module
 *		\version    $Id: customerblocking.php,v 1.0 2011/11/14 11:28:12 remyyounes Exp $
 *		\author		Remy Younes
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/company.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/functions.lib.php");
require_once(DOL_DOCUMENT_ROOT."/user/class/user.class.php");

$module = 'customerblocking';
$langs->load("companies");
$langs->load($module.'@'.$module);
$action		= (GETPOST('action') ? GETPOST('action') : 'view');
$confirm	= GETPOST('confirm');
$socid		= GETPOST("socid");
$parentmodule = "societe";
$customfields_table = "customerblocking";

if ($user->societe_id) $socid=$user->societe_id;

$object = new Societe($db);
foreach ($_POST as $key=>$value) { // Generic way to fill all the fields to the object (particularly useful for triggers and customfields)
    $object->$key = $value;
}

// Security check
$result = restrictedArea($user, 'societe', $socid);

/*******************************************************************
 * ACTIONS
 ********************************************************************/

if ($_POST["action"] == 'update' && ! $_POST["cancel"] )
{
    $object->fetch($socid);
    //update
    include_once(DOL_DOCUMENT_ROOT.'/customfields/class/customfields.class.php');
    $customfields = new CustomFields($db, $parentmodule, $customfields_table);
    $upd = $customfields->create($object,0,1);
     
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

    if ($conf->notification->enabled) $langs->load("mails");

    $head = societe_prepare_head($object);
    dol_fiche_head($head, 'customerblocking', $langs->trans("ThirdParty"),0,'company');

    if ($action == 'edit')
    {
        print '<form action="'.$module.'.php?socid='.$socid.'" method="post">';
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="update">';
        print '<input type="hidden" name="id" value="'.$socid.'">';

    }

    // En mode visu
    print '<table class="border" width="100%">';
    
    // Name
    print '<tr><td>'.$langs->trans('Name').'</td>';
    print '<td colspan="3">';
    print $form->showrefnav($object,'socid','',1,'rowid','nom');
    print '</td></tr>';

    // Prefix
    print '<tr><td>'.$langs->trans('Prefix').'</td><td colspan="3">'.$object->prefix_comm.'</td></tr>';

    // Tier code
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
        print '<a class="butAction" href="'.DOL_URL_ROOT.'/'.$module.'/'.$module.'.php?action=edit&amp;socid='.$socid.'">'.$langs->trans("Editer les parametre de blocage").'</a>';
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
    $customfields = new CustomFields($db, $parentmodule, $customfields_table);
    $rights = 1;
    $idvar = "socid";
    customfields_print_log($parentmodule, $object, $action, $user, $idvar, $rights, $customfields_table);
    
}

// End of page
$db->close();
llxFooter();

?>
