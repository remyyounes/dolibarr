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
    	<td class="liste_titre"><?php print $langs->trans('Ref');?></td>
    	<td class="liste_titre"><?php print $langs->trans('fk_stockentry');?></td>
    	<td class="liste_titre"><?php print $langs->trans('fk_societe');?></td>
    	<td class="liste_titre"><?php print $langs->trans('date_entree');?></td>
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
    	<td><a href="<?php print DOL_URL_ROOT.'/stockoperation/fiche.php?id='.$item->rowid;?>"><?php print $this->printField($this->fields->rowid,$item->rowid);?></a></td>
        <td><?php print $item->numerodossier?$item->numerodossier:"(PROV".$item->rowid.")";?></td>
        <td><?php print $item->fournisseur_name;?></td>
        <td><?php print $this->printField($this->fields->date_entree, $item->date_entree);?></td>
    </tr>
    <?php
            $var = !$var; 
        }
    ?>
</table>

<!-- END PHP TEMPLATE -->