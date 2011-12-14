<?php
if ( $id ){
    print '<br>';
    $productPriceBase = new Product_pricebase($db);
    $productPriceBase->fetch($id,1);
    $productPriceBase->printDataSheet();
}
?>