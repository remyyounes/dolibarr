<?php 

$numTables = count($object->tabname);
$i = $numTables+1;

$object->tabname[$i]= MAIN_DB_PREFIX."c_blocage";
$object->tablib[$i]= $langs->trans("DictionnaryCustomerBlocking");
$object->tabsql[$i]= "SELECT t.rowid as rowid, p.libelle as pays, p.code as pays_code, t.code, t.libelle, t.active FROM ".MAIN_DB_PREFIX."c_blocage as t,  llx_c_pays as p WHERE t.fk_pays=p.rowid";
$object->tabsqlsort[$i]="code ASC";
$object->tabfield[$i]= "code,libelle,pays_id,pays";
$object->tabfieldvalue[$i]= "code,libelle,pays";
$object->tabfieldinsert[$i]= "code,libelle,fk_pays";
$object->tabrowid[$i]= "";
$object->tabcond[$i]= $conf->customerblocking->enabled;

$object->taborder[]=$i;
?>