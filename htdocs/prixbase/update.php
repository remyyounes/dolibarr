<?php

if ($_POST["action"] == 'update_baseprice' && ! $_POST["cancel"] && ($user->rights->produit->creer || $user->rights->service->creer))
{
	$product = new Product($db);
	$productPriceBase = new Product_pricebase($db);
	$result = $product->fetch($_GET["id"]);
	$res = $productPriceBase->fetch($_GET["id"],1);
	$productPriceBase->fk_product=$_GET['id'];
	
	//Fetch all post values and store them in out Product_base Object for update
	foreach($productPriceBase->fields as $field)
	{
		if(in_array($field, $productPriceBase->prices)){
			$productPriceBase->$field=price2num($_POST["pb_".$field], 'MU');
		}else{
			$productPriceBase->$field=$_POST["pb_".$field];
		}
	}
	if((int)($productPriceBase->peremption) < 0 || strlen($productPriceBase->peremption) <= 0 ){
		$productPriceBase->peremption = 0;
	}

	//create or update
	if($productPriceBase->id > 0){
		$upd = $productPriceBase->update($user);
	}else{
		$upd = $productPriceBase->create($user);
	}
	
	//display status message from update/create
	if ($upd > 0)
	{
		$mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
	}
	else
	{
		$mesg = '<div class="error">'.$productPriceBase->error.'</div>';
	}
}

?>