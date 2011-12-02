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
    	<td class="liste_titre">id</td>
    	<td class="liste_titre">type_facture</td>
    	<td class="liste_titre">total_ttc_facture</td>
    	<td class="liste_titre">date_facture_fourn</td>
    	<td class="liste_titre">date_echeance_facture_fourn</td>
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
    	<td><a href="<?php print DOL_URL_ROOT.'/stockoperation/fiche.php?facid='.$item->rowid.'&actions=showfacture';?>"><?php print $this->printField($this->fields->rowid,$item->rowid);?></a></td>
        <td><?php print $this->printField($this->fields->type_facture, $item->type_facture);?></td>
        <td><?php print $this->printField($this->fields->total_ttc_facture, $item->total_ttc_facture);?></td>
        <td><?php print $this->printField($this->fields->date_facture_fourn, $item->date_facture_fourn);?></td>
        <td><?php print $this->printField($this->fields->date_echeance_facture_fourn, $item->date_echeance_facture_fourn);?></td>
        
    </tr>
    <?php
            $var = !$var; 
        }
    ?>
</table>

<!-- END PHP TEMPLATE -->