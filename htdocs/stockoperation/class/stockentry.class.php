<?php
/* Copyright (C) 2007-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) ---Put here your own copyright and developer email---
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
 *      \file       dev/skeletons/stockentry.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2011-11-29 15:24
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/makina/class/commoncustomobject.class.php");


/**
 *      \class      Stockentry
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2011-11-29 15:24
 */
class Stockentry  extends CommonCustomObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='stockentry';			//!< Id that identify managed objects
    protected $table_element = 'stockentry';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_societe;
	var $date_entree='';
	var $date_validation='';
	var $numerodossier;
	var $transport;
	var $numeroplomb;
	var $numeroconteneur;
	var $ref_ext1;
	var $ref_ext2;
	var $note;
	var $marchandise_description;
	var $mode_calcul;
	var $coeff_rev_global;
	protected $templates;
    protected $customfields;
    protected $customTypes= array();

    /**
     *  Constructor
     *
     *  @param      DoliDb		$DB      Database handler
     */
    function Stockentry($DB)
    {
        $this->db = $DB;
        $this->templates = new stdclass;
        $this->templates->datasheet = DOL_DOCUMENT_ROOT."/stockoperation/canvas/tpl/datasheet.tpl.php";
        $this->templates->list = DOL_DOCUMENT_ROOT."/stockoperation/canvas/tpl/list.tpl.php";
        $this->customfields = new CustomFields($this->db, '');
        $this->customfields->moduletable =  MAIN_DB_PREFIX . $this->table_element;
        $this->defineCustomFieldTypes();
        return 1;
    }
    
    function defineCustomFieldTypes(){
        $this->customTypes['fk_societe'] = 'fournisseur';
        return;
    }


    /**
     *  Create object into database
     *
     *  @param      User	$user        User that create
     *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return     int      		   	 <0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->fk_societe)) $this->fk_societe=trim($this->fk_societe);
		if (isset($this->numerodossier)) $this->numerodossier=trim($this->numerodossier);
		if (isset($this->transport)) $this->transport=trim($this->transport);
		if (isset($this->numeroplomb)) $this->numeroplomb=trim($this->numeroplomb);
		if (isset($this->numeroconteneur)) $this->numeroconteneur=trim($this->numeroconteneur);
		if (isset($this->ref_ext1)) $this->ref_ext1=trim($this->ref_ext1);
		if (isset($this->ref_ext2)) $this->ref_ext2=trim($this->ref_ext2);
		if (isset($this->note)) $this->note=trim($this->note);
		if (isset($this->marchandise_description)) $this->marchandise_description=trim($this->marchandise_description);
		if (isset($this->mode_calcul)) $this->mode_calcul=trim($this->mode_calcul);
		if (isset($this->coeff_rev_global)) $this->coeff_rev_global=trim($this->coeff_rev_global);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."stockentry(";
		
		$sql.= "fk_societe,";
		$sql.= "date_entree,";
		$sql.= "date_validation,";
		$sql.= "numerodossier,";
		$sql.= "transport,";
		$sql.= "numeroplomb,";
		$sql.= "numeroconteneur,";
		$sql.= "ref_ext1,";
		$sql.= "ref_ext2,";
		$sql.= "note,";
		$sql.= "marchandise_description,";
		$sql.= "mode_calcul,";
		$sql.= "coeff_rev_global";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_societe)?'NULL':"'".$this->fk_societe."'").",";
		$sql.= " ".(! isset($this->date_entree) || dol_strlen($this->date_entree)==0?'NULL':$this->db->idate($this->date_entree)).",";
		$sql.= " ".(! isset($this->date_validation) || dol_strlen($this->date_validation)==0?'NULL':$this->db->idate($this->date_validation)).",";
		$sql.= " ".(! isset($this->numerodossier)?"'PROV'":"'".$this->db->escape($this->numerodossier)."'").",";
		$sql.= " ".(! isset($this->transport)?'0':"'".$this->db->escape($this->transport)."'").",";
		$sql.= " ".(! isset($this->numeroplomb)?'0':"'".$this->db->escape($this->numeroplomb)."'").",";
		$sql.= " ".(! isset($this->numeroconteneur)?'0':"'".$this->db->escape($this->numeroconteneur)."'").",";
		$sql.= " ".(! isset($this->ref_ext1)?'0':"'".$this->db->escape($this->ref_ext1)."'").",";
		$sql.= " ".(! isset($this->ref_ext2)?'0':"'".$this->db->escape($this->ref_ext2)."'").",";
		$sql.= " ".(! isset($this->note)?'NULL':"'".$this->db->escape($this->note)."'").",";
		$sql.= " ".(! isset($this->marchandise_description)?'NULL':"'".$this->db->escape($this->marchandise_description)."'").",";
		$sql.= " ".(! isset($this->mode_calcul)?'NULL':"'".$this->mode_calcul."'").",";
		$sql.= " ".(! isset($this->coeff_rev_global)?'0':"'".$this->coeff_rev_global."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."stockentry");

			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.

	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_CREATE',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
			}
        }

        // Commit or rollback
        if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::create ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
            return $this->id;
		}
    }


    /**
     *  Load object in memory from database
     *
     *  @param      int	$id    Id object
     *  @return     int          <0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_societe,";
		$sql.= " t.date_entree,";
		$sql.= " t.date_validation,";
		$sql.= " t.numerodossier,";
		$sql.= " t.transport,";
		$sql.= " t.numeroplomb,";
		$sql.= " t.numeroconteneur,";
		$sql.= " t.ref_ext1,";
		$sql.= " t.ref_ext2,";
		$sql.= " t.note,";
		$sql.= " t.marchandise_description,";
		$sql.= " t.mode_calcul,";
		$sql.= " t.coeff_rev_global";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."stockentry as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_societe = $obj->fk_societe;
				$this->date_entree = $this->db->jdate($obj->date_entree);
				$this->date_validation = $this->db->jdate($obj->date_validation);
				$this->numerodossier = $obj->numerodossier;
				$this->transport = $obj->transport;
				$this->numeroplomb = $obj->numeroplomb;
				$this->numeroconteneur = $obj->numeroconteneur;
				$this->ref_ext1 = $obj->ref_ext1;
				$this->ref_ext2 = $obj->ref_ext2;
				$this->note = $obj->note;
				$this->marchandise_description = $obj->marchandise_description;
				$this->mode_calcul = $obj->mode_calcul;
				$this->coeff_rev_global = $obj->coeff_rev_global;

                
            }
            $this->db->free($resql);

            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }


    /**
     *  Update object into database
     *
     *  @param      User	$user        User that modify
     *  @param      int		$notrigger	 0=launch triggers after, 1=disable triggers
     *  @return     int     		   	 <0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;

		// Clean parameters
        
		if (isset($this->fk_societe)) $this->fk_societe=trim($this->fk_societe);
		if (isset($this->numerodossier)) $this->numerodossier=trim($this->numerodossier);
		if (isset($this->transport)) $this->transport=trim($this->transport);
		if (isset($this->numeroplomb)) $this->numeroplomb=trim($this->numeroplomb);
		if (isset($this->numeroconteneur)) $this->numeroconteneur=trim($this->numeroconteneur);
		if (isset($this->ref_ext1)) $this->ref_ext1=trim($this->ref_ext1);
		if (isset($this->ref_ext2)) $this->ref_ext2=trim($this->ref_ext2);
		if (isset($this->note)) $this->note=trim($this->note);
		if (isset($this->marchandise_description)) $this->marchandise_description=trim($this->marchandise_description);
		if (isset($this->mode_calcul)) $this->mode_calcul=trim($this->mode_calcul);
		if (isset($this->coeff_rev_global)) $this->coeff_rev_global=trim($this->coeff_rev_global);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."stockentry SET";
        
		$sql.= " fk_societe=".(isset($this->fk_societe)?$this->fk_societe:"null").",";
		$sql.= " date_entree=".(dol_strlen($this->date_entree)!=0 ? "'".$this->db->idate($this->date_entree)."'" : 'null').",";
		$sql.= " date_validation=".(dol_strlen($this->date_validation)!=0 ? "'".$this->db->idate($this->date_validation)."'" : 'null').",";
		$sql.= " numerodossier=".(isset($this->numerodossier)?"'".$this->db->escape($this->numerodossier)."'":"null").",";
		$sql.= " transport=".(isset($this->transport)?"'".$this->db->escape($this->transport)."'":"null").",";
		$sql.= " numeroplomb=".(isset($this->numeroplomb)?"'".$this->db->escape($this->numeroplomb)."'":"null").",";
		$sql.= " numeroconteneur=".(isset($this->numeroconteneur)?"'".$this->db->escape($this->numeroconteneur)."'":"null").",";
		$sql.= " ref_ext1=".(isset($this->ref_ext1)?"'".$this->db->escape($this->ref_ext1)."'":"null").",";
		$sql.= " ref_ext2=".(isset($this->ref_ext2)?"'".$this->db->escape($this->ref_ext2)."'":"null").",";
		$sql.= " note=".(isset($this->note)?"'".$this->db->escape($this->note)."'":"null").",";
		$sql.= " marchandise_description=".(isset($this->marchandise_description)?"'".$this->db->escape($this->marchandise_description)."'":"null").",";
		$sql.= " mode_calcul=".(isset($this->mode_calcul)?"'".$this->db->escape($this->mode_calcul)."'":"null").",";
		$sql.= " coeff_rev_global=".(isset($this->coeff_rev_global)?"'".$this->coeff_rev_global."'":"null")."";

        
        $sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::update sql=".$sql, LOG_DEBUG);
        $resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
	            // Uncomment this and change MYOBJECT to your own tag if you
	            // want this action call a trigger.

	            //// Call triggers
	            //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
	            //$interface=new Interfaces($this->db);
	            //$result=$interface->run_triggers('MYOBJECT_MODIFY',$this,$user,$langs,$conf);
	            //if ($result < 0) { $error++; $this->errors=$interface->errors; }
	            //// End call triggers
	    	}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::update ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
    }


 	/**
	 *  Delete object in database
	 *
     *	@param     User	$user        User that delete
     *  @param     int		$notrigger	 0=launch triggers after, 1=disable triggers
	 *  @return	int					 <0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."stockentry";
		$sql.= " WHERE rowid=".$this->id;

		$this->db->begin();

		dol_syslog(get_class($this)."::delete sql=".$sql);
		$resql = $this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			if (! $notrigger)
			{
				// Uncomment this and change MYOBJECT to your own tag if you
		        // want this action call a trigger.

		        //// Call triggers
		        //include_once(DOL_DOCUMENT_ROOT . "/core/class/interfaces.class.php");
		        //$interface=new Interfaces($this->db);
		        //$result=$interface->run_triggers('MYOBJECT_DELETE',$this,$user,$langs,$conf);
		        //if ($result < 0) { $error++; $this->errors=$interface->errors; }
		        //// End call triggers
			}
		}

        // Commit or rollback
		if ($error)
		{
			foreach($this->errors as $errmsg)
			{
	            dol_syslog(get_class($this)."::delete ".$errmsg, LOG_ERR);
	            $this->error.=($this->error?', '.$errmsg:$errmsg);
			}
			$this->db->rollback();
			return -1*$error;
		}
		else
		{
			$this->db->commit();
			return 1;
		}
	}



	/**
	 *	Load an object from its id and create a new one in database
	 *
	 *	@param      int		$fromid     Id of object to clone
	 * 	@return		int					New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;

		$error=0;

		$object=new Stockentry($this->db);

		$this->db->begin();

		// Load source object
		$object->fetch($fromid);
		$object->id=0;
		$object->statut=0;

		// Clear fields
		// ...

		// Create clone
		$result=$object->create($user);

		// Other options
		if ($result < 0)
		{
			$this->error=$object->error;
			$error++;
		}

		if (! $error)
		{



		}

		// End
		if (! $error)
		{
			$this->db->commit();
			return $object->id;
		}
		else
		{
			$this->db->rollback();
			return -1;
		}
	}


	/**
	 *	Initialise object with example values
	 *	Id must be 0 if object instance is a specimen
	 *
	 *	@return	void
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->fk_societe='';
		$this->date_entree='';
		$this->date_validation='';
		$this->numerodossier='';
		$this->transport='';
		$this->numeroplomb='';
		$this->numeroconteneur='';
		$this->ref_ext1='';
		$this->ref_ext2='';
		$this->note='';
		$this->marchandise_description='';
		$this->mode_calcul='';
		$this->coeff_rev_global='';

		
	}
	
	function getList($customsql =''){
	    $fields = $this->customfields->fetchAllCustomFields(1);
	    $this->setFields($fields);
	    $elements = array();
	    $sql = "SELECT l.rowid, s.nom  AS fournisseur_name, l.date_entree ";
	    $sql.= " FROM " . MAIN_DB_PREFIX . $this->table_element . " AS l LEFT JOIN ".MAIN_DB_PREFIX."societe AS s ON l.fk_societe = s.rowid". $customsql;
	    $resql = $this->customfields->executeSQL($sql, "getList");
	    if ($this->db->num_rows($resql) > 0) {
	        $num = $this->db->num_rows($resql);
	        for ($i=0;$i < $num;$i++) {
	            $obj = $this->db->fetch_object($resql);
	            $elements[] = $obj;
	        }
	    }
	    $this->db->free($resql);
	    return $elements;
	}

}
?>
