<?php
/* Copyright (C) 2007-2008 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

/**
 *      \file       dev/skeletons/c_fabricant.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\version    $Id: c_fabricant.class.php,v 1.29 2010/04/29 14:54:13 grandoc Exp $
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2010-08-11 16:25
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *      \class      C_fabricant
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-08-11 16:25
 */
class C_fabricant // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='c_fabricant';			//!< Id that identify managed objects
	//var $table_element='c_fabricant';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $code;
	var $libelle;
	var $fk_pays;
	var $active;
	var $cache_fabricantcode=array();
    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function C_fabricant($DB) 
    {
        $this->db = $DB;
        return 1;
    }

	
    /**
     *      \brief      Create in database
     *      \param      user        	User that create
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, Id of created object if OK
     */
    function create($user, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->libelle)) $this->libelle=trim($this->libelle);
		if (isset($this->fk_pays)) $this->fk_pays=trim($this->fk_pays);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_fabricant(";
		
		$sql.= "code,";
		$sql.= "libelle,";
		$sql.= "fk_pays,";
		$sql.= "active";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->code)?'NULL':"'".addslashes($this->code)."'").",";
		$sql.= " ".(! isset($this->libelle)?'NULL':"'".addslashes($this->libelle)."'").",";
		$sql.= " ".(! isset($this->fk_pays)?'NULL':"'".$this->fk_pays."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'")."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."c_fabricant");
    
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
     *    \brief      Load object in memory from database
     *    \param      id          id object
     *    \return     int         <0 if KO, >0 if OK
     */
    function fetch($id)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.code,";
		$sql.= " t.libelle,";
		$sql.= " t.fk_pays,";
		$sql.= " t.active";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."c_fabricant as t";
        $sql.= " WHERE t.rowid = ".$id;
    
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
                $this->id    = $obj->rowid;
                
				$this->code = $obj->code;
				$this->libelle = $obj->libelle;
				$this->fk_pays = $obj->fk_pays;
				$this->active = $obj->active;

                
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
     *      \brief      Update database
     *      \param      user        	User that modify
     *      \param      notrigger	    0=launch triggers after, 1=disable triggers
     *      \return     int         	<0 if KO, >0 if OK
     */
    function update($user=0, $notrigger=0)
    {
    	global $conf, $langs;
		$error=0;
    	
		// Clean parameters
        
		if (isset($this->code)) $this->code=trim($this->code);
		if (isset($this->libelle)) $this->libelle=trim($this->libelle);
		if (isset($this->fk_pays)) $this->fk_pays=trim($this->fk_pays);
		if (isset($this->active)) $this->active=trim($this->active);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."c_fabricant SET";
        
		$sql.= " code=".(isset($this->code)?"'".addslashes($this->code)."'":"null").",";
		$sql.= " libelle=".(isset($this->libelle)?"'".addslashes($this->libelle)."'":"null").",";
		$sql.= " fk_pays=".(isset($this->fk_pays)?$this->fk_pays:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null")."";

        
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
	 *   \brief      Delete object in database
     *	\param      user        	User that delete
     *   \param      notrigger	    0=launch triggers after, 1=disable triggers
	 *	\return		int				<0 if KO, >0 if OK
	 */
	function delete($user, $notrigger=0)
	{
		global $conf, $langs;
		$error=0;
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."c_fabricant";
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
	 *		\brief      Load an object from its id and create a new one in database
	 *		\param      fromid     		Id of object to clone
	 * 	 	\return		int				New id of clone
	 */
	function createFromClone($fromid)
	{
		global $user,$langs;
		
		$error=0;
		
		$object=new C_fabricant($this->db);

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
	 *		\brief		Initialise object with example values
	 *		\remarks	id must be 0 if object instance is a specimen.
	 */
	function initAsSpecimen()
	{
		$this->id=0;
		
		$this->code='';
		$this->libelle='';
		$this->fk_pays='';
		$this->active='';

		
	}
	
	function printCode(){
		$out = "";
		$out = $this->code . " - " . $this->libelle;
		if($this->libelle == ""){
			$out = "";
		}
		return $out;
	}
	
	
	
	/**
	 *    	\brief      Return an html string with a select combo box to choose from load_cache_fabricantcode
	 *    	\param      htmlname            Name of html select field
	 *    	\param      selected           Pre-selected value
	 *  	\param      addempty        add blank option
	 * 		\return		string			form select field
	 */
	function select_code($selected='',$htmlname='condid',$addempty=0)
	{
		global $langs,$user;
	
		$this->load_cache_fabricantcode();
		$input = "";
		$input .= '<select class="flat" name="'.$htmlname.'">';
		if ($addempty) $input .= "<option value=''>&nbsp;</option>";
		foreach($this->cache_fabricantcode as $id => $arrayconditions)
		{
			$code = $arrayconditions['code'];
			if ($selected == $id)
			{
				$input .= '<option value="'.$id.'" selected="true">';
			}
			else
			{
				$input .= '<option value="'.$id.'">';
			}
			$input .= $arrayconditions['code'] . " - " . $arrayconditions['label'];
			$input .= '</option>';
		}
		$input .= '</select>';
		if ($user->admin) $input .= info_admin($langs->trans("YouCanChangeValuesForThisListFromDictionnarySetup"),1);
		return $input;
	}
	
/**
	 *      \brief      Charge dans cache la liste des code de taxes parafiscales
	 *      \return     int             Nb lignes chargees, 0 si deja chargees, <0 si ko
	 */
	function load_cache_fabricantcode()
	{
		global $langs;

		if (sizeof($this->cache_fabricantcode)) return 0;    // Cache deja charge

		$sql = "SELECT rowid as id, code, libelle";
		$sql.= " FROM ".MAIN_DB_PREFIX."c_fabricant";
		$sql.= " WHERE active > 0";
		$sql.= " ORDER BY code ASC";
		dol_syslog('c_fabricant::load_cache_codefabricant sql='.$sql,LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while ($i < $num)
			{
				$obj = $this->db->fetch_object($resql);
				$this->cache_fabricantcode[$obj->id]['code'] =$obj->code;
				$this->cache_fabricantcode[$obj->id]['label']=$obj->libelle;
				$i++;
			}
			return $num;
		}
		else {
			dol_print_error($this->db);
			return -1;
		}
	}
	

}
?>
