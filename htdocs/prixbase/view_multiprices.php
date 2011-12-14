<?php
if($id){

    if ($conf->global->PRODUIT_MULTIPRICES)
    {
        $price_lines = array();
        $pLine = array();

        $valorisation = $productPriceBase->valorisation;
        $pxRev = $valorisation == "pxmp"? $productPriceBase->prmpht : $productPriceBase->prht;
        $pxRev = !$pxRev? $productPriceBase->prht : $pxRev;
        //$langs->trans("VATRate")
        $VATRate = vatrate($product->multiprices_tva_tx[1] . ($product->tva_npr?'*':''),true);
        for ($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++){

            //#
            $pLine['num'] = $i;

            //base
            if ($product->multiprices_base_type["$i"]){
                $basePriceType =  ' '.$langs->trans($product->multiprices_base_type["$i"]);
            }else{
                $basePriceType = ' '.$langs->trans($product->price_base_type);
            }
            $pLine['base_price_type'] = $basePriceType;

            //coeff
            if ( $pxRev  > 0 ){
                $coeffVente = number_format($product->multiprices[$i] / $pxRev,3);
            }else{
                $coeffVente = 0;
            }
            $pLine['coeff'] = $coeffVente;

            //marge
            $marge = $product->multiprices[$i] - $pxRev;
            $marge = ($marge <= 0) ? 0 : $marge;
            $margepct = 0 ;
            if ( $product->multiprices[$i] > 0 ){
                $margepct = $marge / $product->multiprices[$i]*100;
            }
            $pLine['marge'] = price($marge);
            $pLine['margepct'] = number_format($margepct,3) . "%";

            $pLine['pxVenteHT'] = price($product->multiprices["$i"]);
            $pLine['pxVenteTTC'] = price($product->multiprices_ttc["$i"]);

            if ($product->multiprices_base_type["$i"] == 'TTC'){
                $pxMin = price($product->multiprices_min_ttc["$i"]).' '.$langs->trans($product->multiprices_base_type["$i"]);
            }else{
                $pxMin = price($product->multiprices_min["$i"]).' '.$langs->trans($product->multiprices_base_type["$i"]);
            }
            $pLine['pxMin'] = $pxMin;
            $price_lines[] = $pLine;
        }
    }
    $multiprices_template = DOL_DOCUMENT_ROOT."/prixbase/canvas/tpl/multiprices.tpl.php";
    include($multiprices_template);
}