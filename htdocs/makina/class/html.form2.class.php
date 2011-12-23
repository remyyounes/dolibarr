<?php
/* Copyright (c) 2002-2007 Rodolphe Quiedeville  <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur   <eldy@users.sourceforge.net>
 * Copyright (C) 2004      Benoit Mortier        <benoit.mortier@opensides.be>
 * Copyright (C) 2004      Sebastien Di Cintio   <sdicintio@ressource-toi.org>
 * Copyright (C) 2004      Eric Seigne           <eric.seigne@ryxeo.com>
 * Copyright (C) 2005-2011 Regis Houssin         <regis@dolibarr.fr>
 * Copyright (C) 2006      Andre Cianfarani      <acianfa@free.fr>
 * Copyright (C) 2006      Marc Barilley/Ocebo   <marc@ocebo.com>
 * Copyright (C) 2007      Franky Van Liedekerke <franky.van.liedekerker@telenet.be>
 * Copyright (C) 2007      Patrick Raguin        <patrick.raguin@gmail.com>
 * Copyright (C) 2010      Juanjo Menent         <jmenent@2byte.es>
 * Copyright (C) 2010      Philippe Grand        <philippe.grand@atoo-net.com>
 * Copyright (C) 2011      Herve Prot            <herve.prot@symeos.com>
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
 */

/**
 *	\file       htdocs/makina/class/html.form2.class.php
 *  \ingroup    core
 *	\brief      File of class with all *missing* html predefined components
 */


/**
 *	\class      Form
 *	\brief      Class to manage generation of HTML components
 *	\remarks	Only common components must be here.
 */
class Form2
{
	var $db;
	var $error;

	// Cache arrays
	var $cache_types_paiements=array();
	var $cache_conditions_paiements=array();
	var $cache_availability=array();
	var $cache_demand_reason=array();
	var $cache_type_fees=array();

	var $tva_taux_value;
	var $tva_taux_libelle;


	/**
	 * Constructor
	 *
	 * @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}

	
    
function select_factures_fournisseurs($socid, $selected='', $htmlname='factureid')
    {
        global $langs,$conf;
        //TODO:create global for factures_fournisseurs
        if ($conf->global->PRODUIT_USE_SEARCH_TO_SELECT)
        {
            $out = '';
            $out .= ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/makina/includes/ajax_facture_fourn.php', ($socid > 0?'socid='.$socid.'&':'').'htmlname='.$htmlname.'&outjson=1', $conf->global->PRODUIT_USE_SEARCH_TO_SELECT);
            $out .= '<input type="text" size="32" name="search_'.$htmlname.'" id="search_'.$htmlname.'">' .'<span class="autocomplete_searchbtn">'. img_picto("Search", "search")."</span>";
            $out .= '<br>';
        }
        else
        {
            //$this->select_produits_fournisseurs_do($socid,$selected,$htmlname,$filtertype,$filtre,'',-1,0);
            $out .= $this->select_factures_fournisseurs_do($socid,$selected,$htmlname,"",$searchkey,$status,$outjson);
        }
        return $out;
    }

    /**
     *	Retourne la liste des produits de fournisseurs
     *
     *	@param		socid   		Id societe fournisseur (0 pour aucun filtre)
     *	@param      selected        Produit pre-selectionne
     *	@param      htmlname        Nom de la zone select
     *	@param      filtre          Pour filtre sql
     *	@param      filterkey       Filtre des produits
     *  @param      status          -1=Return all products, 0=Products not on sell, 1=Products on sell
     *  @param      disableout      Disable print output
     *  @return     array           Array of keys for json
     */
    function select_factures_fournisseurs_do($socid,$selected='',$htmlname='factureid',$filtre='',$filterkey='',$statut=-1,$disableout=0)
    {
        global $langs,$conf;

        //$langs->load('stocks');

        $sql = "SELECT f.rowid, f.ref_ext";
        $sql.= " FROM ".MAIN_DB_PREFIX."facture_fourn as f";
        $sql.= " WHERE  f.fk_soc= ".$socid;
        
        // Add criteria on ref/label
        if ($filterkey && $filterkey != '' && $filterkey != ' ')
        {
            $sql.=" AND (f.ref_ext LIKE '%".$filterkey."%' OR f.rowid LIKE '%".$filterkey."%' )";
        }
        $sql.= " ORDER BY f.rowid DESC";

        // Build output string
        $outselect='';
        $outjson=array();

        dol_syslog(get_class($this)."::select_factures_fournisseurs_do sql=".$sql,LOG_DEBUG);
        $result=$this->db->query($sql);
        if ($result)
        {

            $num = $this->db->num_rows($result);

            $outselect.='<select class="flat" id="select'.$htmlname.'" name="'.$htmlname.'">';
            if (! $selected) $outselect.='<option value="0" selected="selected">&nbsp;</option>';
            else $outselect.='<option value="0">&nbsp;</option>';
			
            $i = 0;
            while ($i < $num)
            {
                $objp = $this->db->fetch_object($result);

                $outkey=$objp->rowid;
                $outref = $objp->ref_ext?$objp->ref_ext:"(PROV".$objp->rowid.")";
                $outval= $outref;

                $opt = '<option value="'.$objp->rowid.'"';
                if ($selected == $objp->rowid) $opt.= ' selected="selected"';
                $opt.= '>';
                $opt .= $outref;
                $opt .= "</option>\n";
                
                // Add new entry
                // "key" value of json key array is used by jQuery automatically as selected value
                // "label" value of json key array is used by jQuery automatically as text for combo box
                $outselect.=$opt;
                array_push($outjson,array('key'=>$outkey,'value'=>$outref,'label'=>$outval));

                $i++;
            }
            $outselect.='</select>';

            $this->db->free($result);

            if (empty($disableout)) return $outselect;
            return $outjson;
        }
        else
        {
            dol_print_error($db);
        }
    }
    
    
    //==================
    
    function select_entrepots($socid, $selected='', $htmlname='stockid')
    {
        global $langs,$conf;
        //TODO:create global for entrepots
        if ($conf->global->PRODUIT_USE_SEARCH_TO_SELECT)
        {
            $out = '';
            $out .= ajax_autocompleter($selected, $htmlname, DOL_URL_ROOT.'/makina/includes/ajax_entrepot.php', 'htmlname='.$htmlname.'&outjson=1', $conf->global->PRODUIT_USE_SEARCH_TO_SELECT);
            $out .= '<input type="text" size="32" name="search_'.$htmlname.'" id="search_'.$htmlname.'">' .'<span class="autocomplete_searchbtn">'. img_picto("Search", "search")."</span>";
            $out .= '<br>';
        }
        else
        {
            //$this->select_produits_fournisseurs_do($socid,$selected,$htmlname,$filtertype,$filtre,'',-1,0);
            $out .= $this->select_factures_fournisseurs_do($selected,$htmlname,"",$searchkey,$status,$outjson);
        }
        return $out;
    }
    
    /**
     *	Retourne la liste des produits de fournisseurs
     *
     *	@param		socid   		Id societe fournisseur (0 pour aucun filtre)
     *	@param      selected        Produit pre-selectionne
     *	@param      htmlname        Nom de la zone select
     *	@param      filtre          Pour filtre sql
     *	@param      filterkey       Filtre des produits
     *  @param      status          -1=Return all products, 0=Products not on sell, 1=Products on sell
     *  @param      disableout      Disable print output
     *  @return     array           Array of keys for json
     */
    function select_entrepots_do($selected='',$htmlname='stockid',$filtre='',$filterkey='',$statut=-1,$disableout=0)
    {
        global $langs,$conf;
    
        //$langs->load('stocks');
    
        $sql = "SELECT s.rowid, s.lieu";
        $sql.= " FROM ".MAIN_DB_PREFIX."entrepot as s";
        //$sql.= " WHERE  s.fk_soc= ".$socid;
    
        // Add criteria on ref/label
        if ($filterkey && $filterkey != '' && $filterkey != ' ')
        {
            $sql.=" WHERE (s.lieu LIKE '%".$filterkey."%' OR s.rowid LIKE '%".$filterkey."%' )";
        }
        $sql.= " ORDER BY s.rowid DESC";
    
        // Build output string
        $outselect='';
        $outjson=array();
    
        dol_syslog(get_class($this)."::select_factures_fournisseurs_do sql=".$sql,LOG_DEBUG);
        $result=$this->db->query($sql);
        if ($result)
        {
    
            $num = $this->db->num_rows($result);
    
            $outselect.='<select class="flat" id="select'.$htmlname.'" name="'.$htmlname.'">';
            if (! $selected) $outselect.='<option value="0" selected="selected">&nbsp;</option>';
            else $outselect.='<option value="0">&nbsp;</option>';
            	
            $i = 0;
            while ($i < $num)
            {
                $objp = $this->db->fetch_object($result);
    
                $outkey = $objp->rowid;
                $outref = $objp->lieu;
                $outval = $outref;
    
                $opt = '<option value="'.$objp->rowid.'"';
                if ($selected == $objp->rowid) $opt.= ' selected="selected"';
                $opt.= '>';
                $opt .= $outref;
                $opt .= "</option>\n";
    
                // Add new entry
                // "key" value of json key array is used by jQuery automatically as selected value
                // "label" value of json key array is used by jQuery automatically as text for combo box
                $outselect.=$opt;
                array_push($outjson,array('key'=>$outkey,'value'=>$outref,'label'=>$outval));
    
                $i++;
            }
            $outselect.='</select>';
    
            $this->db->free($result);
    
            if (empty($disableout)) return $outselect;
            return $outjson;
        }
        else
        {
            dol_print_error($this->db);
        }
    }

}

?>
