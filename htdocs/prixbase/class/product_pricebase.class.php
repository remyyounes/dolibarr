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
 *      \file       dev/skeletons/product_pricebase.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2011-12-05 11:11
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

require_once(DOL_DOCUMENT_ROOT."/makina/class/commoncustomobject.class.php");


/**
 *      \class      Product_pricebase
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2011-12-05 11:11
 */
class Product_pricebase extends CommonCustomObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='product_pricebase';			//!< Id that identify managed objects
	protected $table_element = 'product_pricebase';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_product;
	var $pa;
	var $pamp;
	var $prht;
	var $prmpht;
	var $prttc;
	var $prmpttc;
	var $valorisation;
	var $peremption;
	var $fk_user;

    


    /**
     *  Constructor
     *
     *  @param      DoliDb		$DB      Database handler
     */
    function Product_pricebase($DB)
    {
        $this->db = $DB;$this->templates = new stdclass;
        $this->templates->datasheet = DOL_DOCUMENT_ROOT."/prixbase/canvas/tpl/datasheet.tpl.php";
        $this->templates->list = DOL_DOCUMENT_ROOT."/prixbase/canvas/tpl/list.tpl.php";
        $this->customfields = new CustomFields($this->db, '');
        $this->customfields->moduletable =  MAIN_DB_PREFIX . $this->table_element;
        $this->defineCustomFieldTypes();
        return 1;
    }
    
    function defineCustomFieldTypes(){
        $this->customTypes['pa'] = 'price';
        $this->customTypes['pamp'] = 'price';
        $this->customTypes['prht'] = 'price';
        $this->customTypes['prmpht'] = 'price';
        $this->customTypes['prttc'] = 'price';
        $this->customTypes['prmpttc'] = 'price';
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
        
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->pa)) $this->pa=trim($this->pa);
		if (isset($this->pamp)) $this->pamp=trim($this->pamp);
		if (isset($this->prht)) $this->prht=trim($this->prht);
		if (isset($this->prmpht)) $this->prmpht=trim($this->prmpht);
		if (isset($this->prttc)) $this->prttc=trim($this->prttc);
		if (isset($this->prmpttc)) $this->prmpttc=trim($this->prmpttc);
		if (isset($this->valorisation)) $this->valorisation=trim($this->valorisation);
		if (isset($this->peremption)) $this->peremption=trim($this->peremption);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."product_pricebase(";
		
		$sql.= "fk_user,";
		$sql.= "date_log,";
		$sql.= "fk_product,";
		$sql.= "pa,";
		$sql.= "pamp,";
		$sql.= "prht,";
		$sql.= "prmpht,";
		$sql.= "prttc,";
		$sql.= "prmpttc,";
		$sql.= "valorisation,";
		$sql.= "peremption";
		
        $sql.= ") VALUES (";
        
        $sql.= " ".(! isset($user->date_log)?'NULL':"'".$this->date_log."'").",";
        $sql.= " ".(! isset($user->id)?'NULL':"'".$user->id."'").",";        
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->pa)?'NULL':"'".$this->pa."'").",";
		$sql.= " ".(! isset($this->pamp)?'NULL':"'".$this->pamp."'").",";
		$sql.= " ".(! isset($this->prht)?'NULL':"'".$this->prht."'").",";
		$sql.= " ".(! isset($this->prmpht)?'NULL':"'".$this->prmpht."'").",";
		$sql.= " ".(! isset($this->prttc)?'NULL':"'".$this->prttc."'").",";
		$sql.= " ".(! isset($this->prmpttc)?'NULL':"'".$this->prmpttc."'").",";
		$sql.= " ".(! isset($this->valorisation)?'NULL':"'".$this->valorisation."'").",";
		$sql.= " ".(! isset($this->peremption)?'NULL':"'".$this->peremption."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."product_pricebase");

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
    function fetch($id,$fk=0)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_product,";
		$sql.= " t.pa,";
		$sql.= " t.pamp,";
		$sql.= " t.prht,";
		$sql.= " t.prmpht,";
		$sql.= " t.prttc,";
		$sql.= " t.prmpttc,";
		$sql.= " t.valorisation,";
		$sql.= " t.peremption,";
		$sql.= " t.fk_user,";
		$sql.= " t.date_log";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."product_pricebase as t";
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
				$this->pa = $obj->pa;
				$this->pamp = $obj->pamp;
				$this->prht = $obj->prht;
				$this->prmpht = $obj->prmpht;
				$this->prttc = $obj->prttc;
				$this->prmpttc = $obj->prmpttc;
				$this->valorisation = $obj->valorisation;
				$this->peremption = $obj->peremption;
				$this->fk_user = $obj->fk_user;
				$this->date_log = $obj->date_log;
                
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
        
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->pa)) $this->pa=trim($this->pa);
		if (isset($this->pamp)) $this->pamp=trim($this->pamp);
		if (isset($this->prht)) $this->prht=trim($this->prht);
		if (isset($this->prmpht)) $this->prmpht=trim($this->prmpht);
		if (isset($this->prttc)) $this->prttc=trim($this->prttc);
		if (isset($this->prmpttc)) $this->prmpttc=trim($this->prmpttc);
		if (isset($this->valorisation)) $this->valorisation=trim($this->valorisation);
		if (isset($this->peremption)) $this->peremption=trim($this->peremption);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."product_pricebase SET";
        
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " pa=".(($this->pa)?$this->pa:"0").",";
		$sql.= " pamp=".(($this->pamp)?$this->pamp:"0").",";
		$sql.= " prht=".(($this->prht)?$this->prht:"0").",";
		$sql.= " prmpht=".(($this->prmpht)?$this->prmpht:"0").",";
		$sql.= " prttc=".(($this->prttc)?$this->prttc:"0").",";
		$sql.= " prmpttc=".(($this->prmpttc)?$this->prmpttc:"0").",";
		$sql.= " valorisation=".(isset($this->valorisation)?"'".$this->valorisation."'":"null").",";
		$sql.= " peremption=".(($this->peremption)?$this->peremption:"0").",";
		$sql.= " date_log=".(($this->date_log)?$this->date_log:"").",";
		$sql.= " fk_user='".$user->id."' ";
		
        
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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."product_pricebase";
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

		$object=new Product_pricebase($this->db);

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
		
		$this->fk_product='';
		$this->pa='';
		$this->pamp='';
		$this->prht='';
		$this->prmpht='';
		$this->prttc='';
		$this->prmpttc='';
		$this->valorisation='';
		$this->peremption='';
		$this->fk_user='';
		$this->date_log='';

		
	}

}
?>
