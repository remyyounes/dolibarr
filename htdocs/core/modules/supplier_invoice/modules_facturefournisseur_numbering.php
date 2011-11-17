<?php
/* Copyright (C) 2003-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2005 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Eric Seigne          <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2011      Philippe Grand       <philippe.grand@atoo-net.com>
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
 *		\file       htdocs/core/modules/supplier_invoice/modules_facturefournisseur_numbering.php
 *      \ingroup    invoice
 *      \brief      File that contain parent class for supplier invoice numbering models
 */
require_once(DOL_DOCUMENT_ROOT."/core/class/commondocgenerator.class.php");
require_once(DOL_DOCUMENT_ROOT."/compta/bank/class/account.class.php");	// requis car utilise par les classes qui heritent

/**
 *	\class      ModeleNumRefSuppliersInvoices
 *	\brief      Classe mere des modeles de numerotation des references de commandes fournisseurs
 */
abstract class ModeleNumRefSuppliersInvoices
{
	var $error='';

	/**  Return if a module can be used or not
	 *
	 *   @return		boolean     true if module can be used
	 */
	function isEnabled()
	{
		return true;
	}

	/**  Renvoie la description par defaut du modele de numerotation
	 *
	 *   @return     string      Texte descripif
	 */
	function info()
	{
		global $langs;
		$langs->load("orders");
		return $langs->trans("NoDescription");
	}

	/**   Renvoie un exemple de numerotation
	 *
	 *    @return     string      Example
	 */
	function getExample()
	{
		global $langs;
		$langs->load("orders");
		return $langs->trans("NoExample");
	}

	/**  Test si les numeros deja en vigueur dans la base ne provoquent pas de conflits qui empecheraient cette numerotation de fonctionner.
	 *
	 *   @return     boolean     false si conflit, true si ok
	 */
	function canBeActivated()
	{
		return true;
	}

	/**  Renvoie prochaine valeur attribuee
	 *
	 *   @return     string      Valeur
	 */
	function getNextValue()
	{
		global $langs;
		return $langs->trans("NotAvailable");
	}

	/**   Renvoie version du module numerotation
	 *
	 *    @return     string      Valeur
	 */
	function getVersion()
	{
		global $langs;
		$langs->load("admin");

		if ($this->version == 'development') return $langs->trans("VersionDevelopment");
		if ($this->version == 'experimental') return $langs->trans("VersionExperimental");
		if ($this->version == 'dolibarr') return DOL_VERSION;
		return $langs->trans("NotAvailable");
	}
}
?>
