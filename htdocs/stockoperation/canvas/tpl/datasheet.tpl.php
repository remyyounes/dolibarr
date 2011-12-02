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
<tr>
    <td width="15%"><?php print $fields_data['fk_societe']['label'];?></td>
    <td width="35%"><?php print $fields_data['fk_societe']['data'];?></td>
	<td width="15%"><?php print $fields_data['transport']['label'];?></td>
    <td width="35%"><?php print $fields_data['transport']['data'];?></td>
</tr>
<tr>
    <td width="15%"><?php print $fields_data['mode_calcul']['label'];?></td>
    <td width="35%"><?php print $fields_data['mode_calcul']['data'];?></td>
	<td width="15%"><?php print $fields_data['numeroplomb']['label'];?></td>
    <td width="35%"><?php print $fields_data['numeroplomb']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['coeff_rev_global']['label'];?></td>
    <td><?php print $fields_data['coeff_rev_global']['data'];?></td>
	<td><?php print $fields_data['numeroconteneur']['label'];?></td>
    <td><?php print $fields_data['numeroconteneur']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['date_entree']['label'];?></td>
    <td><?php print $fields_data['date_entree']['data'];?></td>
	<td><?php print $fields_data['ref_ext1']['label'];?></td>
    <td><?php print $fields_data['ref_ext1']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['date_validation']['label'];?></td>
    <td><?php print $fields_data['date_validation']['data'];?></td>
	<td><?php print $fields_data['ref_ext2']['label'];?></td>
    <td><?php print $fields_data['ref_ext2']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['marchandise_description']['label'];?></td>
    <td colspan="3"><?php print $fields_data['marchandise_description']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['note']['label'];?></td>
    <td colspan="3"><?php print $fields_data['note']['data'];?></td>
</tr>

<!-- END PHP TEMPLATE -->