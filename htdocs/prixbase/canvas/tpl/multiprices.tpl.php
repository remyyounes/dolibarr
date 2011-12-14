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
<table class="border" width="100%">
<tr>
<td width="15%">
	<?php print $langs->trans('VATRate');?>
</td>
<td>
	<?php print $VATRate;?>
</td>
</tr>
</table>
<table class="border">
<!-- BEGIN PHP TEMPLATE -->
<tr class="liste_titre">
    <td width="5%"><?php print $langs->trans('#');?></td>
    <td width="5%"><?php print $langs->trans('BaseDuPrix');?></td>
    <td width="10%"><?php print $langs->trans('Coeff');?></td>
    <td width="15%"><?php print $langs->trans('PxVenteHT');?></td>
    <td width="15%"><?php print $langs->trans('PxVenteTTC');?></td>
    <td width="10%"><?php print $langs->trans('Marge');?></td>
    <td width="20%"><?php print $langs->trans('MargePCT');?></td>
    <td width="20%"><?php print $langs->trans('PxVenteMin');?></td>
</tr>
<?php 
foreach($price_lines as $pLine){
    
?>
<tr>
	<td><?php print $pLine['num'];?></td>
	<td><?php print $pLine['base_price_type'];?></td>
	<td><?php print $pLine['coeff'];?></td>
	<td><?php print $pLine['pxVenteHT'];?></td>
	<td><?php print $pLine['pxVenteTTC'];?></td>
	<td><?php print $pLine['marge'];?></td>
	<td><?php print $pLine['margepct'];?></td>
	<td><?php print $pLine['pxMin'];?></td>
</tr>
<?php 
}
?>
</table>
<table>
<!-- END PHP TEMPLATE -->