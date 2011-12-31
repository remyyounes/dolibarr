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
 *      \file       dev/skeletons/societe_remise_prod.class.php
 *      \ingroup    mymodule othermodule1 othermodule2
 *      \brief      This file is an example for a CRUD class file (Create/Read/Update/Delete)
 *		\version    $Id: societe_remise_prod.class.php,v 1.29 2010/04/29 14:54:13 grandoc Exp $
 *		\author		Put author name here
 *		\remarks	Initialy built by build_class_from_table on 2010-09-23 00:22
 */

// Put here all includes required by your class file
//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/makinalib/class/makinaProduct.class.php");


/**
 *      \class      Societe_remise_prod
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-09-23 00:22
 */
class Societe_remise_prod // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='societe_remise_prod';			//!< Id that identify managed objects
	//var $table_element='societe_remise_prod';	//!< Name of table without prefix where object is stored

	var $id;

	var $fk_soc;
	var $fk_categorie_soc;
	var $fk_categorie;
	var $fk_product;
	var $fk_user_author;
	var $productlevel;
	var $qte;
	var $txrem;
	var $cfrem;
	var $prem;
	var $prem2;
	var $active = 1;
	var $tms='';
	var $datec='';
	var $dated='';
	var $datef='';

	private $discountType;
	private $discountOrigin = "Unknown";

	var $editable_fields = array(
	'active',
	'qte',
	'dated',
	'datef'
	);

	var $ajax_fields = array(
		'txrem',
	'cfrem',
	'prem',
	'prem2');

	var $numeric_fields = array(
	'txrem',
	'cfrem',
	'prem',
	'prem2',
	'qte'
	);

	var $date_fields = array( 'datef','dated');


	/**
	 *      \brief      Constructor
	 *      \param      DB      Database handler
	 */
	function Societe_remise_prod($DB)
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

		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->fk_categorie_soc)) $this->fk_categorie_soc=trim($this->fk_categorie_soc);
		if (isset($this->fk_categorie)) $this->fk_categorie=trim($this->fk_categorie);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->productlevel)) $this->productlevel=trim($this->productlevel);
		if (isset($this->qte)) $this->qte=trim($this->qte);
		if (isset($this->txrem)) $this->txrem=trim($this->txrem);
		if (isset($this->cfrem)) $this->cfrem=trim($this->cfrem);
		if (isset($this->prem)) $this->prem=trim($this->prem);
		if (isset($this->prem2)) $this->prem2=trim($this->prem2);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add control on parameters values

		// Insert request
		$sql = "INSERT INTO ".MAIN_DB_PREFIX."societe_remise_prod(";

		$sql.= "fk_soc,";
		$sql.= "fk_categorie_soc,";
		$sql.= "fk_categorie,";
		$sql.= "fk_product,";
		$sql.= "fk_user_author,";
		$sql.= "productlevel,";
		$sql.= "qte,";
		$sql.= "txrem,";
		$sql.= "cfrem,";
		$sql.= "prem,";
		$sql.= "prem2,";
		$sql.= "active,";
		$sql.= "datec,";
		$sql.= "dated,";
		$sql.= "datef";


		$sql.= ") VALUES (";

		$sql.= " ".(! isset($this->fk_soc)?'NULL':"'".$this->fk_soc."'").",";
		$sql.= " ".(! isset($this->fk_categorie_soc)?'NULL':"'".$this->fk_categorie_soc."'").",";
		$sql.= " ".(! isset($this->fk_categorie)?'NULL':"'".$this->fk_categorie."'").",";
		$sql.= " ".(! isset($this->fk_product)?'NULL':"'".$this->fk_product."'").",";
		$sql.= " ".(! isset($this->fk_user_author)?'NULL':"'".$this->fk_user_author."'").",";
		$sql.= " ".(! isset($this->productlevel)?'NULL':"'".$this->productlevel."'").",";
		$sql.= " ".(! isset($this->qte)?'NULL':"'".$this->qte."'").",";
		$sql.= " ".(! isset($this->txrem)?'NULL':"'".$this->txrem."'").",";
		$sql.= " ".(! isset($this->cfrem)?'NULL':"'".$this->cfrem."'").",";
		$sql.= " ".(! isset($this->prem)?'NULL':"'".$this->prem."'").",";
		$sql.= " ".(! isset($this->prem2)?'NULL':"'".$this->prem2."'").",";
		$sql.= " ".(! isset($this->active)?'NULL':"'".$this->active."'").",";
		$sql.= " ".(! isset($this->datec) || strlen($this->datec)==0?'NULL':$this->db->idate($this->datec)).",";
		$sql.= " ".(! isset($this->dated) || strlen($this->dated)==0?'NULL':$this->db->idate($this->dated)).",";
		$sql.= " ".(! isset($this->datef) || strlen($this->datef)==0?'NULL':$this->db->idate($this->datef))."";


		$sql.= ")";

		$this->db->begin();

		dol_syslog(get_class($this)."::create sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if (! $resql) { $error++; $this->errors[]="Error ".$this->db->lasterror(); }

		if (! $error)
		{
			$this->id = $this->db->last_insert_id(MAIN_DB_PREFIX."societe_remise_prod");

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



	function fetch($clientOrCategoryKey, $productOrCategoryKey = null, $discountOrigin = null)
	{
		global $langs;

		$this->setDiscountOrigin($discountOrigin);

		$sql = "SELECT";
		$sql.= " t.rowid,";

		$sql.= " t.fk_soc,";
		$sql.= " t.fk_categorie_soc,";
		$sql.= " t.fk_categorie,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.productlevel,";
		$sql.= " t.qte,";
		$sql.= " t.txrem,";
		$sql.= " t.cfrem,";
		$sql.= " t.prem,";
		$sql.= " t.prem2,";
		$sql.= " t.active,";
		$sql.= " t.tms,";
		$sql.= " t.datec,";
		$sql.= " t.dated,";
		$sql.= " t.datef";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe_remise_prod as t";

		if($discountOrigin === null ){
			$sql.= " WHERE t.rowid = ".$clientOrCategoryKey;
		}
		elseif($discountOrigin == "ClientOnProduct" ){

			$this->fk_soc = $clientOrCategoryKey;
			$this->fk_product = $productOrCategoryKey;
			$sql.= " WHERE t.fk_soc = ".$clientOrCategoryKey." AND t.fk_product = ".$productOrCategoryKey;
		}
		elseif($discountOrigin == "ClientOnCategoryProduct" ){
			$this->fk_soc = $clientOrCategoryKey;
			$this->fk_categorie = $productOrCategoryKey;
			$sql.= " WHERE t.fk_soc = ".$clientOrCategoryKey." AND t.fk_categorie = ".$productOrCategoryKey;
		}
		elseif($discountOrigin == "CategoryClientOnProduct" ){
			$this->fk_categorie_soc = $clientOrCategoryKey;
			$this->fk_product = $productOrCategoryKey;
			$sql.= " WHERE t.fk_categorie_soc = ".$clientOrCategoryKey." AND t.fk_product = ".$productOrCategoryKey;
		}
		elseif($discountOrigin == "CategoryClientOnCategoryProduct" ){
			$this->fk_categorie_soc = $clientOrCategoryKey;
			$this->fk_categorie = $productOrCategoryKey;
			$sql.= " WHERE t.fk_categorie_soc = ".$clientOrCategoryKey." AND t.fk_categorie = ".$productOrCategoryKey;
		}
		elseif($discountOrigin == "Product" ){
			$this->fk_product = $productOrCategoryKey;
			$sql.= " WHERE t.fk_product = ".$productOrCategoryKey;
		}

		$sql.= " ORDER BY t.datec DESC";
		$sql.= " LIMIT 1";

		dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
		$resql=$this->db->query($sql);
		if ($resql)
		{
			if ($this->db->num_rows($resql))
			{
				$obj = $this->db->fetch_object($resql);

				$this->id    = $obj->rowid;

				$this->fk_soc = $obj->fk_soc;
				$this->fk_categorie_soc = $obj->fk_categorie_soc;
				$this->fk_categorie = $obj->fk_categorie;
				$this->fk_product = $obj->fk_product;
				$this->fk_user_author = $obj->fk_user_author;
				$this->productlevel = $obj->productlevel;
				$this->qte = $obj->qte;
				$this->txrem = $obj->txrem;
				$this->cfrem = $obj->cfrem;
				$this->prem = $obj->prem;
				$this->prem2 = $obj->prem2;
				$this->active = $obj->active;
				$this->tms = $this->db->jdate($obj->tms);
				$this->datec = $this->db->jdate($obj->datec);
				$this->dated = $this->db->jdate($obj->dated);
				$this->datef = $this->db->jdate($obj->datef);

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

		if (isset($this->fk_soc)) $this->fk_soc=trim($this->fk_soc);
		if (isset($this->fk_categorie_soc)) $this->fk_categorie_soc=trim($this->fk_categorie_soc);
		if (isset($this->fk_categorie)) $this->fk_categorie=trim($this->fk_categorie);
		if (isset($this->fk_product)) $this->fk_product=trim($this->fk_product);
		if (isset($this->fk_user_author)) $this->fk_user_author=trim($this->fk_user_author);
		if (isset($this->productlevel)) $this->productlevel=trim($this->productlevel);
		if (isset($this->qte)) $this->qte=trim($this->qte);
		if (isset($this->txrem)) $this->txrem=trim($this->txrem);
		if (isset($this->cfrem)) $this->cfrem=trim($this->cfrem);
		if (isset($this->prem)) $this->prem=trim($this->prem);
		if (isset($this->prem2)) $this->prem2=trim($this->prem2);
		if (isset($this->active)) $this->active=trim($this->active);



		// Check parameters
		// Put here code to add control on parameters values

		// Update request
		$sql = "UPDATE ".MAIN_DB_PREFIX."societe_remise_prod SET";

		$sql.= " fk_soc=".(isset($this->fk_soc)?$this->fk_soc:"null").",";
		$sql.= " fk_categorie_soc=".(isset($this->fk_categorie_soc)?$this->fk_categorie_soc:"null").",";
		$sql.= " fk_categorie=".(isset($this->fk_categorie)?$this->fk_categorie:"null").",";
		$sql.= " fk_product=".(isset($this->fk_product)?$this->fk_product:"null").",";
		$sql.= " fk_user_author=".(isset($this->fk_user_author)?$this->fk_user_author:"null").",";
		$sql.= " productlevel=".(isset($this->productlevel)?$this->productlevel:"null").",";
		$sql.= " qte=".(isset($this->qte)?$this->qte:"null").",";
		$sql.= " txrem=".(isset($this->txrem)?$this->txrem:"null").",";
		$sql.= " cfrem=".(isset($this->cfrem)?$this->cfrem:"null").",";
		$sql.= " prem=".(isset($this->prem)?$this->prem:"null").",";
		$sql.= " prem2=".(isset($this->prem2)?$this->prem2:"null").",";
		$sql.= " active=".(isset($this->active)?$this->active:"null").",";
		$sql.= " tms=".(strlen($this->tms)!=0 ? "'".$this->db->idate($this->tms)."'" : 'null').",";
		$sql.= " datec=".(strlen($this->datec)!=0 ? "'".$this->db->idate($this->datec)."'" : 'null').",";
		$sql.= " dated=".(strlen($this->dated)!=0 ? "'".$this->db->idate($this->dated)."'" : 'null').",";
		$sql.= " datef=".(strlen($this->datef)!=0 ? "'".$this->db->idate($this->datef)."'" : 'null')."";


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

		$sql = "DELETE FROM ".MAIN_DB_PREFIX."societe_remise_prod";
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

		$object=new Societe_remise_prod($this->db);

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

		$this->fk_soc='';
		$this->fk_categorie_soc='';
		$this->fk_categorie='';
		$this->fk_product='';
		$this->fk_user_author='';
		$this->productlevel='';
		$this->qte='';
		$this->txrem='';
		$this->cfrem='';
		$this->prem='';
		$this->prem2='';
		$this->active='';
		$this->tms='';
		$this->datec='';
		$this->dated='';
		$this->datef='';


	}

	function form_inputField($field,$html_name)
	{
		global $langs, $conf;



		$input = "";
		$input .= '<tr><td width="15%">';
		$input .= $langs->trans($html_name);
		$input .= '</td>';
		$input .= '<td>';
		if( in_array($field, $this->numeric_fields) ){
			if(!$this->$field){
				$this->$field = 0;
			}
			$input .= '<input type="text" name="'.$html_name.'" size="9" value="'.$this->$field.'"> ';
		}elseif(in_array($field,$this->date_fields)){
			$html = new Form($this->db);
			echo $input;
			$input = "";
			print $html->select_date('',$html_name,'','',1,'update');
		}elseif($field == "active"){
			$html = new Form($this->db);
			$html->selectyesno($html_name,1,1);
			$input .= $html->selectyesno($html_name,1,1);
		}elseif(in_array($field,$this->editable_fields)){
			$input .= '<input type="text" name="'.$html_name.'" size="25" value="'.$this->$field.'"> ';
		}else{
			return "";
		}
		$input .= '</td>';
		$input .= '</tr>';

		return $input;
	}

	function getDiscountPrice($productId){
		global $conf,$langs;
		$product = new MakinaProduct($this->db);
		$product->fetch($productId);
		$publicPrice = $product->getPublicPrice();
		$discountPrice = $publicPrice * ( 1 - $this->getDiscountPct($productId) / 100 );


		//TODO: check validity
		//=====================================
		//===== PLACE DISCOUNT CHECK HERE =====
		//=====================================

		if($discountPrice <= 0){
			$this->error = $langs->trans("FinalPriceTooLow");
			$discountPrice = -1;
		}
		return $discountPrice;
	}

	function getDiscountPct($productId){
		global $conf;

		$product = new MakinaProduct($this->db);
		$product->fetch($productId);
		$publicPrice = $product->getPublicPrice();
		$newPrice = 0;
		if($publicPrice <= 0){
			return -1;
		}

		$discountType = $this->getDiscountType();

		switch ($discountType) {
			case 'coeff':
				return $this->getDiscountValue();
				break;
			case 'taux':
				$newPrice = $publicPrice - $this->getDiscountValue();
				break;
			case 'prix':
				$newPrice = $this->getDiscountValue();
				break;
			case 'level':
				$newPrice = $product->multiprices[ $this->getDiscountValue()];
				break;
			default:
				$newPrice = $publicPrice;
		}

		//TODO: check validity
		//=====================================
		//===== PLACE DISCOUNT CHECK HERE =====
		//=====================================

		$pct = 0;
		if($newPrice > 0){
			$pct = (1 - $newPrice / $publicPrice) * 100;
		}
		return $pct;
	}

	private function _fetchDiscountType(){

		$this->setDiscountType( null );

		if($this->cfrem != null){
			$this->setDiscountType( "coeff" );
		}
		if($this->txrem != null){
			$this->setDiscountType( "taux" );
		}
		if($this->prem != null){
			$this->setDiscountType( "prix" );
		}
		if($this->productlevel != null){
			$this->setDiscountType( "level" );
		}
	}

	private function _fetchDiscountValue(){

		$discountType = $this->getDiscountType();

		switch ($discountType) {
			case 'coeff':
				$this->setDiscountValue( $this->cfrem );
				break;
			case 'taux':
				$this->setDiscountValue( $this->txrem );
				break;
			case 'prix':
				$this->setDiscountValue( $this->prem );
				break;
			case 'level':
				$this->setDiscountValue( $this->productlevel );
				break;
			default:
				$this->setDiscountValue( null );
		}
	}


	public function getDiscountValue(){
		if(!$this->discountValue){
			$this->_fetchDiscountValue();
		}
		return $this->discountValue;
	}

	public function getDiscountType(){
		if(!$this->discountType){
			$this->_fetchDiscountType();
		}
		return $this->discountType;
	}

	public function getDiscountOrigin(){
		return $this->discountOrigin;
	}

	public function setDiscountValue($value){
		$this->discountValue = $value;
	}

	public function setDiscountType($type){
		$this->discountType = $type;
	}

	public function setDiscountOrigin($origin){
		$this->discountOrigin = $origin;
	}


	public function isDiscountValid(){
		//TODO:
		$dateValid = true;
		return ($dateValid && $this->active);
	}

	public function getMinQty(){
		$this->qte = ($this->qte)?$this->qte:0;
		return $this->qte;
	}

}
?>
