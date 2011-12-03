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
 *      \file       dev/skeletons/stockentry_line.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2011-12-02 00:56
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/makina/class/commoncustomobject.class.php");

/**
 *      \class      Stockentry_line
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2011-12-02 00:56
 */
class Stockentry_line extends CommonCustomObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='stockentry_line';			//!< Id that identify managed objects
	protected $table_element='stockentry_line';	//!< Name of table without prefix where object is stored

    var $id;
    
	var $fk_stockentry;
	var $fk_entrepot;
	var $fk_societe;
	var $fk_facture_fourn;
	var $numerofacture_externe;
	var $date_facture_fourn='';
	var $date_echeance_facture_fourn='';
	var $total_ht_facture;
	var $total_ttc_facture;
	var $coeff_ht;
	var $coeff_ttc;
	var $daom;
	var $numeroconteneur;
	var $code_list_control;
	var $code_selection;
	var $repartition;
	var $volume;
	var $weight;
	var $weight_unit;
	var $volume_unit;
	var $type_facture;
	var $mode_calcul;
	var $accounting;
	var $note;

    


    /**
     *  Constructor
     *
     *  @param      DoliDb		$DB      Database handler
     */
    function Stockentry_line($DB)
    {
        $this->db = $DB;
        $this->templates = new stdclass;
        $this->templates->datasheet = DOL_DOCUMENT_ROOT."/stockoperation/canvas/tpl/facture.tpl.php";
        $this->templates->list = DOL_DOCUMENT_ROOT."/stockoperation/canvas/tpl/facturelist.tpl.php";
        $this->customfields = new CustomFields($this->db, '');
        $this->customfields->moduletable =  MAIN_DB_PREFIX . $this->table_element;
        $this->defineCustomFieldTypes();
        return 1;
    }
    
    function defineCustomFieldTypes(){
        $this->customTypes['fk_societe'] = 'fournisseur';
        $this->customTypes['fk_facture_fourn'] = 'facture_fourn';
        $this->customTypes['fk_stockentry'] = 'stockentry';
        $this->customTypes['fk_entrepot'] = 'entrepot';
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
        
		if (isset($this->fk_stockentry)) $this->fk_stockentry=trim($this->fk_stockentry);
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->fk_societe)) $this->fk_societe=trim($this->fk_societe);
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->numerofacture_externe)) $this->numerofacture_externe=trim($this->numerofacture_externe);
		if (isset($this->total_ht_facture)) $this->total_ht_facture=trim($this->total_ht_facture);
		if (isset($this->total_ttc_facture)) $this->total_ttc_facture=trim($this->total_ttc_facture);
		if (isset($this->coeff_ht)) $this->coeff_ht=trim($this->coeff_ht);
		if (isset($this->coeff_ttc)) $this->coeff_ttc=trim($this->coeff_ttc);
		if (isset($this->daom)) $this->daom=trim($this->daom);
		if (isset($this->numeroconteneur)) $this->numeroconteneur=trim($this->numeroconteneur);
		if (isset($this->code_list_control)) $this->code_list_control=trim($this->code_list_control);
		if (isset($this->code_selection)) $this->code_selection=trim($this->code_selection);
		if (isset($this->repartition)) $this->repartition=trim($this->repartition);
		if (isset($this->volume)) $this->volume=trim($this->volume);
		if (isset($this->weight)) $this->weight=trim($this->weight);
		if (isset($this->weight_unit)) $this->weight_unit=trim($this->weight_unit);
		if (isset($this->volume_unit)) $this->volume_unit=trim($this->volume_unit);
		if (isset($this->type_facture)) $this->type_facture=trim($this->type_facture);
		if (isset($this->mode_calcul)) $this->mode_calcul=trim($this->mode_calcul);
		if (isset($this->accounting)) $this->accounting=trim($this->accounting);
		if (isset($this->note)) $this->note=trim($this->note);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."stockentry_line(";
		
		$sql.= "fk_stockentry,";
		$sql.= "fk_entrepot,";
		$sql.= "fk_societe,";
		$sql.= "fk_facture_fourn,";
		$sql.= "numerofacture_externe,";
		$sql.= "date_facture_fourn,";
		$sql.= "date_echeance_facture_fourn,";
		$sql.= "total_ht_facture,";
		$sql.= "total_ttc_facture,";
		$sql.= "coeff_ht,";
		$sql.= "coeff_ttc,";
		$sql.= "daom,";
		$sql.= "numeroconteneur,";
		$sql.= "code_list_control,";
		$sql.= "code_selection,";
		$sql.= "repartition,";
		$sql.= "volume,";
		$sql.= "weight,";
		$sql.= "weight_unit,";
		$sql.= "volume_unit,";
		$sql.= "type_facture,";
		$sql.= "mode_calcul,";
		$sql.= "accounting,";
		$sql.= "note";

		
        $sql.= ") VALUES (";
        
		$sql.= " ".(! isset($this->fk_stockentry)?'NULL':"'".$this->fk_stockentry."'").",";
		$sql.= " ".(! isset($this->fk_entrepot)?'NULL':"'".$this->fk_entrepot."'").",";
		$sql.= " ".(! isset($this->fk_societe)?'NULL':"'".$this->fk_societe."'").",";
		$sql.= " ".(! isset($this->fk_facture_fourn)?'NULL':"'".$this->fk_facture_fourn."'").",";
		$sql.= " ".(! isset($this->numerofacture_externe)?'NULL':"'".$this->db->escape($this->numerofacture_externe)."'").",";
		$sql.= " ".(! isset($this->date_facture_fourn) || dol_strlen($this->date_facture_fourn)==0?'NULL':$this->db->idate($this->date_facture_fourn)).",";
		$sql.= " ".(! isset($this->date_echeance_facture_fourn) || dol_strlen($this->date_echeance_facture_fourn)==0?'NULL':$this->db->idate($this->date_echeance_facture_fourn)).",";
		$sql.= " ".(! isset($this->total_ht_facture)?'NULL':"'".$this->total_ht_facture."'").",";
		$sql.= " ".(! isset($this->total_ttc_facture)?'NULL':"'".$this->total_ttc_facture."'").",";
		$sql.= " ".(! isset($this->coeff_ht)?'NULL':"'".$this->coeff_ht."'").",";
		$sql.= " ".(! isset($this->coeff_ttc)?'NULL':"'".$this->coeff_ttc."'").",";
		$sql.= " ".(! isset($this->daom)?'NULL':"'".$this->daom."'").",";
		$sql.= " ".(! isset($this->numeroconteneur)?'NULL':"'".$this->db->escape($this->numeroconteneur)."'").",";
		$sql.= " ".(! isset($this->code_list_control)?'NULL':"'".$this->db->escape($this->code_list_control)."'").",";
		$sql.= " ".(! isset($this->code_selection)?'NULL':"'".$this->db->escape($this->code_selection)."'").",";
		$sql.= " ".(! isset($this->repartition)?'NULL':"'".$this->db->escape($this->repartition)."'").",";
		$sql.= " ".(! isset($this->volume)?'NULL':"'".$this->volume."'").",";
		$sql.= " ".(! isset($this->weight)?'NULL':"'".$this->weight."'").",";
		$sql.= " ".(! isset($this->weight_unit)?'NULL':"'".$this->weight_unit."'").",";
		$sql.= " ".(! isset($this->volume_unit)?'NULL':"'".$this->volume_unit."'").",";
		$sql.= " ".(! isset($this->type_facture)?'NULL':"'".$this->type_facture."'").",";
		$sql.= " ".(! isset($this->mode_calcul)?'NULL':"'".$this->mode_calcul."'").",";
		$sql.= " ".(! isset($this->accounting)?'NULL':"'".$this->accounting."'").",";
		$sql.= " ".(! isset($this->note)?'NULL':"'".$this->db->escape($this->note)."'")."";

        
		$sql.= ")";

		$this->db->begin();

	   	dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
    	if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
        {
            $this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."stockentry_line");

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
		
		$sql.= " t.fk_stockentry,";
		$sql.= " t.fk_entrepot,";
		$sql.= " t.fk_societe,";
		$sql.= " t.fk_facture_fourn,";
		$sql.= " t.numerofacture_externe,";
		$sql.= " t.date_facture_fourn,";
		$sql.= " t.date_echeance_facture_fourn,";
		$sql.= " t.total_ht_facture,";
		$sql.= " t.total_ttc_facture,";
		$sql.= " t.coeff_ht,";
		$sql.= " t.coeff_ttc,";
		$sql.= " t.daom,";
		$sql.= " t.numeroconteneur,";
		$sql.= " t.code_list_control,";
		$sql.= " t.code_selection,";
		$sql.= " t.repartition,";
		$sql.= " t.volume,";
		$sql.= " t.weight,";
		$sql.= " t.weight_unit,";
		$sql.= " t.volume_unit,";
		$sql.= " t.type_facture,";
		$sql.= " t.mode_calcul,";
		$sql.= " t.accounting,";
		$sql.= " t.note";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."stockentry_line as t";
        $sql.= " WHERE t.rowid = ".$id;

    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
            if ($this->db->num_rows($resql))
            {
                $obj = $this->db->fetch_object($resql);

                $this->id    = $obj->rowid;
                
				$this->fk_stockentry = $obj->fk_stockentry;
				$this->fk_entrepot = $obj->fk_entrepot;
				$this->fk_societe = $obj->fk_societe;
				$this->fk_facture_fourn = $obj->fk_facture_fourn;
				$this->numerofacture_externe = $obj->numerofacture_externe;
				$this->date_facture_fourn = $this->db->jdate($obj->date_facture_fourn);
				$this->date_echeance_facture_fourn = $this->db->jdate($obj->date_echeance_facture_fourn);
				$this->total_ht_facture = $obj->total_ht_facture;
				$this->total_ttc_facture = $obj->total_ttc_facture;
				$this->coeff_ht = $obj->coeff_ht;
				$this->coeff_ttc = $obj->coeff_ttc;
				$this->daom = $obj->daom;
				$this->numeroconteneur = $obj->numeroconteneur;
				$this->code_list_control = $obj->code_list_control;
				$this->code_selection = $obj->code_selection;
				$this->repartition = $obj->repartition;
				$this->volume = $obj->volume;
				$this->weight = $obj->weight;
				$this->weight_unit = $obj->weight_unit;
				$this->volume_unit = $obj->volume_unit;
				$this->type_facture = $obj->type_facture;
				$this->mode_calcul = $obj->mode_calcul;
				$this->accounting = $obj->accounting;
				$this->note = $obj->note;

                
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
        
		if (isset($this->fk_stockentry)) $this->fk_stockentry=trim($this->fk_stockentry);
		if (isset($this->fk_entrepot)) $this->fk_entrepot=trim($this->fk_entrepot);
		if (isset($this->fk_societe)) $this->fk_societe=trim($this->fk_societe);
		if (isset($this->fk_facture_fourn)) $this->fk_facture_fourn=trim($this->fk_facture_fourn);
		if (isset($this->numerofacture_externe)) $this->numerofacture_externe=trim($this->numerofacture_externe);
		if (isset($this->total_ht_facture)) $this->total_ht_facture=trim($this->total_ht_facture);
		if (isset($this->total_ttc_facture)) $this->total_ttc_facture=trim($this->total_ttc_facture);
		if (isset($this->coeff_ht)) $this->coeff_ht=trim($this->coeff_ht);
		if (isset($this->coeff_ttc)) $this->coeff_ttc=trim($this->coeff_ttc);
		if (isset($this->daom)) $this->daom=trim($this->daom);
		if (isset($this->numeroconteneur)) $this->numeroconteneur=trim($this->numeroconteneur);
		if (isset($this->code_list_control)) $this->code_list_control=trim($this->code_list_control);
		if (isset($this->code_selection)) $this->code_selection=trim($this->code_selection);
		if (isset($this->repartition)) $this->repartition=trim($this->repartition);
		if (isset($this->volume)) $this->volume=trim($this->volume);
		if (isset($this->weight)) $this->weight=trim($this->weight);
		if (isset($this->weight_unit)) $this->weight_unit=trim($this->weight_unit);
		if (isset($this->volume_unit)) $this->volume_unit=trim($this->volume_unit);
		if (isset($this->type_facture)) $this->type_facture=trim($this->type_facture);
		if (isset($this->mode_calcul)) $this->mode_calcul=trim($this->mode_calcul);
		if (isset($this->accounting)) $this->accounting=trim($this->accounting);
		if (isset($this->note)) $this->note=trim($this->note);

        

		// Check parameters
		// Put here code to add control on parameters values

        // Update request
        $sql = "UPDATE ".MAIN_DB_PREFIX."stockentry_line SET";
        
		$sql.= " fk_stockentry=".(isset($this->fk_stockentry)?$this->fk_stockentry:"null").",";
		$sql.= " fk_entrepot=".(isset($this->fk_entrepot)?$this->fk_entrepot:"null").",";
		$sql.= " fk_societe=".(isset($this->fk_societe)?$this->fk_societe:"null").",";
		$sql.= " fk_facture_fourn=".(isset($this->fk_facture_fourn)?$this->fk_facture_fourn:"null").",";
		$sql.= " numerofacture_externe=".(isset($this->numerofacture_externe)?"'".$this->db->escape($this->numerofacture_externe)."'":"null").",";
		$sql.= " date_facture_fourn=".(dol_strlen($this->date_facture_fourn)!=0 ? "'".$this->db->idate($this->date_facture_fourn)."'" : 'null').",";
		$sql.= " date_echeance_facture_fourn=".(dol_strlen($this->date_echeance_facture_fourn)!=0 ? "'".$this->db->idate($this->date_echeance_facture_fourn)."'" : 'null').",";
		$sql.= " total_ht_facture=".(isset($this->total_ht_facture)?$this->total_ht_facture:"null").",";
		$sql.= " total_ttc_facture=".(isset($this->total_ttc_facture)?$this->total_ttc_facture:"null").",";
		$sql.= " coeff_ht=".(isset($this->coeff_ht)?$this->coeff_ht:"null").",";
		$sql.= " coeff_ttc=".(isset($this->coeff_ttc)?$this->coeff_ttc:"null").",";
		$sql.= " daom=".(isset($this->daom)?$this->daom:"null").",";
		$sql.= " numeroconteneur=".(isset($this->numeroconteneur)?"'".$this->db->escape($this->numeroconteneur)."'":"null").",";
		$sql.= " code_list_control=".(isset($this->code_list_control)?"'".$this->db->escape($this->code_list_control)."'":"null").",";
		$sql.= " code_selection=".(isset($this->code_selection)?"'".$this->db->escape($this->code_selection)."'":"null").",";
		$sql.= " repartition=".(isset($this->repartition)?"'".$this->db->escape($this->repartition)."'":"null").",";
		$sql.= " volume=".(isset($this->volume)?$this->volume:"null").",";
		$sql.= " weight=".(isset($this->weight)?$this->weight:"null").",";
		$sql.= " weight_unit=".(isset($this->weight_unit)?$this->weight_unit:"null").",";
		$sql.= " volume_unit=".(isset($this->volume_unit)?$this->volume_unit:"null").",";
		$sql.= " type_facture=".(isset($this->type_facture)?$this->type_facture:"null").",";
		$sql.= " mode_calcul=".(isset($this->mode_calcul)?$this->mode_calcul:"null").",";
		$sql.= " accounting=".(isset($this->accounting)?$this->accounting:"null").",";
		$sql.= " note=".(isset($this->note)?"'".$this->db->escape($this->note)."'":"null")."";

        
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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."stockentry_line";
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

		$object=new Stockentry_line($this->db);

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
		
		$this->fk_stockentry='';
		$this->fk_entrepot='';
		$this->fk_societe='';
		$this->fk_facture_fourn='';
		$this->numerofacture_externe='';
		$this->date_facture_fourn='';
		$this->date_echeance_facture_fourn='';
		$this->total_ht_facture='';
		$this->total_ttc_facture='';
		$this->coeff_ht='';
		$this->coeff_ttc='';
		$this->daom='';
		$this->numeroconteneur='';
		$this->code_list_control='';
		$this->code_selection='';
		$this->repartition='';
		$this->volume='';
		$this->weight='';
		$this->weight_unit='';
		$this->volume_unit='';
		$this->type_facture='';
		$this->mode_calcul='';
		$this->accounting='';
		$this->note='';

		
	}
	function getList($customsql =''){
	    $fields = $this->customfields->fetchAllCustomFields(1);
	    $this->setFields($fields);
	    $elements = array();
	    $sql = "SELECT l.rowid , l.total_ttc_facture, l.date_facture_fourn, l.date_echeance_facture_fourn, f.ref_ext as referenced_facture, f.rowid as facid ";
	    $sql.= " FROM " . MAIN_DB_PREFIX . $this->table_element . " AS l LEFT JOIN ".MAIN_DB_PREFIX."facture_fourn AS f ON l.fk_facture_fourn = f.rowid ". $customsql;
	    $resql = $this->customfields->executeSQL($sql, "getList for ".$this->table_element);
	    if ($this->db->num_rows($resql) > 0) {
	        $num = $this->db->num_rows($resql);
	        for ($i=0;$i < $num;$i++) {
	            $obj = $this->db->fetch_object($resql);
	            if(!$obj->referenced_facture ){
	                if($obj->facid){
	                    $obj->referenced_facture = "(PROV".$obj->facid.")";
	                }
	            }
	            $elements[] = $obj;
	        }
	    }
	    $this->db->free($resql);
	    return $elements;
	}

}
?>
