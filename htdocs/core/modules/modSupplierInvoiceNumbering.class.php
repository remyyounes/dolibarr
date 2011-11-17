<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2011	   Remy Younes			<ryounes@gmail.com>
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
 * 		\defgroup   facture     Module SupplierInvoice
 *      \brief      Module used to define a numbering system for supplier invoices
 *      \file       htdocs/includes/modules/modSupplierInvoiceNumbering.class.php
 *      \ingroup    facture
 *      \brief      Description and activation file for Supplier Invoice Numbering 
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modSupplierInvoiceNumbering
 *      \brief      Description and activation class for SupplierInvoiceNumbering
 */
class modSupplierInvoiceNumbering extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$DB      Database handler
	 */
	function modSupplierInvoiceNumbering($DB)
	{
        global $langs,$conf;

        $this->db = $DB;

		$this->numero = 1771;
		$this->family = "products";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Description of module Supplierinvoicenumbering";
		$this->version = 'dolibarr';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='picto@supplierinvoicenumbering';

		$this->triggers = 0;

		// Data directories to create when module is enabled.
		$this->dirs = array();
		$r=0;


		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("supplierinvoicenumbering.php");

		// Dependencies
		//TODO: fill depends array with proper values ("makinalib, etc...)
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("supplierinvoicenumbering@supplierinvoicenumbering");

		// Tabs
        $this->tabs = array();

        // Dictionnaries
        $this->dictionnaries=array();

        // Boxes
        $this->boxes = array();			// List of boxes

		// Permissions
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'supplierinvoicenumbering';
		$this->rights = array();		// Permission array used by this module
		

		// Main menu entries
		$this->menus = array();			// List of menus to add

	}

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		$sql = array();

		$result=$this->load_tables();

		return $this->_init($sql);
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql);
	}


	/**
	 *		Create tables, keys and data required by supplierinvoicenumbering module
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('');
	}
}

?>