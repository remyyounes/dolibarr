<?php

/*
 * Edition Prix Base
 */
if ($_GET["action"] == 'edit_baseprice' && ($user->rights->produit->creer || $user->rights->service->creer))
{
	
	print_fiche_titre($langs->trans("Edition du Prix Base"),'','');
	
	print '<form action="price.php?id='.$product->id.'" method="post">';
	print '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
	print '<input type="hidden" name="action" value="update_baseprice">';
	print '<input type="hidden" name="id" value="'.$product->id.'">';
	print '<table class="border" width="100%">';
	if($productPriceBase->pa > 0){
		$coeff 		=  number_format($productPriceBase->prht 	 / $productPriceBase->pa, 3);
	}else{
		$coeff = 0;
	}
	if($productPriceBase->pamp > 0){
		$coeff_mp 	=  number_format($productPriceBase->prmpht 	 / $productPriceBase->pamp, 3);
	}else{
		$coeff_mp = 0;
	}
	$dec = 2;
	echo '<tr>';
	echo $productPriceBase->form_inputField("pa","pb_pa");
	echo $productPriceBase->form_inputField("pamp","pb_pamp");
	echo '</tr>';

	echo '<tr>';
	echo '<td>'.$langs->trans("pb_coeff").'</td><td><input id="pb_coeff" onBlur="calculatePR(this)" size="10" value="'.$coeff.'"></td>';
	echo '<td>'.$langs->trans("pb_coeff_mp").'</td><td><input id="pb_coeff_mp" onBlur="calculatePR(this)" size="10" value="'.$coeff_mp.'"></td>';
	echo '</tr>';
	
	echo '<tr>';
	echo $productPriceBase->form_inputField("prht","pb_prht");
	echo $productPriceBase->form_inputField("prmpht","pb_prmpht");
	echo '</tr>';
	
	echo '<tr>';
	echo $productPriceBase->form_inputField("prttc","pb_prttc");
	echo $productPriceBase->form_inputField("prmpttc","pb_prmpttc");
	echo '</tr>';
	
	echo '<tr>';
	echo $productPriceBase->form_inputField("valorisation","pb_valorisation");
	echo $productPriceBase->form_inputField("peremption","pb_peremption");
	echo '</tr>';
		
	
	
	//buttons
	print '<tr><td colspan="4" align="center"><input type="submit" class="button" value="'.$langs->trans("Save").'">&nbsp;';
	print '<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'"></td></tr>';
	
	//end of edition
	print '</form>';
	print '</table>';
	
}
?>