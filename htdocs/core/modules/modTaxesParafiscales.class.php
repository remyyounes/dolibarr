<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2010 Regis Houssin        <regis@dolibarr.fr>
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
 * 		\defgroup   taxesparafiscales    Module taxesparafiscales
 *      \brief      Module used to apply parafiscal taxes
 *      \file       htdocs/includes/modules/modTaxesParafiscales.class.php
 *      \ingroup    taxesparafiscales
 *      \brief      Description and activation file for Parafiscal Taxes module 
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modTaxesParafiscales
 *      \brief      Description and activation class for module TaxesParafiscales
 */
class modTaxesParafiscales extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$DB      Database handler
	 */
	function modTaxesParafiscales($DB)
	{
        global $langs,$conf;

        $this->db = $DB;

		$this->numero = 1344;
		$this->family = "products";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Description of module TaxesParafiscales";
		$this->version = "1.1";//'dolibarr';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='bill';

		// Defined if the directory /mymodule/includes/triggers/ contains triggers or not
		$this->triggers = 1;

		// Data directories to create when module is enabled.
		$this->dirs = array();
		$r=0;

		//$this->style_sheet = '/mymodule/mymodule.css.php';

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		//$this->config_page_url = array("mymodulesetuppage.php");

		// Dependencies
		$this->depends = array('modTaxe','modCustomFields');		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("taxesparafiscales@taxesparafiscales");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		//                             2=>array('MAIN_MODULE_MYMODULE_HOOKS','chaine','hookcontext1:hookcontext2','To say: This module manage hooks in hookcontext1 and hookcontext2',1,'current',1)
		// );
		$this->const = array(0=>array('MAIN_MODULE_TaxesParafiscales_HOOKS','chaine','produitdao','This module manage hooks in productdao',1,'current',1));
		
		// Tabs
        $this->tabs = array('product:+taxesparafiscales:Taxes Parafiscales:@taxesparafiscales:$user->rights->taxesparafiscales->read:/taxesparafiscales/taxesparafiscales.php?id=__ID__');

        // Dictionnaries
        $this->dictionnaries=array();

        // Boxes
        $this->boxes = array();			// List of boxes

		// Permissions
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'taxesparafiscales';
		$this->rights = array();		// Permission array used by this module
		
		$r=0;
		$this->rights[$r][0] = 99071;
		$this->rights[$r][1] = 'Lire les informations liees aux taxes parafiscales';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';
		
		$r++;
		$this->rights[$r][0] = 99072;
		$this->rights[$r][1] = 'Modifier la taxation parafiscales';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'write';
		

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
		$this->initCustomFields();
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
		$sql = array("DROP TABLE ".MAIN_DB_PREFIX."product_taxe");
        return $this->_remove($sql);
	}


	/**
	 *		Create tables, keys and data required by taxe module
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('');
	}
	
	function initCustomFields(){
	    require_once(DOL_DOCUMENT_ROOT."/customfields/lib/customfields.lib.php");
	    require_once(DOL_DOCUMENT_ROOT."/customfields/class/customfields.class.php");
	    $customfields = new CustomFields($this->db, "product", "taxe");
	    $customfields->initCustomFields();
	    
	    $type = "other";
	    $size = "";
	    $nulloption = "";
	    $defaultvalue = null;
	    $constraint = MAIN_DB_PREFIX."c_taxe";
	    $customtype = null;
	    $customdef = null;
	    $customsql = null;
	    $fieldid = null;
	    $notrigger = 0;
	    
	    $fieldname ="libelle_ctaom";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	
	    $fieldname ="libelle_ctax1";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	
	    $fieldname ="libelle_ctax2";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	}
}

?>