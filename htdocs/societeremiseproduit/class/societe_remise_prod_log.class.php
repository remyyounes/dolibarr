<?php

class Societe_remise_prod_log 
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $soc_remise_prod_entries = array();

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Societe_remise_prod_log($DB) 
    {
        $this->db = $DB;
        return 1;
    }


	function fetch($id,$fk_prod=0)
    {
    	global $langs;
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_soc,";
		$sql.= " t.fk_categorie_soc,";
		$sql.= " t.fk_categorie,";
		$sql.= " t.fk_product,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.productlevel,";
		$sql.= " t.txrem,";
		$sql.= " t.cfrem,";
		$sql.= " t.prem,";
		$sql.= " t.prem2,";
		$sql.= " t.qte,";
		$sql.= " t.active,";
		$sql.= " t.tms,";
		$sql.= " t.datec,";
		$sql.= " date_format(`dated`,'%d/%m/%Y')  as fdated,";
		$sql.= " date_format(`datef`,'%d/%m/%Y')  as fdatef";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."societe_remise_prod as t";
        if($fk_prod){
        	$sql.= " WHERE fk_product = ".$id." AND fk_soc is NULL  ";
        }else{
        	$sql.= " WHERE fk_soc = ".$id;
        }
        $sql.= " ORDER BY tms DESC";
        
        $this->soc_remise_prod_entries = array();
    
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
        $resql=$this->db->query($sql);
        if ($resql)
        {
        	$num = $this->db->num_rows($resql);
        	$i = 0;
	        while ( $i < $num){
                $obj = $this->db->fetch_object($resql);
                $soc_remise_prod_e = new Societe_remise_prod($this->db);
	            $soc_remise_prod_e->id    = $obj->rowid;
                
				$soc_remise_prod_e->fk_soc = $obj->fk_soc;
				$soc_remise_prod_e->fk_categorie_soc = $obj->fk_categorie_soc;
				$soc_remise_prod_e->fk_categorie = $obj->fk_categorie;
				$soc_remise_prod_e->fk_product = $obj->fk_product;
				$soc_remise_prod_e->fk_user_author = $obj->fk_user_author;
				$soc_remise_prod_e->productlevel = $obj->productlevel;
				$soc_remise_prod_e->txrem = $obj->txrem;
				$soc_remise_prod_e->cfrem = $obj->cfrem;
				$soc_remise_prod_e->prem = $obj->prem;
				$soc_remise_prod_e->prem2 = $obj->prem2;
				$soc_remise_prod_e->qte = $obj->qte;
				$soc_remise_prod_e->active = $obj->active;
				$soc_remise_prod_e->tms = $this->db->jdate($obj->tms);
				$soc_remise_prod_e->datec = $this->db->jdate($obj->datec);
				$soc_remise_prod_e->dated = $obj->fdated;
				$soc_remise_prod_e->datef = $obj->fdatef;
				$this->soc_remise_prod_entries[]=$soc_remise_prod_e;
				$i++;
            }
            $this->db->free($resql);
            
            return 1;
        }
        else
        {
      	    $this->error="Error ".$this->db->lasterror();
            dol_syslog(get_class($this)."::fetch ".$this->error, LOG_ERR);
            return -1;
        }
    }
    
    
    
    function printLogTable($soc=0){
    	global $langs, $bc;
	    $entries = $this->soc_remise_prod_entries;
	    if(count($entries) < 1){
	 	   return;
	    }
	    
	    //choices
	    //remise: product / categorie
	    //typeRemise
	    //soc
	    $typeRemises['taux'] = 'txrem';
		$typeRemises['coeff'] = 'cfrem';
		$typeRemises['level'] = 'productlevel';
		$typeRemises['prix'] = 'prem';
	    
	    
	    print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre">';
		print '<td align="left">'.$langs->trans('AppliedRemiseFrom').'</td>';
		print '<td align="right">'.$langs->trans('cr_dated').'</td>';
		print '<td align="right">'.$langs->trans('cr_datef').'</td>';
		
		if($soc){
			//product // categorie
			print '<td align="right">'.$langs->trans('categorieOrProduct').'</td>';
		}
		//type remise
		print '<td align="right">'.$langs->trans('type_remise').'</td>';
		//qte min
		print '<td align="right">'.$langs->trans('qteMinRemise').'</td>';
		//remise
		print '<td align="right">'.$langs->trans('remise').'</td>';
		//finalprice (if  product ) (empty for categorie product)
		print '<td align="right">'.$langs->trans('final_price').'</td>';
		//author
		print '<td align="right">'.$langs->trans('ChangedBy').'</td>';
		//status 
		print '<td align="right">'.$langs->trans('Status').'</td>';
		//delete
		print '<td align="right">'."X".'</td>';
		print '</tr>';
		
		$var = True;
		$actl[0] = img_picto($langs->trans("Disabled"),'off');
		$actl[1] = img_picto($langs->trans("Activated"),'on');
		$acts[0] = "activate";
		$acts[1] = "disable";

		foreach($entries as $e){

			$var=!$var;
			print "<tr $bc[$var]>";
			
			print '<td align="left">'.date("d/m/Y H:i" ,$e->tms)."</td>";
			print '<td align="right">'.$e->dated.'</td>';
			print '<td align="right">'.$e->datef.'</td>';
			
			if($soc){
				if($e->fk_product == NULL){
					$cat = new Categorie($this->db);
					$cat->fetch($e->fk_categorie);
					print '<td align="right"><a href="'.DOL_URL_ROOT.'/categories/viewcat.php?id='.$cat->id.'&type=0">'.$cat->label.'</a></td>';
				}else{
					$prod = new Product($this->db);
					$prod->fetch($e->fk_product);
					print '<td align="right"><a href="'.DOL_URL_ROOT.'/product/fiche.php?id='.$prod->id.'">'.$prod->ref.'</a></td>';
				}
			}
			
			foreach($typeRemises as $key=>$val){
				if($e->$val != NULL){
					print '<td align="right">'.$langs->trans($key).'</td>';
					$remisefield = $val;
					if($key == "coeff"){
						$remise = $e->$remisefield . "%";
					}elseif($key == "level"){
						$remise = $e->$remisefield;
					}else{
						$remise = price($e->$remisefield);
					}
					break;
				}
			}
			
			print '<td align="right">'.$e->qte.'</td>';
			
			print '<td align="right">'.$remise.'</td>';
			
			print '<td align="right">'.price($e->prem2).'</td>';
			
			//author
			$user_author = new User($this->db);
			$user_author->fetch($e->fk_user_author);
			print '<td align="right"><a href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$user_author->id.'">'.img_object($langs->trans("ShowUser"),'user').' '.$user_author->login.'</a></td>';
			
			$hrefId = "";
			if($_GET['socid']){
				$hrefId = "&socid=".$_GET['socid'];
			}elseif($_GET['id']){
				$hrefId = "&id=".$_GET['id'];
			}elseif($_GET['ref']){
				$hrefId = "&ref=".$_GET['ref'];
			}
			//active
			print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action='.$acts[$e->active].'&remiseId='.$e->id.$hrefId.'">'.$actl[$e->active].'</a></td>';
			
			//del
			print '<td align="right"><a href="'.$_SERVER['PHP_SELF'].'?action=delete&remiseId='.$e->id.$hrefId.'">'.img_delete().'</a></td>';
			
			
			print '</tr>';
		}

		print "</table>";
    }	
	

}
?>