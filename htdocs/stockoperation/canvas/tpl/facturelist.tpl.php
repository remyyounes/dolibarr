<?php
/* Copyright (C) 2011 Remy Younes <ryounes@gmail.com>
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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
?>

<!-- BEGIN PHP TEMPLATE -->
<table class="liste" width="100%">
	
    <tr class="liste_titre">
    	<td class="liste_titre">ID</td>
    	<td class="liste_titre"><?php print $langs->trans('type_facture');?></td>
    	<td class="liste_titre"><?php print $langs->trans('referenced_facture');?></td>
    	<td class="liste_titre"><?php print $langs->trans('total_ttc_facture');?></td>
    	<td class="liste_titre"><?php print $langs->trans('date_facture_fourn');?></td>
    	<td class="liste_titre"><?php print $langs->trans('date_echeance_facture_fourn');?></td>
    	<td class="liste_titre"></td>
    	<td class="liste_titre"></td>
    </tr>
    <tr class="liste_titre">
    <td class="liste_titre" colspan="9">filter info</td>
    </tr>
    <?php 
    global $bc;
        $var=true;
        foreach($list as $item){
    ?>
    <tr <?php print $bc[$var];?>>
    	<td><a href="<?php print DOL_URL_ROOT.'/stockoperation/fiche.php?action=showfacture&id='.$this->fk_stockentry.'&facid='.$item->rowid;?>"><?php print $this->printField($this->fields->rowid,$item->rowid);?></a></td>
        <td><?php print $this->printField($this->fields->type_facture, $item->type_facture);?></td>
        <td><a href="<?php print DOL_URL_ROOT.'/fourn/facture/fiche.php?facid='.$item->facid;?>"><?php print $item->referenced_facture.'</a>';?></td>
        <td><?php print $this->printField($this->fields->total_ttc_facture, $item->total_ttc_facture);?></td>
        <td><?php print $this->printField($this->fields->date_facture_fourn, $item->date_facture_fourn);?></td>
        <td><?php print $this->printField($this->fields->date_echeance_facture_fourn, $item->date_echeance_facture_fourn);?></td>
        <td><a href="<?php print DOL_URL_ROOT.'/stockoperation/fiche.php?action=editfacture&id='.$this->fk_stockentry.'&facid='.$item->rowid;?>"><?php print img_edit();?></a></td>
        <td><a href="<?php print DOL_URL_ROOT.'/stockoperation/fiche.php?action=deletefacture&id='.$this->fk_stockentry.'&facid='.$item->rowid;?>"><?php print img_delete();?></a></td>
    </tr>
    <?php
            $var = !$var; 
        }
    ?>
</table>

<!-- END PHP TEMPLATE -->