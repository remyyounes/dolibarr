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

//=================
//===== COEFF =====
//=================

$coeff = 0;
$coeff_mp = 0;
if($this->pa > 0 ){
    $coeff = $this->prht / $this->pa;
}
if($this->pamp > 0 ){
    $coeff = $this->prmpht / $this->pamp;
}  

$coeff    = number_format($coeff,3);
$coeff_mp = number_format($coeff_mp,3);

$fields_data['coeff']['label'] = $langs->trans('coeff');
$fields_data['coeff_mp']['label'] = $langs->trans('coeff_mp');

if($action == 'edit'){
    $fields_data['coeff']['data'] 		=  "<input type='text' name='coeff'    id='cf_coeff'    value='".$coeff."'>";
    $fields_data['coeff_mp']['data'] 	=  "<input type='text' name='coeff_mp' id='cf_coeff_mp' value='".$coeff_mp."'>";
}else{
    $fields_data['coeff']['data'] 		=  $coeff;
    $fields_data['coeff_mp']['data'] 	=  $coeff_mp;
}
//=================
//===== COEFF =====
//=================

// OLD



// OLD
?>

<!-- BEGIN PHP TEMPLATE -->
<tr>
    <td width="15%"><?php print $fields_data['pa']['label'];?></td>
    <td><? print $fields_data['pa']['data'];?></td>
    <td width="15%"><?php print $fields_data['pamp']['label'];?></td>
    <td><?php print $fields_data['pamp']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['coeff']['label']?></td>
    <td><?php print $fields_data['coeff']['data']?></td>
	<td><?php print $fields_data['coeff_mp']['label']?></td>
    <td><?php print $fields_data['coeff_mp']['data']?></td>
</tr>
<tr>
	<td><?php print $fields_data['prht']['label'];?></td>
    <td><?php print $fields_data['prht']['data'];?></td>
	<td><?php print $fields_data['prmpht']['label'];?></td>
    <td><?php print $fields_data['prmpht']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['prttc']['label'];?></td>
    <td><?php print $fields_data['prttc']['data'];?></td>
	<td><?php print $fields_data['prmpttc']['label'];?></td>
    <td><?php print $fields_data['prmpttc']['data'];?></td>
</tr>
<tr>
	<td><?php print $fields_data['valorisation']['label'];?></td>
    <td><?php print $fields_data['valorisation']['data'];?></td>
	<td><?php print $fields_data['peremption']['label'];?></td>
    <td><?php print $fields_data['peremption']['data'];?></td>
</tr>

<!-- END PHP TEMPLATE -->