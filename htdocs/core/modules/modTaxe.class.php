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
 * 		\defgroup   taxe     Module taxe
 *      \brief      Module used to define extra taxes needed by 3rd party tax modules
 *      \file       htdocs/includes/modules/modTaxe.class.php
 *      \ingroup    taxe
 *      \brief      Description and activation file for Taxe module 
 */
include_once(DOL_DOCUMENT_ROOT ."/core/modules/DolibarrModules.class.php");


/**
 * 		\class      modTaxe
 *      \brief      Description and activation class for module Taxe
 */
class modTaxe extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$DB      Database handler
	 */
	function modTaxe($DB)
	{
        global $langs,$conf;

        $this->db = $DB;

		$this->numero = 1331;
		$this->family = "products";
		$this->name = preg_replace('/^mod/i','',get_class($this));
		$this->description = "Description of module Taxe";
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
		//TODO: fill depends array with proper values ("makinalib, etc...)
		$this->depends = array();		// List of modules id that must be enabled if this module is enabled
		$this->requiredby = array('modTaxesParafiscales','modTaxesAlcool');	// List of modules id to disable if this one is disabled
		$this->phpmin = array(5,0);					// Minimum version of PHP required by module
		$this->need_dolibarr_version = array(3,0);	// Minimum version of Dolibarr required by module
		$this->langfiles = array("taxe@taxe");

		// Constants
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0)
		//                             2=>array('MAIN_MODULE_MYMODULE_HOOKS','chaine','hookcontext1:hookcontext2','To say: This module manage hooks in hookcontext1 and hookcontext2',1,'current',1)
		// );
		$this->const = array(0=>array('MAIN_MODULE_TAXE_HOOKS','chaine','admin','This module manage hooks in the dictionary management page',1,'current',1));
		
		// Ta
        $this->tabs = array();

        // Dictionnaries
        $this->dictionnaries=array(
            'langs'=>'taxe@taxe',
            'tabname'=>array(MAIN_DB_PREFIX."c_taxe"),
            'tablib'=>array("Taxe"),
            'tabsql'=>array("SELECT t.rowid as rowid, p.libelle as pays, p.code as pays_code, t.code, t.libelle, t.taxetype, t.active,t.mont, t.cdouane FROM ".MAIN_DB_PREFIX."c_taxe as t,  llx_c_pays as p WHERE t.fk_pays=p.rowid"),
            'tabsqlsort'=>array("code ASC"),
            'tabfield'=>array("code,libelle,pays_id,pays,taxetype,mont,cdouane"),
            'tabfieldvalue'=>array("code,libelle,pays,taxetype,mont,cdouane"),
            'tabfieldinsert'=>array("code,libelle,fk_pays,taxetype,mont,cdouane"),
            'tabrowid'=>array("rowid"),
            'tabcond'=>array($conf->taxe->enabled)
        );

        // Boxes
        $this->boxes = array();			// List of boxes

		// Permissions
		// Key text used to identify module (for permissions, menus, etc...)
		$this->rights_class = 'taxe';
		$this->rights = array();		// Permission array used by this module
		
		$r=0;
		$this->rights[$r][0] = 99021;
		$this->rights[$r][1] = 'Lire le dictionnaire des taxes externes';
		$this->rights[$r][2] = 'r';
		$this->rights[$r][3] = 1;
		$this->rights[$r][4] = 'read';
		
		$r++;
		$this->rights[$r][0] = 99022;
		$this->rights[$r][1] = 'Modifier le dictionnaire des taxes externes';
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
	 *		Create tables, keys and data required by taxe module
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		return $this->_load_tables('/taxe/sql/');
	}
}

?>