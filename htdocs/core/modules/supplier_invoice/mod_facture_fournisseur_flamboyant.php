<?php
/* Copyright (C) 2005-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * or see http://www.gnu.org/
 */

/**
 *    	\file       htdocs/core/modules/supplier_order/mod_facture_fournisseur_flamboyant.php
 *		\ingroup    facture
 *		\brief      Fichier contenant la classe du modele de numerotation de reference de facture fournisseur Muguet
 */

require_once(DOL_DOCUMENT_ROOT ."/core/modules/supplier_invoice/modules_facturefournisseur_numbering.php");


/**	    \class      mod_facture_fournisseur_flamboyant
 *		\brief      Classe du modele de numerotation de reference de facture fournisseur Muguet
 */
class mod_facture_fournisseur_flamboyant extends ModeleNumRefSuppliersInvoices
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error = '';
	var $nom = 'Flamboyant';
	var $prefix='FF';


    /**     \brief      Return description of numbering module
     *      \return     string      Text with description
     */
    function info()
    {
    	global $langs;
      	return $langs->trans("SimpleNumRefModelDesc",$this->prefix);
    }


    /**     \brief      Renvoi un exemple de numerotation
     *      \return     string      Example
     */
    function getExample()
    {
        return $this->prefix."0501-0001";
    }


    /**     \brief      Test si les numeros deja en vigueur dans la base ne provoquent pas de
     *                  de conflits qui empechera cette numerotation de fonctionner.
     *      \return     boolean     false si conflit, true si ok
     */
    function canBeActivated()
    {
    	global $conf,$langs;

        $coyymm=''; $max='';

		$posindice=8;
		$sql = "SELECT MAX(SUBSTRING(ref FROM ".$posindice.")) as max";
        $sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn";
		$sql.= " WHERE ref LIKE '".$this->prefix."____-%'";
        $sql.= " AND entity = ".$conf->entity;
        $resql=$db->query($sql);
        if ($resql)
        {
            $row = $db->fetch_row($resql);
            if ($row) { $coyymm = substr($row[0],0,6); $max=$row[0]; }
        }
        if (! $coyymm || preg_match('/'.$this->prefix.'[0-9][0-9][0-9][0-9]/i',$coyymm))
        {
            return true;
        }
        else
        {
			$langs->load("errors");
			$this->error=$langs->trans('ErrorNumRefModel',$max);
            return false;
        }
    }

    /**     \brief      Return next value
	*      	\param      objsoc      Object third party
	*      	\param      object		Object
	*       \return     string      Valeur
    */
    function getNextValue($objsoc=0,$object='')
    {
        global $db,$conf;

        // D'abord on recupere la valeur max
        $posindice=8;
        $sql = "SELECT MAX(SUBSTRING(ref_ext FROM ".$posindice.")) as max";
        $sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn";
		$sql.= " WHERE ref_ext like '".$this->prefix."____-%'";
        $sql.= " AND entity = ".$conf->entity;

        $resql=$db->query($sql);
        if ($resql)
        {
            $obj = $db->fetch_object($resql);
            if ($obj) $max = intval($obj->max);
            else $max=0;
        }

		//$date=time();
        $date=$object->datec;   // Not always defined
        if (empty($date)) $date=$object->date;  // Creation date is order date for suppliers orders
        $yymm = strftime("%y%m",$date);
        $num = sprintf("%04s",$max+1);

        return $this->prefix.$yymm."-".$num;
    }


    /**     \brief      Renvoie la reference de facture suivante non utilisee
	*      	\param      objsoc      Object third party
	*      	\param      object		Object
    *      	\return     string      Texte descripif
    */
    function facture_get_num($objsoc=0,$object='')
    {
        return $this->getNextValue($objsoc,$object);
    }
}

?>