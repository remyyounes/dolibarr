<?php
/* Copyright (C) 2005      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2005-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2011 Regis Houssin        <regis@dolibarr.fr>
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
 * or see http://www.gnu.org/
 */

/**
 *  \file       htdocs/core/modules/expedition/doc/ModelePdfExpedition.class.php
 *  \ingroup    shipping
 *  \brief      Fichier contenant la classe mere de generation des expeditions
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commondocgenerator.class.php");


/**
 *  \class      ModelePdfExpedition
 *  \brief      Parent class of sending receipts models
 */
abstract class ModelePdfExpedition extends CommonDocGenerator
{
    var $error='';


	/**
	 *      \brief      Return list of active generation modules
	 * 		\param		$db		Database handler
	 */
	function liste_modeles($db)
	{
		global $conf;

		$type='shipping';
		$liste=array();

		include_once(DOL_DOCUMENT_ROOT.'/core/lib/functions2.lib.php');
		$liste=getListOfModels($db,$type,'');

		return $liste;
	}
}


/**
 * 	Cree un bon d'expedition sur disque
 * 	@param	    db  			objet base de donnee
 * 	@param	    object			object expedition
 * 	@param	    modele			force le modele a utiliser ('' to not force)
 * 	@param		outputlangs		objet lang a utiliser pour traduction
 *  @return     int             <=0 if KO, >0 if OK
 */
function expedition_pdf_create($db, $object, $modele, $outputlangs)
{
	global $conf,$langs;

	$langs->load("sendings");

	// Increase limit for PDF build
	$err=error_reporting();
	error_reporting(0);
	@set_time_limit(120);
	error_reporting($err);

	$dir = "/core/modules/expedition/";
	$srctemplatepath='';

	// Positionne le modele sur le nom du modele a utiliser
	if (! dol_strlen($modele))
	{
	    if (! empty($conf->global->EXPEDITION_ADDON_PDF))
	    {
	        $modele = $conf->global->EXPEDITION_ADDON_PDF;
	    }
	    else
	    {
	        $modele = 'rouget';
	    }
	}

	// If selected modele is a filename template (then $modele="modelname:filename")
	$tmp=explode(':',$modele,2);
	if (! empty($tmp[1]))
	{
	    $modele=$tmp[0];
	    $srctemplatepath=$tmp[1];
	}

	// Search template file
	$file=''; $classname=''; $filefound=0;
	foreach(array('doc','pdf') as $prefix)
	{
	    $file = $prefix."_expedition_".$modele.".modules.php";

	    // On verifie l'emplacement du modele
	    $file = dol_buildpath($dir.'doc/'.$file);

	    if (file_exists($file))
	    {
	        $filefound=1;
	        $classname=$prefix.'_expedition_'.$modele;
	        break;
	    }
	}

	// Charge le modele
	if ($filefound)
	{
	    require_once($file);

		$obj = new $classname($db);

		$result=$object->fetch_origin();

		// We save charset_output to restore it because write_file can change it if needed for
		// output format that does not support UTF8.
		$sav_charset_output=$outputlangs->charset_output;
		if ($obj->write_file($object, $outputlangs) > 0)
		{
			$outputlangs->charset_output=$sav_charset_output;

			// we delete preview files
        	//require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
			//dol_delete_preview($object);
			return 1;
		}
		else
		{
			$outputlangs->charset_output=$sav_charset_output;
			dol_syslog("Erreur dans expedition_pdf_create");
			dol_print_error($db,$obj->error);
			return 0;
		}
	}
	else
	{
		dol_print_error('',$langs->trans("Error")." ".$langs->trans("ErrorFileDoesNotExists",$dir.$file));
		return -1;
    }
}

?>
