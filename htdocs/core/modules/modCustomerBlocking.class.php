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
 * 		\defgroup   customerblocking     Module customerblocking
 *      \brief      Module used to define customers restrictions
 *      \file       htdocs/includes/modules/modCustomerBlocking.class.php
 *      \ingroup    customerblocking
 *      \brief      Description and activation file for Customer Blocking module
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modCustomerBlocking
 *      \brief      Description and activation class for module CustomerBlocking
 */
class modCustomerBlocking extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$DB      Database handler
	 */
	function modCustomerBlocking($DB)
	{
        global $langs,$conf;

        $this->db = $DB;

		$this->numero = 1335;
		$this->family = "crm";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Description of module CustomerBlocking";
		$this->version = "1.1";//'dolibarr';
		$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		$this->special = 0;
		
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'
		$this->picto='company';

		// Defined if the directory /mymodule/includes/triggers/ contains triggers or not
		$this->triggers = 0;

		// Data directories to create when module is enabled.
		$this->dirs = array();
		$r=0;

		//$this->style_sheet = '/mymodule/mymodule.css.php';

		// Config pages. Put here list of php page names stored in admmin directory used to setup module.
		//$this->config_page_url = array("mymodulesetuppage.php");

		// Dependencies
		//TODO: fill depends array with proper values ("makinalib, etc...)
		$this->depends = array('customfields');		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array();	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("customerblocking@customerblocking");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		//                             2=>array('MAIN_MODULE_MYMODULE_HOOKS','chaine','hookcontext1:hookcontext2','To say: This module manage hooks in hookcontext1 and hookcontext2',1,'current',1)
		// );
		$this->const = array();
		
		// Tabs
        $this->tabs = array('thirdparty:+customerblocking:Blocage Client:@customerblocking:$user->rights->customerblocking->read:/customerblocking/customerblocking.php?socid=__ID__');

        // Dictionnaries
        $this->dictionnaries=array(
            'langs'=>'customerblocking@customerblocking',
            'tabname'=>array(MAIN_DB_PREFIX."c_customerblocking"),
            'tablib'=>array("CustomerBlocking"),
            'tabsql'=>array("SELECT t.rowid as rowid, p.libelle as pays, p.code as pays_code, t.code, t.libelle, t.active FROM ".MAIN_DB_PREFIX."c_customerblocking as t,  llx_c_pays as p WHERE t.fk_pays=p.rowid"),
            'tabsqlsort'=>array("code ASC"),
            'tabfield'=>array("code,libelle,pays_id,pays"),
            'tabfieldvalue'=>array("code,libelle,pays"),
            'tabfieldinsert'=>array("code,libelle,fk_pays"),
            'tabrowid'=>array("rowid"),
            'tabcond'=>array($conf->customerblocking->enabled)
        );

        // Boxes
        $this->boxes = array();			// List of boxes

		// Permissions
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'customerblocking';
		$this->rights = array();		// Permission array used by this module
		
		$r=0;
		$this->rights[$r][0] = 99051;
		$this->rights[$r][1] = 'Lire les informations de blocage clients';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';
		
		$r++;
		$this->rights[$r][0] = 99052;
		$this->rights[$r][1] = 'Modifier le status de blocage des clients';
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
		$sql = array("DROP TABLE ".MAIN_DB_PREFIX."societe_customerblocking");
		return $this->_remove($sql);
	}


	/**
	 *		Create tables, keys and data required by customerblockinge module
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/customerblocking/sql/')    ;
	}
	
	function initCustomFields(){
	    require_once(DOL_DOCUMENT_ROOT."/customfields/lib/customfields.lib.php");
	    require_once(DOL_DOCUMENT_ROOT."/customfields/class/customfields.class.php");
	    $customfields = new CustomFields($this->db, "societe", "customerblocking");
        $customfields->initCustomFields();
        $fieldname ="libelle_blockingcode";
        $type = "other";
        $size = "";
        $nulloption = "";
        $defaultvalue = null;
        $constraint = MAIN_DB_PREFIX."c_customerblocking";
        $customtype = null;
        $customdef = null;
        $customsql = null;
        $fieldid = null;
        $notrigger = 0;
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $constraint = null;
        $fieldname ="plafond";
        $type = "int";
        $size = "4";
        $nulloption = 0;
        $defaultvalue = 0.1;
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="depassement";
        $type ="";
        $type = "enum('Yes','No')";
        $size = "";
        $nulloption = 0;
        $defaultvalue = 'No';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="test_weight";
        $type ="";
        $type = "int";
        $size = "";
        $nulloption = 0;
        $defaultvalue = '0';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="test_weight_unit";
        $type ="";
        $type = "int";
        $size = "";
        $nulloption = 0;
        $defaultvalue = '0';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="prodvolume";
        $type ="";
        $type = "int";
        $size = "";
        $nulloption = 0;
        $defaultvalue = '0';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="prodvolume_unit";
        $type ="";
        $type = "int";
        $size = "";
        $nulloption = 0;
        $defaultvalue = '0';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        
        $fieldname ="logdate_auto";
        $constraint = null;
        $type ="date";
        $size = "";
        $nulloption = 0;
        $defaultvalue = '';
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
      
        $fieldname ="login_author_auto";
        $constraint = MAIN_DB_PREFIX."user";
        $type ="other";
        $size = "";
        $nulloption = 0;
        $defaultvalue = null;
        $customfields->addCustomField($fieldname, $type, $size, $nulloption, $defaultvalue, $constraint, $customtype, $customdef, $customsql, $fieldid, $notrigger);
	}
	
}

?>
