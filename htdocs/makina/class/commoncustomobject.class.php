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
 *      \file       htdocs/makina/class/commoncustomobject.class.php
 *      \ingroup    makina
 *      \brief      Super class for custommodule classes
 *		\author		Remy Younes
 */

//require_once(DOL_DOCUMENT_ROOT."/core/class/commonobject.class.php");
//require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
//require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/makina/class/commoncustomobject.class.php");
require_once(DOL_DOCUMENT_ROOT."/customfields/class/customfields.class.php");
require_once(DOL_DOCUMENT_ROOT."/customfields/lib/customfields.lib.php");


/**
 *      \class      CommonCustomObject
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2011-11-29 15:24
 */
class CommonCustomObject
{
    var $db;							//!< To store db handler
    var $error;							//!< To return error code (or message)
    var $errors=array();				//!< To return several error codes (or messages)
    var $id;
    protected $fields;

    /**
     *  Constructor
     *
     *  @param      DoliDb		$DB      Database handler
     */
    function CommonCustomObject($DB)
    {
        $this->db = $DB;
        return 1;
    }


    function createCustom($object, $notrigger=0, $forceinsert = 0)
    {
        // Get all the columns (custom fields)
        $fields = $this->fetchAllCustomFields(false,0,1);

        if (empty($fields)) return null;

        // Forging the SQL statement
        $sqlfields = '';
        foreach ($fields as $field) {
            $key = $this->varprefix.$field->column_name;
            if (!isset($object->$key)) {
                $key = $field->column_name;
            }else{
                //We need to fetch the correct value when we update a date field
                if($field->data_type == 'date'){
                    $object->$key = $this->db->idate(dol_mktime(0, 0, 0, $object->{$key.'month'}, $object->{$key.'day'}, $object->{$key.'year'}));
                }
            }
             
            if(preg_match('/^(.*)_((auto).*)$/i', $key, $matches)){
                //constraints
                if(!empty($field->referenced_table_name)){
                    //users
                    if($field->referenced_table_name == MAIN_DB_PREFIX.'user'){
                        $object->$key = $GLOBALS['user']->id;
                    }
                }else{
                    //date / datetime / timestamp
                    if($field->data_type == 'date' || $field->data_type == 'datetime' || $field->data_type == 'timestamp'){
                        $object->$key = $this->db->idate(dol_now());
                    }
                }
            }

            if ($object->$key) {
                // Only insert/update this field if it was submitted
                if ($sqlfields != '') {
                    $sqlfields.=',';
                }
                $sqlfields.=$field->column_name."='".$this->escape($object->$key)."'";
            }
        }
        if ($sqlfields != '') {
            $sqlfields.=',';
        } // in the case that all fields are empty, this one can be the only one submitted, so we have to put the comma only if it's not alone (or else sql syntax error)
        $sqlfields.="fk_".$this->module."=".$object->id; // we add the object id (filtered by fetchAllCustomFields)

        $result = $this->fetch($object->id);

        if (!empty($result) and count($result) > 0 and !$forceinsert) {
            // if the record already exists for this facture id, we update it
            $sql = "UPDATE ".$this->moduletable." SET ".$sqlfields." WHERE fk_".$this->module."=".$object->id;
        } else { // else we insert a new record
            $sql = "INSERT INTO ".$this->moduletable." SET ".$sqlfields;
        }

        // Trigger or not?
        if ($notrigger) {
            $trigger = null;
        } else {
            $trigger = strtoupper($this->module).'_CUSTOMFIELD_CREATEUPDATERECORD';
        }

        // Executing the SQL statement
        $rtncode = $this->executeSQL($sql, 'createOrUpdateRecord_CustomFields',$trigger);

        $this->id = $this->db->last_insert_id($this->moduletable);

        return $rtncode;
    }


    /**
     *  Show Create Form
     *
     *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return     string      		 HTML Form
     */
    function printCreateForm($action='add', $hidden_fields = null)
    {
        global $conf, $langs;
        $error=0;

        print "<form action=\"fiche.php\" method=\"post\">\n";
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        foreach($hidden_fields as $hf){
            print '<input type="hidden" name="'.$hf[0].'" value="'.$hf[1].'">';
        }
        print '<input type="hidden" name="action" value="'.$action.'">'."\n";

        //TODO: Move somewhereelse
        //dol_htmloutput_mesg($mesg);

        print '<table class="border" width="100%">';

        $action = 'create';
        $fields_data = $this->loadFields($action);
        include($this->templates->datasheet);
        print '</table>';
        print '<center><br><input type="submit" class="button" value="'.$langs->trans("Create").'"></center>';
        print '</form>';
    }


    /**
     *  Show Edit Form
     *
     *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return     string      		 HTML Form
     */
    function printEditForm($action='update',$hidden_fields = null)
    {
        global $conf, $langs;
        $error=0;

        print "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
        print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
        print '<input type="hidden" name="action" value="'.$action.'">'."\n";
        print '<input type="hidden" name="id" value="'.$this->id.'">';
        foreach($hidden_fields as $hf){
            print '<input type="hidden" name="'.$hf[0].'" value="'.$hf[1].'">';
        }
        print '<table class="border" width="100%">';

        $action = 'edit';
        $fields_data = $this->loadFields($action);
        include($this->templates->datasheet);

        print '</table>';
        print '<tr><td colspan="4" align="center"><input type="submit" class="button" value="'.$langs->trans("Save").'">';
        print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td></tr>';
        print '</form>';
    }

    /**
     *  Show Data Sheet
     *
     *  @param      int		$notrigger   0=launch triggers after, 1=disable triggers
     *  @return     string      		 HTML
     */
    function printDataSheet( )
    {
        global $conf, $langs;
        $error=0;
        print '<table class="border" width="100%">';
        $action = '';
        $fields_data = $this->loadFields($action);
        include($this->templates->datasheet);

        print '</table>';
    }


    function loadFields($action){
        global $db, $langs, $conf;

        // Init and main vars
        $fields_data = array();

        if ($this->customfields->probeCustomFields()) {
            // == Fetching customfields
            $fields = $this->customfields->fetchAllCustomFields(1); // fetching the customfields list
            $this->setFields($fields);
            //$datas = $this->customfields->fetchByRowid($this->id); // fetching the record - the values of the customfields for this id (if it exists)
            if($action != 'create'){
                $this->fetch($this->id);
            }
            //$datas->id = $this->id; // in case the record does not yet exist for this id, we at least set the id property of the datas object (useful for the update later on)

            foreach ($fields as $field) {
                // for each customfields, we will print/save the edits

                // == Default values from database record
                $name = $field->column_name; // the name of the customfield (which is the property of the record)
                $value = ''; // by default the value of this property is empty
                if (isset($this->$name)) {
                    $value = $this->$name;
                } // if the property exists (the record is not empty), then we fill in this value

                $currentfield_data = array();
                $currentfield_data['label'] = $this->customfields->findLabel($name);
                $rightok = customfields_check_right($currentmodule,$user,$rights);

                // load the editing form...
                if (($action == 'editcustomfields' && GETPOST('field') == $name) || $action == 'edit' || $action == 'create') {
                    $currentfield_data['data'] = $this->showInputField($field, $value);
                } else { // ... or load the field's value
                    $currentfield_data['data'] = $this->printField($field, $value);
                }
                $fields_data[$name] = $currentfield_data;
            }
        }
        return $fields_data;
    }
    /*
     * $this->customTypes['fk_facture_fourn'] = 'facture_fourn';
    $this->customTypes['fk_stockentry'] = 'stockentry';
    $this->customTypes['fk_entrepot'] = 'entrepot';
    */
    function showInputField($field, $value){
        $out = '';
        $column_name = $field->column_name;
        $filter ='';
        if(!empty($this->customTypes[$column_name])){
            $type = $this->customTypes[$column_name];
            $htmlname= $this->customfields->varprefix . $column_name;
            if ($type == 'fournisseur'){
                $filter =' s.fournisseur = 1 ';
                $form=new Form($this->db);
                //($socid,$selected='',$htmlname='productid',$filtertype='',$filtre)
                $out .= $form->select_company($value,$htmlname,$filter);
            }elseif ($type == 'facture_fourn'){
                require_once(DOL_DOCUMENT_ROOT."/makina/class/html.form2.class.php");
                $form2=new Form2($this->db);
                $out .= $form2->select_factures_fournisseurs($this->fk_societe,$value, $htmlname);
            }elseif ($type == 'entrepot'){
                require_once(DOL_DOCUMENT_ROOT."/makina/class/html.form2.class.php");
                $form2=new Form2($this->db);
                $out .= $form2->select_entrepots($this->fk_stock,$value, $htmlname);
            }else{
                $out .= $this->customfields->ShowInputField($field, $value);
            }
        }else{
            $out .= $this->customfields->ShowInputField($field, $value);
        }
        return $out;
    }

    function printField($field, $value){
        $out = '';
        $column_name = $field->column_name;
        if(!empty($this->customTypes[$column_name])){
            $type = $this->customTypes[$column_name];
            if ($type == 'fournisseur'){
                $soc = new Societe($this->db);
                $soc->fetch($value);
                $out .= $soc->nom;
            }elseif ($type == 'stockentry'){
                $stockEntry = new Stockentry($this->db);
                $stockEntry->fetch($value);
                $out .= $stockEntry->numerodossier;
            }elseif ($type == 'facture_fourn' && $value){
                include_once(DOL_DOCUMENT_ROOT."/fourn/class/fournisseur.facture.class.php");
                $ff = new FactureFournisseur($this->db);
                $ff->fetch($value);
                $ref = $ff->ref_ext?$ff->ref_ext:"(PROV".$ff->rowid.")";
                $out .= $ref;
            }elseif ($type == 'entrepot'){
                include_once(DOL_DOCUMENT_ROOT."/product/stock/class/entrepot.class.php");
                $entrepot = new Entrepot($this->db);
                $entrepot->fetch($value);
                $out .= $entrepot->lieu;
            }elseif($type == 'price'){
                $out .= price($value);
            }
        }else{
            $type = $field->data_type;
            if($type == 'date'){
                if($value){
                    //check if datestring is already converted to dateint
                    if(!is_numeric($value)) $value = strtotime($value);
                    $out .= date("d/m/y", $value);
                }
            }else{
                $out .= $this->customfields->printField($field, $value);
            }
        }
        return $out;
    }

    function printList($customsql =''){
        global $langs;
        $list =  $this->getList($customsql);
        include($this->templates->list);
    }

    function getList($customsql =''){
        $fields = $this->customfields->fetchAllCustomFields(1);
        $this->setFields($fields);
        $elements = array();
        $sql = "SELECT * FROM " . MAIN_DB_PREFIX . $this->table_element . $customsql;
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

    function getId(){
        return $this->id;
    }

    function getTableName(){
        return $this->table_element;
    }

    function setFields($fields){
        if(is_null($this->fields)){
            $this->fields = new stdclass();
        }
        foreach($fields as $field){
            $field_column_name = $field->column_name;
            $this->fields->$field_column_name = $field;
        }
    }

    function validateFields(){
        $validate = true;
        $fields = $this->customfields->fetchAllCustomFields(1);
        $this->setFields($fields);

        /*
         $is_nullable;
        $data_type;
        $column_type;
        $column_default;
        */
         
        foreach($fields as $field){

            $column_name = $field->column_name;



            if( !$this->$column_name && $this->$column_name !== '0' && $field->is_nullable == 'NO'){
                $this->$column_name = $field->column_default;
                if($field->data_type == 'date'){
                    $this->$column_name = dol_now();
                }
            }elseif($field->data_type == 'date'){
                $this->$column_name = dol_mktime(0, 0, 0, $this->{$column_name.'month'}, $this->{$column_name.'day'}, $this->{$column_name.'year'});
            }
        }


        return $validate ;
    }

    function getPostValues(){
        //get POST_VALUES
        foreach ($_POST as $key=>$value) {
            // Generic way to fill all the fields to the object (particularly useful for triggers and customfields)
            $custom_val = strstr($key, 'cf_');
            if($custom_val){
                $newKey =  str_ireplace ( 'cf_' , '', $key);
                $this->$newKey =  $value;
            }
        }
    }

}
?>
