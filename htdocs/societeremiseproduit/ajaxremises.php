<?php
/* Copyright (C) 2006      Andre Cianfarani     <acianfa@free.fr>
 * Copyright (C) 2005-2009 Regis Houssin        <regis@dolibarr.fr>
 * Copyright (C) 2007-2009 Laurent Destailleur  <eldy@users.sourceforge.net>
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
 *       \file       htdocs/product/ajaxproducts.php
 *       \brief      File to return Ajax response on product list request
 *       \version    $Id: ajaxproducts.php,v 1.23 2010/07/09 23:49:42 eldy Exp $
 */

if (! defined('NOREQUIREUSER'))  define('NOREQUIREUSER','1');
//if (! defined('NOREQUIREDB'))    define('NOREQUIREDB','1');
if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');
if (! defined('NOCSRFCHECK'))    define('NOCSRFCHECK','1');
if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
if (empty($_GET['keysearch']) && ! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
if (! defined("NOLOGIN"))        define("NOLOGIN",'1');


require('../main.inc.php');
require_once(DOL_DOCUMENT_ROOT ."/core/class/html.form.class.php");
require_once(DOL_DOCUMENT_ROOT ."/lib/ajax.lib.php");
require_once("./class/societe_remise_prod.class.php");
$langs->load("products");
$langs->load("main");
$langs->load("societeremiseproduit@societeremiseproduit");

top_htmlhead("", "", 1);

print '<body class="nocellnopadd">'."\n";

//=========================================
//========== NEEDED VARIABLES  ===========
//=========================================
$typeRemises = array();
$typeRemises['taux'] ='cr_txrem';
$typeRemises['coeff'] = 'cr_cfrem';
$typeRemises['level'] = 'cr_productlevel';
$typeRemises['prix'] = 'cr_prem';
//===========================================
//=========== GET DISCOUNT PRICE ============
//===========================================
if (! empty($_GET['price_adjust'])){
	$prodId = $_GET['prodId'];
	if($prodId <= 0){
		echo '<div class="error" style="border:0px;">'.$langs->trans("ProductNotSelected").'</div>';
	}else{
		$typeRemise = $_GET['typeRemise'];
		$remiseValue = $_GET['price_adjust'];

		$soc_rem = new Societe_remise_prod($db);
		$soc_rem->setDiscountType($typeRemise);
		$soc_rem->setDiscountValue($remiseValue);

		$finalPrice = $soc_rem->getDiscountPrice($prodId);//,$typeRemise,$remiseValue,$socPriceLevel);
		if($finalPrice > 0){
			echo '<input disabled="true" type="text" name="cr_prem2" value="'.price($finalPrice).'">';
		}else{
			echo '<div class="error" style="border:0px;">'.$soc_rem->error.'</div>';
		}
	}
}
//===========================================
//=========== SELECT REMISE TYPE ============
//===========================================
else if (! empty($_GET['type']))
{
	$type = $_GET['type'];
	if($type == ""){	return; }

	$changeing = $_GET['changeing'];
	$catremise = $_GET['catremise'];
	$html_name = $langs->trans($typeRemises[$type]);
	$current_field_value = "0.00";

	$onChangeJS = "";
	if($catremise == "product"){
		$onChangeJS = 'onChange="updateFinalPrice(this.value)"';
	}

	if($type == "coeff"){
		$pctNotice = " (coeff.: 0-100)";
	}

	// if pricelevel
	if($type == 'level'){
		echo '<select class="flat" id="'.$typeRemises[$type].'"  name="'.$typeRemises[$type].'" '.$onChangeJS.'>';
		for ($i=1; $i <= $conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++){
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
		echo '</select> ';
		echo '<span style="color:grey">- Tarif</span>';
	}else{
		echo '<input value="" id="'.$typeRemises[$type].'" name="'.$typeRemises[$type].'" '.$onChangeJS.'> ';
		echo '<span style="color:grey">- Remise '.$pctNotice.'</span>';
	}
	if($catremise == "product"){
		echo '<div class="nocellnopadd" id="ajdynfield'.$typeRemises[$type].'"></div>';
	}
}

$db->close();

print "</body>";
print "</html>";
?>
