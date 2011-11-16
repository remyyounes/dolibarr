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
 *      \file       dev/skeletons/c_product_alcool.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\version    $Id: c_product_alcool.class.php,v 1.29 2010/04/29 14:54:13 grandoc Exp $
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2010-08-05 04:04
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");


/**
 *      \class      C_product_alcool
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-08-05 04:04
 */
class C_product_alcool // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='c_product_alcool';			//!< Id that identify managed objects
	//var $table_element='c_product_alcool';	//!< Name of table without prefix where object is stored
    
    var $id;
    
	var $fk_product;
	var $fk_ctar1;
	var $fk_ctar2;
	var $fk_cvig;
	var $cont;
	var $alcp;

	var $editable_fields = array(
		'fk_ctar1',
		'fk_ctar2',
		'fk_cvig',
		'cont',
		'alcp',
	);
    

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function C_product_alcool($DB) 
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
        
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_ctar1)) $this->fk_ctar1=trim($this->fk_ctar1);
		if (isset($this->fk_ctar2)) $this->fk_ctar2=trim($this->fk_ctar2);
		if (isset($this->fk_cvig)) $this->fk_cvig=trim($this->fk_cvig);
		if (isset($this->cont)) $this->cont=trim($this->cont);
		if (isset($this->alcp)) $this->alcp=trim($this->alcp);

        

		// Check parameters
		// Put here code to add control on parameters values
		
        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."c_product_alcool(";
		
		$sql.= "fk_product,";
		$sql.= "fk_ctar1,";
		$sql.= "fk_ctar2,";
		$sql.= "fk_cvig,";
		$sql.= "cont,";
		$sql.= "alcp";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->fk_ctar1)?'NULL':"'".addslashes($this->fk_ctar1)."'").",";
		$sql.= " ".(! isset($this->fk_ctar2)?'NULL':"'".addslashes($this->fk_ctar2)."'").",";
		$sql.= " ".(! isset($this->fk_cvig)?'NULL':"'".addslashes($this->fk_cvig)."'").",";
		$sql.= " ".(! isset($this->cont)?'NULL':"'".$this->cont."'").",";
		$sql.= " ".(! isset($this->alcp)?'NULL':"'".$this->alcp."'")."";

        
		$sql.= ")";

		$this->db->begin();
		
	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }
        
		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."c_product_alcool");
    
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
    function fetch($id,$fk=0)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_product,";
		$sql.= " t.fk_ctar1,";
		$sql.= " t.fk_ctar2,";
		$sql.= " t.fk_cvig,";
		$sql.= " t.cont,";
		$sql.= " t.alcp";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."c_product_alcool as t";
        
        if($fk){
        	$sql.= " WHERE t.fk_product = ".$id;
        }else{
        	$sql.= " WHERE t.rowid = ".$id;
        }
    
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);
    
                $this->id    = $obj->rowid;
                
				$this->fk_product = $obj->fk_product;
				$this->fk_ctar1 = $obj->fk_ctar1;
				$this->fk_ctar2 = $obj->fk_ctar2;
				$this->fk_cvig = $obj->fk_cvig;
				$this->cont = $obj->cont;
				$this->alcp = $obj->alcp;

                
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
        
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_ctar1)) $this->fk_ctar1=trim($this->fk_ctar1);
		if (isset($this->fk_ctar2)) $this->fk_ctar2=trim($this->fk_ctar2);
		if (isset($this->fk_cvig)) $this->fk_cvig=trim($this->fk_cvig);
		if (isset($this->cont)) $this->cont=trim($this->cont);
		if (isset($this->alcp)) $this->alcp=trim($this->alcp);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."c_product_alcool SET";
        
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " fk_ctar1=".(isset($this->fk_ctar1)?"'".addslashes($this->fk_ctar1)."'":"null").",";
		$sql.= " fk_ctar2=".(isset($this->fk_ctar2)?"'".addslashes($this->fk_ctar2)."'":"null").",";
		$sql.= " fk_cvig=".(isset($this->fk_cvig)?"'".addslashes($this->fk_cvig)."'":"null").",";
		$sql.= " cont=".(isset($this->cont)?$this->cont:"null").",";
		$sql.= " alcp=".(isset($this->alcp)?$this->alcp:"null");

        
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
		
		$sql = "DELETE FROM ".MAIN_DB_PREFIX."c_product_alcool";
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
		
		$object=new C_product_alcool($this->db);

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
		
		$this->fk_product='';
		$this->fk_ctar1='';
		$this->fk_ctar2='';
		$this->fk_cvig='';
		$this->cont='';
		$this->alcp='';

		
	}
	
function form_inputField($field,$html_name){
		global $langs;

		
		
		$input = "";
		$input .= '<tr><td width="15%">';
		$input .= $langs->trans($html_name);
		$input .= '</td>';
		$input .= '<td>';
		if( strpos($field, "fk_") !== false) {
			$c_taxe = new C_taxe($this->db);
			$input .= $c_taxe->select_taxecode($this->$field, $html_name, 1);
		}elseif( in_array($field, $this->editable_fields) ){
			$input .= '<input type="text" name="'.$html_name.'" size="25" value="'.$this->$field.'"> ';
		}else{
			return "";
		}
		$input .= '</td>';
		$input .= '</tr>';
		
		return $input;
	}

}
?>