<?php
/* Copyright (C) 2011      Juanjo Menent	    <jmenent@2byte.es>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *	\file       htdocs/admin/contract.php
 *	\ingroup    contract
 *	\brief      Setup page of module Contracts
 *	\version    $Id$
 */

require("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/contrat/class/contrat.class.php');

$langs->load("admin");
$langs->load("bills");
$langs->load("other");
$langs->load("contracts");

if (!$user->admin)
accessforbidden();

/*
 * Actions
 */
if ($_POST["action"] == 'updateMask')
{
	$maskconst=$_POST['maskconstcontract'];
	$maskvalue=$_POST['maskcontract'];
	if ($maskconst) dolibarr_set_const($db,$maskconst,$maskvalue,'chaine',0,'',$conf->entity);
}

if ($_GET["action"] == 'setmod')
{
	dolibarr_set_const($db, "CONTRACT_ADDON",$_GET["value"],'chaine',0,'',$conf->entity);
}

// constants of magre model
if ($_POST["action"] == 'updateMatrice') dolibarr_set_const($db, "CONTRACT_NUM_MATRICE",$_POST["matrice"],'chaine',0,'',$conf->entity);
if ($_POST["action"] == 'updatePrefix') dolibarr_set_const($db, "CONTRACT_NUM_PREFIX",$_POST["prefix"],'chaine',0,'',$conf->entity);
if ($_POST["action"] == 'setOffset') dolibarr_set_const($db, "CONTRACT_NUM_DELTA",$_POST["offset"],'chaine',0,'',$conf->entity);
if ($_POST["action"] == 'setNumRestart') dolibarr_set_const($db, "CONTRACT_NUM_RESTART_BEGIN_YEAR",$_POST["numrestart"],'chaine',0,'',$conf->entity);

/*
 * View
 */
llxHeader();

$dir=DOL_DOCUMENT_ROOT."/includes/modules/contract/";
$html=new Form($db);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("ContractsSetup"),$linkback,'setup');

print "<br>";

print_titre($langs->trans("ContractsNumberingModules"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td width="100">'.$langs->trans("Name").'</td>';
print '<td>'.$langs->trans("Description").'</td>';
print '<td>'.$langs->trans("Example").'</td>';
print '<td align="center" width="60">'.$langs->trans("Status").'</td>';
print '<td align="center" width="16">'.$langs->trans("Infos").'</td>';
print "</tr>\n";

clearstatcache();

$dir = "../includes/modules/contract/";
$handle = opendir($dir);
if (is_resource($handle))
{
	$var=true;

	while (($file = readdir($handle))!==false)
	{
		if (substr($file, 0, 13) == 'mod_contract_' && substr($file, dol_strlen($file)-3, 3) == 'php')
		{
			$file = substr($file, 0, dol_strlen($file)-4);

			require_once(DOL_DOCUMENT_ROOT ."/includes/modules/contract/".$file.".php");

			$module = new $file;

			// Show modules according to features level
			if ($module->version == 'development'  && $conf->global->MAIN_FEATURES_LEVEL < 2) continue;
			if ($module->version == 'experimental' && $conf->global->MAIN_FEATURES_LEVEL < 1) continue;

			if ($module->isEnabled())
			{
				$var=!$var;
				print '<tr '.$bc[$var].'><td>'.$module->nom."</td>\n";
				print '<td>';
				print $module->info();
				print '</td>';

				// Examples
				print '<td nowrap="nowrap">'.$module->getExample()."</td>\n";

				print '<td align="center">';
				if ($conf->global->CONTRACT_ADDON == "$file")
				{
					print img_picto($langs->trans("Activated"),'on');
				}
				else
				{
					print '<a href="'.$_SERVER["PHP_SELF"].'?action=setmod&amp;value='.$file.'">';
					print img_picto($langs->trans("Disabled"),'off');
					print '</a>';
				}
				print '</td>';

				$contract=new Contrat($db);
				$contract->initAsSpecimen();

				// Info
				$htmltooltip='';
				$htmltooltip.=''.$langs->trans("Version").': <b>'.$module->getVersion().'</b><br>';
				$facture->type=0;
				$nextval=$module->getNextValue($mysoc,$contract);
				if ("$nextval" != $langs->trans("NotAvailable"))	// Keep " on nextval
				{
					$htmltooltip.=''.$langs->trans("NextValue").': ';
					if ($nextval)
					{
						$htmltooltip.=$nextval.'<br>';
					}
					else
					{
						$htmltooltip.=$langs->trans($module->error).'<br>';
					}
				}

				print '<td align="center">';
				print $html->textwithpicto('',$htmltooltip,1,0);
				print '</td>';

				print '</tr>';
			}
		}
	}
	closedir($handle);
}

print '</table><br>';
$db->close();

llxFooter('$Date$ - $Revision$');
?>