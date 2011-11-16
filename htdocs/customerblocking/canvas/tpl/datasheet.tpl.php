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
    <td width="15%"><?php print $fields_data['libelle_blockingcode']['label'];?></td>
    <td><? print $fields_data['libelle_blockingcode']['data'];?></td>
</tr>
<tr>
    <td><?php print $fields_data['plafond']['label'];?></td>
    <td><?php print $fields_data['plafond']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['depassement']['label'];?></td>
    <td><?php print $fields_data['depassement']['data'];?></td>
</tr>

<!-- END PHP TEMPLATE -->