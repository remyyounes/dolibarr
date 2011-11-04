<?php

class Societe_blocage_log 
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $soc_bloc_entries = array();

	
    /**
     *      \brief      Constructor
     *      \param      DB      Database handler
     */
    function Societe_blocage_log($DB) 
    {
        $this->db = $DB;
        return 1;
    }


    function fetch($soc_id)
    {
    	global $langs;
    	
        $sql = "SELECT";
		$sql.= " t.rowid,";
		
		$sql.= " t.fk_code,";
		$sql.= " t.fk_user_author,";
		$sql.= " t.fk_soc,";
		$sql.= " t.plafond,";
		$sql.= " t.tms";

		
        $sql.= " FROM ".MAIN_DB_PREFIX."societe_blocage as t";
        $sql.= " WHERE t.fk_soc = ".$soc_id;
        
        
        $sql.= " ORDER BY tms DESC";
    
    	dol_syslog(get_class($this)."::fetch sql=".$sql, LOG_DEBUG);
    	
    	$this->soc_bloc_entries = array();
    	
        $resql=$this->db->query($sql);
        if ($resql)
        {
        	$num = $this->db->num_rows($resql);
        	$i = 0;
            while ( $i < $num){
            	$obj = $this->db->fetch_object($resql);
    			$soc_bloc_e = new Societe_blocage($this->db);
                $soc_bloc_e->id    = $obj->rowid;
                
				$soc_bloc_e->fk_code = $obj->fk_code;
				$soc_bloc_e->fk_user_author = $obj->fk_user_author;
				$soc_bloc_e->fk_soc = $obj->fk_soc;
				$soc_bloc_e->plafond = $obj->plafond;
				$soc_bloc_e->tms = $this->db->jdate($obj->tms);
				
				$this->soc_bloc_entries[]=$soc_bloc_e;

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
    
//    	
//	function form_inputField($field,$html_name){
//		global $langs;
//		
//		$input = "";
//		$input .= '<tr><td width="15%">';
//		$input .= $langs->trans($html_name);
//		$input .= '</td>';
//		$input .= '<td>';
//		if($field == "fk_code"){
//			$c_block = new C_blocage($this->db);
//			$input .= $c_block->select_blockcode($this->$field, $html_name, 1);
//		}elseif( in_array($field, $this->editable_fields) ){
//			$input .= '<input type="text" name="'.$html_name.'" size="25" value="'.$this->$field.'"> ';
//		}else{
//			return "";
//		}
//		$input .= '</td>';
//		$input .= '</tr>';
//		
//		return $input;
//	}

    
    function printLogTable(){
    	global $langs, $bc;
	    $entries = $this->soc_bloc_entries;
	    if(count($entries) < 1){
	 	   return;
	    }
	    
	    $fields = $entries[0]->viewable_fields;
	    
	    print '<table class="noborder" width="100%">';
		print '<tr class="liste_titre">';
		print '<td align="left">'.$langs->trans('AppliedCeilingFrom').'</td>';
		print '<td align="right">'.$langs->trans('cb_plafond').'</td>';
		print '<td align="right">'.$langs->trans('cb_fk_code').'</td>';
		print '<td align="right">'.$langs->trans('ChangedBy').'</td>';
		
		print '</tr>';
		
		$var = True;
		$c_bloc = new C_blocage($this->db);	

		foreach($entries as $e){

			$var=!$var;
			print "<tr $bc[$var]>";
			
			//tms
			print '<td align="left">'.date("d/m/Y H:i" ,$e->tms)."</td>";
			//print '<td align="left">'.price($e->tms).'</td>';

			//plafond
			print '<td align="right">'.price($e->plafond).'</td>';
	
			//code
			$c_bloc->fetch($e->fk_code);
			print '<td align="right">'.$c_bloc->printBlockCode().'</td>';
			
			//author
			$user_author = new User($this->db);
			$user_author->fetch($e->fk_user_author);
			print '<td align="right"><a href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$user_author->id.'">'.img_object($langs->trans("ShowUser"),'user').' '.$user_author->login.'</a></td>';
			
			
			
			print '</tr>';
		}
		//print "<td>".dol_print_date($db->jdate($objp->dp),"dayhour")."</td>";
		// Action
//		if ($user->rights->produit->supprimer)
//		{
//			print '<td align="right">';
//			if ($i > 0)
//			{
//				print '<a href="'.$_SERVER["PHP_SELF"].'?action=delete&amp;id='.$product->id.'&amp;lineid='.$objp->rowid.'">';
//				print img_delete();
//				print '</a>';
//			}
//			else print '&nbsp;';	// Can not delete last price (it's current price)
//			print '</td>';
//		}
		
		print "</table>";
    }	
	

}
?>