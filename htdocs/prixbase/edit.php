<?php

/*
 * Edition Prix Base
 */
if ($_GET["action"] == 'edit_baseprice' && ($user->rights->produit->creer || $user->rights->service->creer))
{

    print_fiche_titre($langs->trans("Edition du Prix Base"),'','');
	
	$hidden_fields = array( array('id' , $product->id));
	print $productPriceBase->printEditForm('update_baseprice',$hidden_fields);
	
	print '<script language="javascript">  
			productTVA = '.$product->tva_tx .';
			productNPR = '.$product->tva_npr.';
		  </script>';
	
	print '<script src="'.DOL_URL_ROOT.'/prixbase/js/prixbaseLib.js"></script>';
	print '<script src="'.DOL_URL_ROOT.'/makina/js/numbers.js"></script>';
}
?>