<?php

//echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
require_once("../main.inc.php");
require_once(DOL_DOCUMENT_ROOT."/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");

echo "<html>";
echo "<head>";


// START INCLUDES
//echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/includes/scriptaculous/lib/prototype.js"></script>';
//echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/fastinit.js"></script>';
//echo '<script type="text/javascript" src="'.DOL_URL_ROOT.'/makinalib/js/tablekit.js"></script>';
//echo '<link rel="stylesheet" type="text/css" media="all" href="'.DOL_URL_ROOT.'/makinalib/css/tablekit.css">';
// END INCLUDES
	

echo "</head>"; 
echo "<body>";
echo '<center>';



$possibleFilters = array("ref","label","barcode","price","datem");
foreach($possibleFilters as $f){
	$$f = isset($_GET["$f"])?$_GET["$f"]:$_POST["$F"];
}
//$sref=isset($_GET["sref"])?$_GET["sref"]:$_POST["sref"];

$sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,';
$sql.= ' p.fk_product_type, p.tms as datem,';
$sql.= ' p.duration';
$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
// We'll need this table joined to the select in order to filter by categ
/*
if ($search_categ) $sql.= ", ".MAIN_DB_PREFIX."categorie_product as cp";
if ($_GET["fourn_id"] > 0)
{
	$fourn_id = $_GET["fourn_id"];
	$sql.= ", ".MAIN_DB_PREFIX."product_fournisseur as pf";
}
*/
$sql.= " WHERE  p.entity = ".$conf->entity;
/*
if ($search_categ) $sql.= " AND p.rowid = cp.fk_product";	// Join for the needed table to filter by categ
if (!$user->rights->produit->hidden) $sql.=' AND (p.hidden=0 OR p.fk_product_type != 0)';
if (!$user->rights->service->hidden) $sql.=' AND (p.hidden=0 OR p.fk_product_type != 1)';
if ($sall)
{
	$sql.= " AND (p.ref like '%".addslashes($sall)."%' OR p.label like '%".addslashes($sall)."%' OR p.description like '%".addslashes($sall)."%' OR p.note like '%".addslashes($sall)."%')";
}
# if the type is not 1, we show all products (type = 0,2,3)
if (strlen($_GET["type"]) || strlen($_POST["type"]))
{
	if ($type==1) {
		$sql.= " AND p.fk_product_type = '1'";
	} else {
		$sql.= " AND p.fk_product_type <> '1'";
	}
}
*/
if ($ref)     $sql.= " AND p.ref like '%".$ref."%'";
if ($barcode) $sql.= " AND p.barcode like '%".$barcode."%'";
if ($label)     $sql.= " AND p.label like '%".addslashes($label)."%'";

/*
if($catid)
{
	$sql.= " AND cp.fk_categorie = ".$catid;
}

if ($fourn_id > 0)
{
	$sql.= " AND p.rowid = pf.fk_product AND pf.fk_soc = ".$fourn_id;
}
// Insert categ filter
if ($search_categ)
{
	$sql .= " AND cp.fk_categorie = ".addslashes($search_categ);
}

*/
//====================
//====================
$limit = 200;
//====================
//====================

$sql.= $db->order($sortfield,$sortorder);
$sql.= $db->plimit($limit + 1 ,$offset);
$resql = $db->query($sql) ;
if ($resql)
{
	$num = $db->num_rows($resql);

	$i = 0;

		echo '<form id="filterform" onsubmit="filterSearch();return false;">';
	print "\n".'<table id="product_table" class="sortable resizable" width="801px" >';
	// Lignes des titres
		echo "\n ".'<thead>';
		
		$filters = array('ref','label','barcode','datemodif','sellingprice');
		$filter_inputs = array();
		foreach($filters as $f){
			if($f == 'datemodif' || $f == 'sellingprice'){
				$disabledInput = ' disabled="true" ';
			}else{
				$disabledInput = '';
			}
			$filter_inputs[$f] = '<br><input '.$disabledInput.' type="text" id="filter_'.$f.'" value="'.$_GET[$f].'">';
		}
		
		echo "\n  ".'<tr class="liste_titre">';
		echo "\n  ".'<th class="sortfirstdec">'.$langs->trans("Ref").$filter_inputs['ref'].'</th>';
		echo "\n  ".'<th>'.$langs->trans("Label").$filter_inputs['label'].'</th>';
		if ($conf->barcode->enabled){
			echo "\n  ".'<th class="number">'.$langs->trans("BarCode").$filter_inputs['barcode'].'</th>';
		}
		echo "\n  ".'<th>'.$langs->trans("DateModification").$filter_inputs['datemodif'].'</th>';
		echo "\n  ".'<th>'.$langs->trans("SellingPrice").$filter_inputs['sellingprice'].'</th>';
		print "\n  "."</tr>\n";
		
		echo " </thead>\n";
	
		
	
	$product_static=new Product($db);

	$rowStyle = array("roweven","rowodd");
	$var=True;
	while ($i < min($num,$limit))
	{
		
		$objp = $db->fetch_object($resql);


		$var=!$var;
		
	//	print "\n<tr ".$rowStyle[$var]." onclick=\"Modalbox.hide()\" >";
		print "\n<tr ".$rowStyle[$var]." onclick=\"selectProduct('".$objp->rowid."','".$objp->ref."','".$objp->label."')\" >";
		
		// Ref
		print "\n  ".'<td nowrap="nowrap">';
		$product_static->id = $objp->rowid;
		$product_static->ref = $objp->ref;
		$product_static->type = $objp->fk_product_type;
		//print $product_static->getNomUrl(1,'',24);
		echo '<b>'.$objp->ref.'</b>';
		print "</td>";

		// Label
		print "\n  ".'<td>'.dol_trunc($objp->label,15).'</td>';

		// Barcode
		if ($conf->barcode->enabled)		{
			print "\n  ".'<td align="right">'.$objp->barcode.'</td>';
		}

		// Date
		print "\n  ".'<td align="center">'.dol_print_date($db->jdate($objp->datem),'day')."</td>";

		// Price
		print "\n  ".'<td align="right">';
		if ($objp->price_base_type == 'TTC') print price($objp->price_ttc).' '.$langs->trans("TTC");
		else print price($objp->price).' '.$langs->trans("HT");
		print '</td>';

		print "\n</tr>\n";
		$i++;			
	}
	if($num == 0){
		//print empty row
		echo "<tr></tr>";
	}
	print '</table>';
}
echo "<input type='submit' style='padding:3px; margin-top:5px; width:200px;' value='Filtrer'>";
echo "</form>";
$db->close();

echo '<form name="form" id="form" >';
echo '<input type="hidden" id="prodid" name="productid" value="0">';
echo '<input type="hidden" id="prodref" name="prodref" value="0">';
echo '<input type="hidden" id="proddesc" name="proddesc" value="0">';
echo '</form>';

echo '</center>';
echo '<script language="javascript">TableKit.load();</script>';

//==================================================================================
//=============================TABLEKIT ISSUE=======================================
//==================================================================================
//reload to fix sort bug
//IMPORTANT: for some reason, 
//TableKit.reload() reloads the table as an Editable table
//For now the tablekit. source code has been modified by me in order to prevent this
//--Remy
//==================================================================================
echo '<script language="javascript">TableKit.reload();</script>';
echo '<script language="javascript">
	selectProduct = function(prodid, prodref, proddesc){
	alert("D");
		$("prodid").value = prodid;
		$("prodref").value = prodref;
		$("proddesc").value = proddesc;
		alert($("proddesc").value);
		Modalbox.hide();
	}
	
	filterSearch = function(){
		var filterList = new Array("ref","label","barcode","datemodif","sellingprice");
		var filterParams = "?o=o";
		
		filterList.each(function(fil){
			filterParams += addFilterConstraint(fil);
		});
		mhref="productSearch.php"+filterParams;
		mtitle="Selection Produit";
		Modalbox.show(mhref, 	{title: mtitle, width: 850, height:500, beforeHide: function(){  	window.serializedForm = Form.serialize(\'form\'); }, afterHide: function() { updateProductInfo(serializedForm) } });
	}
	
	addFilterConstraint = function(f){
		var filter = $("filter_"+f);
		if(filter != null && filter.value.length > 0){
			return "&"+f+"="+filter.value;
		}else{
			return "";
		}
	}
</script>';



echo "</body></html>";

?>