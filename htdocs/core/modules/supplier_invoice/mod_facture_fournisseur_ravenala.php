<?php
/* Copyright (C) 2003-2007 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *	\file       htdocs/core/modules/supplier_order/mod_facture_fournisseur_ravenala.php
 *	\ingroup    facture
 *	\brief      Fichier contenant la classe du modele de numerotation de reference de facture fournisseur Ravenala
 */

require_once(DOL_DOCUMENT_ROOT ."/core/modules/supplier_invoice/modules_facturefournisseur_numbering.php");


/**
	\class      mod_facture_fournisseur_ravenala
	\brief      Classe du modele de numerotation de reference de facture fournisseur Ravenala
*/
class mod_facture_fournisseur_ravenala extends ModeleNumRefSuppliersInvoices
{
	var $version='dolibarr';		// 'development', 'experimental', 'dolibarr'
	var $error = '';
	var $nom = 'Ravenala';


    /**     \brief      Renvoi la description du modele de numerotation
     *      \return     string      Texte descripif
     */
	function info()
    {
    	global $conf,$langs;

		$langs->load("propal");
		$langs->load("admin");

		$form = new Form($db);

		$texte = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$texte.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$texte.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$texte.= '<input type="hidden" name="action" value="updateMask">';
		$texte.= '<input type="hidden" name="maskconstsupplierinvoice" value="COMMANDE_FOURNISSEUR_RAVENALA_MASK">';
		$texte.= '<table class="nobordernopadding" width="100%">';

		$tooltip=$langs->trans("GenericMaskCodes",$langs->transnoentities("Order"));
		$tooltip.=$langs->trans("GenericMaskCodes2");
		$tooltip.=$langs->trans("GenericMaskCodes3");
		$tooltip.=$langs->trans("GenericMaskCodes4a",$langs->transnoentities("Order"),$langs->transnoentities("Order"));
		$tooltip.=$langs->trans("GenericMaskCodes5");

		// Parametrage du prefix
		$texte.= '<tr><td>'.$langs->trans("Mask").':</td>';
		$texte.= '<td align="right">'.$form->textwithpicto('<input type="text" class="flat" size="24" name="masksupplierinvoice" value="'.$conf->global->COMMANDE_FOURNISSEUR_RAVENALA_MASK.'">',$tooltip,1,1).'</td>';

		$texte.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';

		$texte.= '</tr>';

		$texte.= '</table>';
		$texte.= '</form>';

		return $texte;
    }

    /**     \brief      Renvoi un exemple de numerotation
     *      \return     string      Example
     */
    function getExample()
    {
    	global $conf,$langs,$mysoc;

    	$old_code_client=$mysoc->code_client;
    	$mysoc->code_client='CCCCCCCCCC';
    	$numExample = $this->getNextValue($mysoc,'');
		$mysoc->code_client=$old_code_client;

		if (! $numExample)
		{
			$numExample = $langs->trans('NotConfigured');
		}
		return $numExample;
    }

	/**		\brief      Return next value
	*      	\param      objsoc      Object third party
	*      	\param      facture	Object supplier order
	*      	\return     string      Value if OK, 0 if KO
	*/
    function getNextValue($objsoc=0,$facture='')
    {
		global $db,$conf;

		require_once(DOL_DOCUMENT_ROOT ."/core/lib/functions2.lib.php");

		// On defini critere recherche compteur
		$mask=$conf->global->COMMANDE_FOURNISSEUR_RAVENALA_MASK;

		if (! $mask)
		{
			$this->error='NotConfigured';
			return 0;
		}

		$numFinal=get_next_value($db,$mask,'facture_fourn','ref_ext','',$objsoc->code_fournisseur,$facture->datec);

		return  $numFinal;
	}


    /**     \brief      Renvoie la reference de facture suivante non utilisee
     *      \param      objsoc      Objet societe
     *      \param      facture		Objet facture
     *      \return     string      Texte descripif
     */
    function facture_get_num($objsoc=0,$facture='')
    {
        return $this->getNextValue($objsoc,$facture);
    }
}

?>