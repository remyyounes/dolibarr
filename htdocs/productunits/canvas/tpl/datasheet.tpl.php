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
    <td width="15%"><?php print $fields_data['libelle_unca']['label'];?></td>
    <td><? print $fields_data['libelle_unca']['data'];?></td>
    <td width="15%"><?php print $fields_data['libelle_uncv']['label'];?></td>
    <td><?php print $fields_data['libelle_uncv']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['weight']['label'];?></td>
    <td><?php print $fields_data['weight']['data'].$fields_data['weight_unit']['data'];?></td>
    <td><?php print $fields_data['publiable']['label'];?></td>
    <td><?php print $fields_data['publiable']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['volume']['label'];?></td>
    <td><?php print $fields_data['volume']['data'].$fields_data['volume_unit']['data'];?></td>
	<td><?php print $fields_data['reprise']['label'];?></td>
    <td><?php print $fields_data['reprise']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['cfa']['label'];?></td>
    <td><?php print $fields_data['cfa']['data'];?></td>
	<td><?php print $fields_data['cvs']['label'];?></td>
    <td><?php print $fields_data['cvs']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['libelle_fk_marque']['label'];?></td>
    <td><?php print $fields_data['libelle_fk_marque']['data'];?></td>
	<td><?php print $fields_data['delailivraison']['label'];?></td>
    <td><?php print $fields_data['delailivraison']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['libelle_fk_fabricant']['label'];?></td>
    <td><?php print $fields_data['libelle_fk_fabricant']['data'];?></td>
	<td><?php print $fields_data['delaifabrication']['label'];?></td>
    <td><?php print $fields_data['delaifabrication']['data'];?></td>
</tr>

<!-- END PHP TEMPLATE -->