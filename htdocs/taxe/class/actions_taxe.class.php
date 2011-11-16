<?php

class ActionsTaxe
{

    function __construct($DB){
        $this->db = $DB;
    }

    function viewDictionaryFieldlist($parameters, $object, $action){
        global $langs;

        //exit if taxe module does not handle this table
        if( !$this->ownsDictionary($parameters['tabname']) ) return;

        $obj = $object;
        $fieldlist = $parameters['fieldlist'];
        foreach ($fieldlist as $field => $value)
        {
            $showfield=1;
            $valuetoshow=$obj->$fieldlist[$field];
            if ($valuetoshow=='all') {
                $valuetoshow=$langs->trans('All');
            }
            else if ($fieldlist[$field]=='pays') {
                if (empty($obj->pays_code))
                {
                    $valuetoshow='-';
                }
                else
                {
                    $key=$langs->trans("Country".strtoupper($obj->pays_code));
                    $valuetoshow=($key != "Country".strtoupper($obj->pays_code))?$obj->pays_code." - ".$key:$obj->pays;
                }
            }
            else if ($fieldlist[$field]=='region_id' || $fieldlist[$field]=='pays_id') {
                $showfield=0;
            }
            else if($fieldlist[$field] == "taxetype"){
                $taxeTypes=$this->getTaxeTypesArray();
                $valuetoshow=$taxeTypes[ strtolower($valuetoshow) ];
            }
            if ($showfield) print '<td>'.$valuetoshow.'</td>';
        }
        return 1;

    }
    
    function editDictionaryFieldlist($parameters, $object, $action){
        //exit if taxe module does not handle this table
        if( !$this->ownsDictionary($parameters['tabname']) ) return;

        global $conf,$langs,$db;
        global $region_id;
        global $elementList,$sourceList;

        $html = new Form($db);
        $formadmin = new FormAdmin($db);
        $formcompany = new FormCompany($db);
        $fieldlist = $parameters['fieldlist'];
        $obj = $object;

        foreach ($fieldlist as $field => $value)
        {

            if ($fieldlist[$field] == 'pays') {
                if (in_array('region_id',$fieldlist)) { print '<td>&nbsp;</td>'; continue; }	// For region page, we do not show the country input
                print '<td>';
                $html->select_pays($obj->pays,'pays');
                print '</td>';
            }
            elseif ($fieldlist[$field] == 'pays_id') {
                $pays_id = (! empty($obj->$fieldlist[$field])) ? $obj->$fieldlist[$field] : 0;
                print '<input type="hidden" name="'.$fieldlist[$field].'" value="'.$pays_id.'">';
            }
            elseif ($fieldlist[$field] == 'region') {
                print '<td>';
                $formcompany->select_region($region_id,'region');
                print '</td>';
            }
            elseif ($fieldlist[$field] == 'region_id') {
                $region_id = $obj->$fieldlist[$field]?$obj->$fieldlist[$field]:0;
                print '<input type="hidden" name="'.$fieldlist[$field].'" value="'.$region_id.'">';
            }
            elseif ($fieldlist[$field] == 'code') {
                print '<td><input type="text" class="flat" value="'.$obj->$fieldlist[$field].'" size="10" name="'.$fieldlist[$field].'"></td>';
            }
            elseif ($fieldlist[$field] == 'taxetype') {
                $taxeTypes = $this->getTaxeTypesArray();
                print '<td>';
                $html->select_array($fieldlist[$field],$taxeTypes,strtolower($obj->$fieldlist[$field]),0,0,1);
                print '</td>';
            }
            else
            {
                print '<td>';
                print '<input type="text" '.($fieldlist[$field]=='libelle'?'size="32" ':'').' class="flat" value="'.$obj->$fieldlist[$field].'" name="'.$fieldlist[$field].'">';
                print '</td>';
            }
        }
        return 1;
    }

    function createDictionaryFieldlist($parameters, $object, $action){
        return $this->editDictionaryFieldlist($parameters, $object, $action);
    }
    
    function ownsDictionary($tabname){
        include_once(DOL_DOCUMENT_ROOT ."/includes/modules/modTaxe.class.php");
        $moduleDescriptor = new modTaxe($this->db);
        if( in_array($tabname, $moduleDescriptor->dictionnaries['tabname']) ) {
            return true;
        }else{
            return false;
        }
    }

    function getTaxeTypesArray(){
        global $langs;
        $taxeTypes = array();
        $taxeTypes['c']=$langs->trans("Coefficient");
        $taxeTypes['t']=$langs->trans("Taux");
        $taxeTypes['v']=$langs->trans("Valeur");
        return $taxeTypes;
    }
}