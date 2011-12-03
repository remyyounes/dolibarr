<?php
if($conf->stockoperation->enabled){
    // stockoperation
    $langs->load("stockoperation@stockoperation");
    $newmenu->add("/stockoperation/fiche.php?leftmenu=stockoperation", $langs->trans("StockOperation"), 0, $user->rights->stockoperation->lire);
    if ($leftmenu=="stockoperation") $newmenu->add("/stockoperation/fiche.php?action=create&leftmenu=stockoperation&mainmenu=products", $langs->trans("NewStockEntryRecord"), 1, $user->rights->stockoperation->creer);
    if ($leftmenu=="stockoperation") $newmenu->add("/stockoperation/fiche.php?action=liste&leftmenu=stockoperation&mainmenu=products", $langs->trans("List"), 1, $user->rights->stockoperation->lire);
    if ($leftmenu=="stockoperation") $newmenu->add("/stockoperation/stats/index.php?leftmenu=stockoperation&mainmenu=products", $langs->trans("MiscOperations"), 1, $user->rights->stockoperation->lire);
    if ($leftmenu=="stockoperation") $newmenu->add("/stockoperation/stats/index.php?leftmenu=stockoperation&mainmenu=products", $langs->trans("Inventory"), 1, $user->rights->stockoperation->lire);
}
?>