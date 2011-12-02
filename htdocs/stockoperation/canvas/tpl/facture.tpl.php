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
<?php if($action != 'create'){
?>
<tr>
    <td width="15%"><?php print $fields_data['fk_stockentry']['label'];?></td>
    <td width="85%" colspan="3"><?php print $fields_data['fk_stockentry']['data'];?></td>
</tr>
<?php 
}
?>
<tr>
    <td width="15%"><?php print $fields_data['fk_facture_fourn']['label'];?></td>
    <td width="35%"><?php print $fields_data['fk_facture_fourn']['data'];?></td>
	<td width="15%"><?php print $fields_data['numerofacture_externe']['label'];?></td>
    <td width="35%"><?php print $fields_data['numerofacture_externe']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['date_facture_fourn']['label'];?></td>
    <td><?php print $fields_data['date_facture_fourn']['data'];?></td>
	<td><?php print $fields_data['total_ht_facture']['label'];?></td>
    <td><?php print $fields_data['total_ht_facture']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['date_echeance_facture_fourn']['label'];?></td>
    <td><?php print $fields_data['date_echeance_facture_fourn']['data'];?></td>
	<td><?php print $fields_data['total_ttc_facture']['label'];?></td>
    <td><?php print $fields_data['total_ttc_facture']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['coeff_ht']['label'];?></td>
    <td><?php print $fields_data['coeff_ht']['data'];?></td>
	<td><?php print $fields_data['daom']['label'];?></td>
    <td><?php print $fields_data['daom']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['coeff_ttc']['label'];?></td>
    <td><?php print $fields_data['coeff_ttc']['data'];?></td>
	<td><?php print $fields_data['numeroconteneur']['label'];?></td>
    <td><?php print $fields_data['numeroconteneur']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['code_list_control']['label'];?></td>
    <td><?php print $fields_data['code_list_control']['data'];?></td>
	<td><?php print $fields_data['code_selection']['label'];?></td>
    <td><?php print $fields_data['code_selection']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['fk_entrepot']['label'];?></td>
    <td><?php print $fields_data['fk_entrepot']['data'];?></td>
	<td><?php print $fields_data['fk_societe']['label'];?></td>
    <td><?php print $fields_data['fk_societe']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['volume']['label'];?></td>
    <td><?php print $fields_data['volume']['data'].$fields_data['volume_unit']['data'];?></td>
	<td><?php print $fields_data['weight']['label'];?></td>
    <td><?php print $fields_data['weight']['data'].$fields_data['weight_unit'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['type_facture']['label'];?></td>
    <td><?php print $fields_data['type_facture']['data'];?></td>
	<td><?php print $fields_data['mode_calcul']['label'];?></td>
    <td><?php print $fields_data['mode_calcul']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['accounting']['label'];?></td>
    <td><?php print $fields_data['accounting']['data'];?></td>
	<td><?php print $fields_data['repartition']['label'];?></td>
    <td><?php print $fields_data['repartition']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['note']['label'];?></td>
    <td colspan="3"><?php print $fields_data['note']['data'];?></td>
</tr>

<!-- END PHP TEMPLATE -->