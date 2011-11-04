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
 *      \file       dev/skeletons/societe_blocage.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\version    $Id: societe_blocage.class.php,v 1.29 2010/04/29 14:54:13 grandoc Exp $
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2010-09-05 14:53
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/customerblocking/class/c_blocage.class.php");

/**
 *      \class      Societe_blocage
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-09-05 14:53
 */
class Societe_blocage // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='societe_blocage';			//!< Id that identify managed objects
	//var $table_element='societe_blocage';	//!< Name of table without prefix where object is stored

	var $id;

	var $fk_code;
	var $fk_user_author;
	var $fk_soc;
	var $plafond;
	var $depassement;
	var $tms='';
	var $editable_fields = array("fk_code","plafond","depassement");
	var $viewable_fields = array("fk_code","plafond","depassement","tms","fk_user_author");



	/**
	 *      \brief      Constructor
	 *      \param      DB      Database handler
	 */
	function Societe_blocage($DB)
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
			

		$this->fk_user_author = $user->id;

		// Clean parameters

		if (isset($this->fk_code)) $this->fk_code=trim($this->fk_code);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->plafond)) $this->plafond=trim($this->plafond);
		if (isset($this->depassement)) $this->depassement=trim($this->depassement);
		if (isset($this->tms) && $this->tms == '') $this->tms = null;
		



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."societe_blocage(";

		$sql.= "fk_code,";
		$sql.= "fk_user_author,";
		$sql.= "fk_soc,";
		$sql.= "plafond,";
		$sql.= "depassement,";
		$sql.= "tms";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_code)?'NULL':"'".addslashes($this->fk_code)."'").",";
		$sql.= " ".(! isset($this->fk_user_author)?'NULL':"'".$this->fk_user_author."'").",";
		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".addslashes($this->fk_soc)."'").",";
		$sql.= " ".(! isset($this->plafond)?'NULL':"'".$this->plafond."'").",";
		$sql.= " ".(! isset($this->depassement)?'NULL':"'".$this->depassement."'").",";
		$sql.= " ".(! isset($this->tms)?'NULL':"'".$this->tms."'");


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."societe_blocage");

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
	function fetch($id, $fk)
	{
		global $langs;
		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_code,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.fk_soc,";
		$sql.= " t.plafond,";
		$sql.= " t.depassement,";
		$sql.= " t.tms";


		$sql.= " FROM ".MAIN_DB_PREFIX."societe_blocage as t";
		if($fk){
			$sql.= " WHERE t.fk_soc = ".$id;
		}else{
			$sql.= " WHERE t.rowid = ".$id;
		}

		$sql.= " ORDER BY tms DESC LIMIT 1";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_code = $obj->fk_code;
				$this->fk_user_author = $obj->fk_user_author;
				$this->fk_soc = $obj->fk_soc;
				$this->plafond = $obj->plafond;
				$this->depassement = $obj->depassement;
				$this->tms = $this->db->jdate($obj->tms);


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

		if (isset($this->fk_code)) $this->fk_code=trim($this->fk_code);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->plafond)) $this->plafond=trim($this->plafond);
		if (isset($this->depassement)) $this->depassement=trim($this->depassement);



		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."societe_blocage SET";

		$sql.= " fk_code=".(isset($this->fk_code)?"'".addslashes($this->fk_code)."'":"null").",";
		$sql.= " fk_user_author=".(isset($this->fk_user_author)?$this->fk_user_author:"null").",";
		$sql.= " fk_soc=".(isset($this->fk_soc)?"'".addslashes($this->fk_soc)."'":"null").",";
		$sql.= " plafond=".(isset($this->plafond)?$this->plafond:"null").",";
		$sql.= " depassement=".(isset($this->depassement)?$this->depassement:"null").",";
		$sql.= " tms=".(strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null')."";


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

	function getSocieteBlockingStatus(){
		$status = "authorized";
		if($this->fk_code != null){
			$blockCode = new C_blocage($this->db);
			$blockCode->fetch($this->fk_code);
			$status = $blockCode->printBlockCode();
		}elseif($this->getSocieteUnpaid() >= $this->plafond && $this->plafond > 0){
			$status = "Limit Plafond";
		}
		return $status;
	}
	
	function getBlockCodeLabel(){
		
		if($this->fk_code != null){
			$blockCode = new C_blocage($this->db);
			$blockCode->fetch($this->fk_code);
			$label = $blockCode->printBlockCode();
		}
		if ($label == null ){
			$label = "";
		}
		return $label;
	}

	function getSocieteUnpaid(){
		$sql = 'SELECT SUM(t.am) AS credit,  SUM(t.total_ttc) AS debit ';
		$sql.= ' FROM ';
		$sql.= '( SELECT f.rowid AS facid, f.facnumber, f.type, f.increment, f.total, f.total_ttc, f.datef AS df, f.date_lim_reglement AS datelimite, f.paye AS paye, f.fk_statut, s.nom, s.rowid AS socid, SUM( pf.amount ) AS am';

		$sql.= ' FROM llx_societe AS s, llx_facture AS f';
		$sql.= ' LEFT JOIN llx_paiement_facture AS pf ON pf.fk_facture = f.rowid';
		$sql.= ' WHERE f.fk_soc = s.rowid';
		$sql.= ' AND f.entity =1';
		$sql.= ' AND s.rowid ='.$this->fk_soc;
		$sql.= ' GROUP BY f.rowid, f.facnumber, f.type, f.increment, f.total, f.total_ttc, f.datef, f.date_lim_reglement, f.paye, f.fk_statut, s.nom, s.rowid';
		$sql.= ' ORDER BY f.datef DESC , f.rowid DESC';
		$sql.= ' ) AS t';
		$unpaid = 0;
		$resql=$this->db->query($sql);
		if ($resql)
		{
			$var=true;
			$num = $this->db->num_rows($resql);
			$resobj = $this->db->fetch_object($resql);
			if ( $num > 0)
			{
				$unpaid =  $resobj->debit - $resobj->credit;
			}
		}
		return $unpaid;
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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."societe_blocage";
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

		$object=new Societe_blocage($this->db);

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

		$this->fk_code='';
		$this->fk_user_author='';
		$this->fk_soc='';
		$this->plafond='';
		$this->depassement='';
		$this->tms='';
	}

	function form_inputField($field,$html_name){
		global $langs;

		$input = "";
		$input .= '<tr><td width="15%">';
		$input .= $langs->trans($html_name);
		$input .= '</td>';
		$input .= '<td>';
		if($field == "fk_code"){
			$c_block = new C_blocage($this->db);
			$input .= $c_block->select_blockcode($this->$field, $html_name, 1);
		}elseif($field == "depassement"){
			$form = new Form($this->db);
			$input .=$form->selectyesno($html_name,$this->$field,1);
		}elseif( in_array($field, $this->editable_fields) ){
			if($field == 'plafond'){
				$onblur=' onblur="valueFormat(this, 2)" ';
				$input .= '<input '.$onblur.' type="text" name="'.$html_name.'" size="25" value="'.price($this->$field).'"> ';
			}else{
				$input .= '<input type="text" name="'.$html_name.'" size="25" value="'.$this->$field.'"> ';
			}
		}else{
			return "";
		}
		$input .= '</td>';
		$input .= '</tr>';

		return $input;
	}



}
?>
