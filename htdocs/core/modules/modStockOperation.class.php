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
 * 		\defgroup   facture     Module StockOperation
 *      \brief      Module used execute stock opeartion
 *      \file       htdocs/includes/modules/modStockOperation.class.php
 *      \ingroup    facture
 *      \brief      Description and activation file for Stock Operation
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modStockOperation
 *      \brief      Description and activation class for StockOperation
 */
class modStockOperation extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$DB      Database handler
	 */
	function modStockOperation($DB)
	{
        global $langs,$conf;

        $this->db = $DB;

		$this->numero = 1772;
		$this->family = "products";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Description of module StockOperation";
		$this->version = 'dolibarr';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='picto@stockoperation';

		$this->triggers = 0;

		// Data directories to create when module is enabled.
		$this->dirs = array();
		$r=0;


		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		$this->config_page_url = array("stockoperation.php");

		// Dependencies
		//TODO: fill depends array with proper values ("makinalib, etc...)
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("stockoperation@stockoperation");

		// Tabs
        $this->tabs = array();

        // Dictionnaries
        $this->dictionnaries=array();

        // Boxes
        $this->boxes = array();			// List of boxes

		// Permissions
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'stockoperation';
		$this->rights = array();		// Permission array used by this module
		
		$r=0;
		$this->rights[$r][0] = 97781;
		$this->rights[$r][1] = 'Lire les operations stock';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'lire';
		
		$r++;
		$this->rights[$r][0] = 97758;
		$this->rights[$r][1] = 'Modifier les operations stock';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'creer';
		
		$r++;
		$this->rights[$r][0] = 97759;
		$this->rights[$r][1] = 'Supprimer les operations stock';
		$this->rights[$r][2] = 'w';
		$this->rights[$r][3] = 0;
		$this->rights[$r][4] = 'supprimer';
		
		
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
		//$this->initCustomFields();
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
		$sql = array();//"DROP TABLE ".MAIN_DB_PREFIX."societe_stockentry_line","DROP TABLE ".MAIN_DB_PREFIX."societe_stockentry");

		return $this->_remove($sql);
	}


	/**
	 *		Create tables, keys and data required by stockoperation module
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/stockoperation/sql/');
	}
	
	function initCustomFields(){
	    require_once(DOL_DOCUMENT_ROOT."/customfields/lib/customfields.lib.php");
	    require_once(DOL_DOCUMENT_ROOT."/customfields/class/customfields.class.php");
	    $customfields = new CustomFields($this->db, "societe", "stockentry");
	    $customfields->initCustomFields();
	     
	    $type = "date";
	    $size = "";
	    $nulloption = 0;
	    $defaultvalue = null;
	    $constraint = null;
	    $customtype = null;
	    $customdef = null;
	    $customsql = null;
	    $fieldid = null;
	    $notrigger = 0;
	    
	    $fieldname ="date_entree";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	
	    $nulloption = 1;
	    $fieldname ="date_validation";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $type = 'varchar';
	    $size= '32';
	    $fieldname ="numerodossier";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	
	    $fieldname ="transport";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	
	    $fieldname ="numeroplomb";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="numeroconteneur";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);

	    $fieldname ="ref_ext1";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
	    $fieldname ="ref_ext2";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    
	    $type ='text';
	    $size = '';
	    $fieldname ="note";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="marchandise_description";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $nulloption = 0;
	    $type = 'enum(\'valeur\',\'volume\',\'volumeproduct\',\'taxproduct\')';
	    $fieldname ="mode_calcul";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $nulloption = 1;
	    $type = 'float';
	    $size = '';
	    $fieldname ="coeff_rev_global";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
	    //entries (lines)
	    $customfields = new CustomFields($this->db, "stockentry", "line");
	    $customfields->initCustomFields();
	    
	    $fieldname = "ref_ext_facture_fourn";
	    $type = 'int';
	    $size = "11";
	    $nulloption = 0;
	    $defaultvalue = '';
	    $constraint = MAIN_DB_PREFIX."facture_fournisseur";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $nulloption = 1;
	    $defaultvalue = null;
	    $constraint = null;
	    $type = 'varchar';
	    $size = '32';
	    $fieldname ="numerofacture_externe";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="date_facture_fourn";
	    $size = '';
	    $type = 'date';
	    $constraint=null;
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="date_echeance_facture_fourn";
	    $type = 'date';
	    $constraint=null;
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
	    $nulloption = 0;
	    $defaultvalue = '00';
	    $type = 'double';
	    $fieldname ="total_ht_facture";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="total_ttc_facture";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="coeff_ht";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="coeff_ttc";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $nulloption = 1;
	    $defaultvalue = '';
	    $fieldname ="daom";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $type = 'varchar';
	    $size= '32';
	    $fieldname ="numeroconteneur";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $size = '1';
	    $fieldname ="code_list_control";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="code_selection";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $fieldname ="repartition";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
        $fieldname = 'lieu_stock';	     
        $type = 'int';
        $size = "11";
        $nulloption = 0;
        $defaultvalue = '';
        $constraint = MAIN_DB_PREFIX."entrepot";
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);

        $fieldname = 'nom_fk_societe';
        $constraint = MAIN_DB_PREFIX."societe";
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
        
        $nulloption = 0;
        $defaultvalue = '00';
        
        $type = 'double';
        $size = '';
        $fieldname ="volume";
        $constraint = null;
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
        
        $fieldname ="weight";
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
        
        $type = "int";
        $size='4';
        $defaultvalue = '-3';
        $fieldname ="weight_unit";
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
        $fieldname ="volume_unit";
        $defaultvalue = '00';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
        
        $size = '';
        $defaultvalue = '';
        $nulloption=0;
        
        $type = 'enum(\'Frais\',\'Marchandise\',\'Douane\')';
	    $fieldname ="type_facture";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
	    
	    $type = 'enum(\'valeur\',\'volume\',\'volumeproduct\',\'taxproduct\')';
	    $fieldname ="mode_calcul";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $type = 'enum(\'no\',\'yes\')';
	    $fieldname ="accounting";
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	    
	    $nulloption=1;
	    $type = 'text';
	    $fieldname ="note";
	    $size='';
	    $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	     
	}
}

?>