<?php

if ($_POST["action"] == 'update_baseprice' && ! $_POST["cancel"] && ($user->rights->produit->creer || $user->rights->service->creer))
{
    $product = new Product($db);
    $productPriceBase = new Product_pricebase($db);
    $result = $product->fetch($id);
    $res = $productPriceBase->fetch($id,1);
    $productPriceBase->fk_product=$id;

    //Fetch all post values and store them in out Product_base Object for update
    $productPriceBase->getPostValues();
    $productPriceBase->validateFields();

    //create or update
    $upd = $productPriceBase->updateCreate($user);
    /*
    if($productPriceBase->id > 0){
        $upd = $productPriceBase->update($user);
    }else{
        $upd = $productPriceBase->create($user);
    }
	*/
    
    //display status message from update/create
    if ($upd > 0){
        $mesg = '<div class="ok">'.$langs->trans("RecordSaved").'</div>';
    }else{
        $mesg = '<div class="error">'.$productPriceBase->error.'</div>';
    }
}

?>