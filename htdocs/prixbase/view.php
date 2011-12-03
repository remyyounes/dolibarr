<?php
if($product->id > 0 ){
	$productPriceBase = new Product_pricebase($db);
	$productPriceBase->fetch($product->id,1);

	$coeff = 0;
	$coeff_mp = 0;
	if($productPriceBase->pa > 0 ){
		$coeff 		=  number_format($productPriceBase->prht 	 / $productPriceBase->pa, 3);
	}
	if($productPriceBase->pamp > 0 ){
		$coeff_mp 	=  number_format($productPriceBase->prmpht 	 / $productPriceBase->pamp, 3);
	}
	
	if( $user->rights->prixbase->lire ){
		
		print '<br><table class="border" width="100%">';
		echo '<tr>';
		echo '<td width="15%">'.$langs->trans("pb_pa").'</td><td width="35%">'.price(number_format($productPriceBase->pa,2)).'</td>';
		echo '<td width="15%">'.$langs->trans("pb_pamp").'</td><td width="35%">'.price(number_format($productPriceBase->pamp,2)).'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>'.$langs->trans("pb_coeff").'</td><td>'.$coeff.'</td>';
		echo '<td>'.$langs->trans("pb_coeff_mp").'</td><td>'.$coeff_mp.'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>'.$langs->trans("pb_prht").'</td><td>'.price(number_format($productPriceBase->prht,2)).'</td>';
		echo '<td>'.$langs->trans("pb_prmpht").'</td><td>'.price(number_format($productPriceBase->prmpht,2)).'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>'.$langs->trans("pb_prttc").'</td><td>'.price(number_format($productPriceBase->prttc,2)).'</td>';
		echo '<td>'.$langs->trans("pb_prmpttc").'</td><td>'.price(number_format($productPriceBase->prmpttc,2)).'</td>';
		echo '</tr>';
		
		echo '<tr>';
		echo '<td>'.$langs->trans("pb_valorisation").'</td><td>'.$productPriceBase->valorisationAsHumanReadable().'</td>';
		echo '<td>'.$langs->trans("pb_peremption").'</td><td>'.$productPriceBase->peremption.' '.$langs->trans('days').'</td>';
		echo '</tr>';
		
		//End View
		print '</table>';
	}

}

?>