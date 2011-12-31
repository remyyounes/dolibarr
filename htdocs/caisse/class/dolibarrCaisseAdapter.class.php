<?php

class DolibarrCaisseAdapter
{
	private $caisse;
	private $db;

	function getTicket($ticketId){
		$ticket = null;
		return $ticket;
	}

	public function DolibarrCaisseAdapter($db){
		$this->db = $db;
	}

	public function getClient($clientId){

	}
	
	public function getDefaultClientData(){
		global $conf;
		$defaultClient = new Societe($this->db);
		$defaultClient->fetch($conf->global->CASHDESK_ID_THIRDPARTY);
		$c = array();
		$c['id'] = $defaultClient->id;
		$c['name'] = $defaultClient->name;
		return $c;
	}

	public function getClients(){
		global $conf;

		$sql = "SELECT s.rowid as id, s.nom as name";
		$sql.= " FROM ".MAIN_DB_PREFIX."societe as s";
		$sql.= " WHERE client=1";
		$sql.= " ORDER BY s.nom ASC";
		$clients = array();
		$result = $this->db->query($sql);
		if ($result)
		{
			$i = 0;
			$num = $this->db->num_rows($result);
			while ($i < min($num,$conf->liste_limit))
			{
				$obj = $this->db->fetch_object($result);
				$clients[] = $obj;
				$i++;
			}
		}
		return $clients;
	}
	

	public function getClientTickets($clientId){

		$client = new Client($this->db);
		$client->fetch($clientId);
		$client->read_factures();
		foreach($client->factures as $f){
			$t = array();
			$t['id'] = $f[0];
			$t['facnumber'] = $f[1];
			$tickets[] = $t;
		}
		return $tickets;
	}

	public function createTicket($clientId, $ticketParams){
		global $user;
		$facture = new Facture($this->db);
		$facture->socid = $clientId;
		$facture->cond_reglement_id = "1";
		$facture->date = dol_now();

		$facture->create($user);
		return $facture;
	}

	public function deleteTicket($ticketId){

		$factureToRemove = new Facture($this->db);
		return $factureToRemove->delete((int)$ticketId);
	}

	private function _fetchProductInfo($product){
		$productStockQte = $product->getProductStockQuantite();
		$info = new stdClass();

		$info->stck = $productStockQte->real;
		$info->stcktheo = $productStockQte->getTheoriticalStock();
		$info->public_price = $product->getPublicPrice();
		$info->colisage = $product->getProductBase()->nbcv;
		$info->unite_vente = $product->getUniteVente()->libelle;

		return $info;
	}



	public function loadTicket($ticketId){

		$facture = new Facture($this->db);
		$facture->fetch($ticketId);
		$facture->fetch_lines();
		$lines = $facture->lignes;
		$ticketLines = array();
		foreach($lines as $l){
			$ticketLines[] = $this->_factureLineToJson($l);
		}
		//TODO: get Ticket Total
		return $ticketLines;
	}


	public function payTicket($ticketId,$paymentInfo){
		global $user, $conf;

		$error = 0;
		$facture = new Facture($this->db);
		$facture->fetch($ticketId);
		$amounts = array();

		$reglements = array();
		$reglements[] = "cb";
		$reglements[] = "cash";
		$reglements[] = "tip";
		$reglements[] = "virement";
		$reglements[] = "prelevement";
		$reglements[] = "cheque";

		$typeReglements = array();
		$typeReglements["tip"] = 1;
		$typeReglements["virement"] = 2;
		$typeReglements["prelevement"] = 3;
		$typeReglements["cash"] = 4;
		$typeReglements["cb"] = 6;
		$typeReglements["cheque"] = 7;

		foreach($reglements as $reglement){

			if($paymentInfo[$reglement] >0){
				$this->db->begin();
				$amounts[$ticketId] = $paymentInfo[$reglement];
				$totalpaiement = $paymentInfo[$reglement];
				// Creation de la ligne paiement
				$paiement = new Paiement($this->db);
				$paiement->datepaye     = dol_now();
				$paiement->amounts      = $amounts;   // Tableau de montant
				$paiement->paiementid   = $typeReglements[$reglement];//id: cheque, cb ...
				$paiement->num_paiement = $paymentInfo[$reglement.'_num'];
				//TODO $paiement->note         = $_POST['comment'];

				$paiement_id = $paiement->create($user);

				if ($paiement_id > 0)
				{
					if ($conf->banque->enabled)
					{
						// Insertion dans llx_bank
						$label = "(CustomerInvoicePayment)";
						$paymentInfo['accountid'] = 1; //TODO
						$acc = new Account($this->db, $paymentInfo['accountid']);

						$bank_line_id = $acc->addline($paiement->datepaye,
						$paiement->paiementid,	// Payment mode id or code ("CHQ or VIR for example")
						$label,
						$totalpaiement,
						$paiement->num_paiement,
	      															'',
						$user,
						$paymentInfo[$reglement.'_emetteur'],
						$paymentInfo[$reglement.'_bank']);

						// Mise a jour fk_bank dans llx_paiement.
						// On connait ainsi le paiement qui a genere l'ecriture bancaire
						if ($bank_line_id > 0)
						{
							$paiement->update_fk_bank($bank_line_id);
							// Mise a jour liens (pour chaque facture concernees par le paiement)
							foreach ($paiement->amounts as $key => $value)
							{
								$facid = $key;
								$facture->fetch($facid);
								$facture->fetch_client();
								$acc->add_url_line($bank_line_id,
								$paiement_id,
								DOL_URL_ROOT.'/compta/paiement/fiche.php?id=',
		        									 '(paiement)',
		        									 'payment');
								$acc->add_url_line($bank_line_id,
								$facture->client->id,
								DOL_URL_ROOT.'/compta/fiche.php?socid=',
								$facture->client->nom,
		       										'company');
							}
						}
						else
						{
							$error++;
						}
					}
				}
				else
				{
					$error++;
				}

				if ($error == 0)
				{
					$this->db->commit();
				}
				else
				{
					$this->db->rollback();
				}
			}
		}
		if($facture->user_valid <= 0){
			$valresult = $facture->validate($user);
		}

		$ei = $this->getEncaissementInfo($facture->id, $facture->client->id);
		if(price2num($ei->ticketInfo->paid) >= $facture->total_ttc){
			$facture->set_paid($user);
		}
		return $facture->ref;
	}

	public function addTicketItem($ticketId, $item, $clientId){

		$status = "ok";

		$product = new MakinaProduct($this->db);
		$product->fetch($item['fk_product']);

		$facture = new Facture($this->db);
		$facture->fetch($ticketId);

		$item = $this->updateItemDiscount($item, $product, $clientId);

		$res = $facture->addline($ticketId,'',$item['subprice'],$item['qty'],$product->tva_tx, 0,0,$item['fk_product'],$item['remise_percent'],'','',0,$product->npr, '','HT','',0,$item['extra']);

		if($res < 0){
			if($res == -2){
				$status = "Facture deja validee";
			}else{
				$status = $flx->error;
			}
		}else{
			$item['rowid'] = $facture->lastInsertLineId;
		}

		$dataResult = array();
		$dataResult['status'] = $status;
		$dataResult['item'] = $item;
		return $dataResult;

	}

	public function updateTicketItem($ticketId, $item, $clientId){
		global $user;
		$status = "ok";

		$facture = new Facture($this->db);
		$facture->fetch($ticketId);

		$product = new MakinaProduct($this->db);
		$product->fetch($item['fk_product']);


		$item = $this->updateItemDiscount($item, $product, $clientId);

		//=============================
		//===== UPDATE LINE EXTRA =====
		//=============================

		$flx = new FactureLigneExtra($this->db);
		$flx->fetch($item['rowid'], 1);
		if($flx->id > 0){
			$flxJsonDataFields = $flx->getJsonDataFields();
			foreach($flxJsonDataFields as $field){
				if($item['extra'][$field] != null){
					$flx->$field = $item['extra'][$field];
				}
			}

			$res = $flx->update($user);
			if($res < 0){
				$status = $flx->error;
			}
		}

		//=======================
		//===== UPDATE LINE =====
		//=======================
		$res = $facture->updateline($item['rowid'],	'',	$item['subprice'],$item['qty'],$item['remise_percent'],'','',	$product->tva_tx, 0,0,'HT',	$product->npr,	0);
		if($res < 0){
			if($res == -2){
				$status = "Facture deja validee";
			}else{
				$status = $facture->error;
			}
		}

		$dataResult = array();
		$dataResult['status'] = $status;
		$dataResult['item'] = $item;
		return $dataResult;

	}

	public function updateItemDiscount($item, $product, $clientId){

		//===========================
		//===== CHECK DISCOUNT ======
		//===========================
		//if discount wasn't forced manually, check for a better one
		if($item['extra']['discount_origin'] != "Manual"){

			$productDiscount = new ProductDiscount($this->db);
			$bestDiscount = $productDiscount->getProductDiscount($item['fk_product'], $clientId, $item['qty']);

			$item['extra']['public_price'] = $product->getPublicPrice();
			$item['extra']['discount_origin'] = $bestDiscount['discountOrigin'];
			$item['extra']['discount_type'] = $bestDiscount['discountType'];
			$item['extra']['discount_limit'] = $bestDiscount['discountLimit'];
			$item['extra']['discount_qtymin'] = $bestDiscount['discountQtyMin'];

			if($bestDiscount['discountOrigin'] == "ClientLevel"){
				$item['remise_percent'] = 0;
				$item['subprice'] = $bestDiscount['discountPrice'];
			}else{
				$item['remise_percent'] = $bestDiscount['discountPct'];
				$item['subprice'] = $product->getPublicPrice();
			}
		}
		return $item;

	}

	public function removeTicketItem($ticketId, $item){
		global $user;
		$facture = new Facture($this->db);
		$facture->fetch($ticketId);
		return $facture->deleteline($item['rowid'], $user);
	}

	public function getProducts($filters){
		global $conf;
		$ps = array();

		$ref = $filters['ref'];
		$barcode = $filters['barcode'];
		$label = $filters['label'];


		$sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,';
		$sql.= ' p.fk_product_type, p.tms as datem,';
		$sql.= ' p.duration';
		$sql.= ' FROM '.MAIN_DB_PREFIX.'product as p';
		$sql.= " WHERE  p.entity = ".$conf->entity;
		if ($ref)     $sql.= " AND p.ref like '%".$ref."%'";
		if ($barcode) $sql.= " AND p.barcode like '%".$barcode."%'";
		if ($label)     $sql.= " AND p.label like '%".addslashes($label)."%'";
		//====================
		//====================
		$limit = 500;
		$offset = 0;
		//====================
		//====================


		//$sql.= $db->order($sortfield,$sortorder);
		$sql.= $this->db->plimit($limit + 1 ,$offset);
		$resql = $this->db->query($sql) ;
		if ($resql)
		{
			$num = $this->db->num_rows($resql);
			$i = 0;
			while($i < $num && $i<$limit){
				$p = $this->db->fetch_object($resql);
				$ps[] = $p;
				$i++;
			}
		}
		return $ps;
	}

	public function getItem($params){
		global $user;

		$user = MakinaUser::getCurrentUserInstance();
		$product = new MakinaProduct($this->db);
		$product->fetch($params['prodid']);
		if($product->id < 0 ){
			return null;
		}

		//=============
		//=== basic ===
		//=============

		$json = new stdClass();

		$json->rowid = -1;
		$json->fk_product = $product->id;
		$json->libelle = $product->libelle;
		$json->product_ref = $product->ref;
		$json->qty = $qty;

		//============
		//=== info ===
		//============


		$json->info = $this->_fetchProductInfo($product);

		$prodDiscount = new ProductDiscount($this->db);
		$discount = $prodDiscount->getProductDiscount($params['prodid'], $params['clientid'], $params['qty']);

		$json->info->remise_type_limit = $discount['discountLimit'];

		$json->remise_percent = $discount['discountPct'];
		if($discount['discountOrigin'] == "ClientLevel"){
			$json->subprice = $discount['discountPrice'];
		}else{
			$json->subprice = $product->getPublicPrice();
		}
		$json->total_ht = $json->qty * $json->subprice;

		//=============
		//=== extra ===
		//=============

		$facData = new stdClass();

		$facData->fk_product = $product->id;
		$facData->rowid = -1;
		$facData->public_price = $product->getPublicPrice();
		$facData->discount_origin = $discount['discountOrigin'];
		$facData->discount_type = $discount['discountType'];
		$facData->discount_limit = $discount['discountLimit'];
		$facData->discount_qtymin = $discount['discountQtyMin'];

		$json->extra = new stdClass();
		$json->unit = new stdClass();

		$flx = new FactureLigneExtra($this->db);
		$flx->fetchProductData($facData);

		//UNIT
		if($flx->uncv > 0){
			$unite = new C_unitemesure($this->db);
			$unite->fetch($flx->uncv);
			$json->unit->nbdec = $unite->nbdec;
			$json->unit->fmcal = $unite->fmcal;
		}

		$flxToJson = $this->_factureLineExtraToJson($flx);
		$json->extra = $flxToJson->extra;

		return $json;
	}

	private function _factureLineToJson($line){

		$product = new MakinaProduct($this->db);
		$product->fetch($line->fk_product);

		$json = new stdClass();
		$json->rowid = $line->rowid;
		$json->product_ref = $line->product_ref;
		$json->libelle = $line->libelle;
		$json->qty = $line->qty;
		$json->subprice = $line->subprice;
		$json->remise_percent = $line->remise_percent;
		$json->fk_product = $line->fk_product;
		$json->total_ht = $line->total_ht;
		$json->total_ttc = $line->total_ttc;

		$json->info = $this->_fetchProductInfo($product);

		$flx = new FactureLigneExtra($this->db);
		$flx->fetch($line->rowid,1);
		$flxToJson = $this->_factureLineExtraToJson($flx);
		$json->extra = $flxToJson->extra;
		$json->unit = $flxToJson->unit;

		if($json->extra->nbcv){
			$json->extra->qtyu = $json->qty - $json->extra->nbcv * $json->extra->qtyc;
		}

		return $json;
	}

	private function _factureLineExtraToJson($flx){

		$json = new stdClass();
		$json->extra = new stdClass();
		$json->unit = new stdClass();

		if($flx->uncv > 0){
			$unite = new C_unitemesure($this->db);
			$unite->fetch($flx->uncv);
			$json->unit->nbdec = $unite->nbdec;
			$json->unit->fmcal = $unite->fmcal;
		}

		$jsonDataFields = $flx->getJsonDataFields();
		foreach($jsonDataFields as $field){
			$json->extra->$field = $flx->$field;
		}
		return $json;
	}

	public function dolibarrLogin($username, $password){
		global $conf;

		//require('include/environnement.php');
		require('class/Auth.class.php');

		// Check password
		$auth = new Auth($this->db);
		$res = $auth->verif ($username, $password);

		if ( $res >= 0 )
		{
			$return=array();

			$sql = "SELECT rowid, name, firstname";
			$sql.= " FROM ".MAIN_DB_PREFIX."user";
			$sql.= " WHERE login = '".$username."'";
			$sql.= " AND entity IN (0,".$conf->entity.")";

			$result = $this->db->query($sql);
			if ($result)
			{
				$tab = $this->db->fetch_array($res);

				foreach ( $tab as $key => $value )
				{
					$return[$key] = $value;
				}

				$_SESSION['uid'] = $tab['rowid'];
				$_SESSION['uname'] = $username;
				$_SESSION['nom'] = $tab['name'];
				$_SESSION['prenom'] = $tab['firstname'];

				return $return;
			}
		}
		return -1;
	}

	public function getEncaissementInfo($ticketId, $clientId){
		global $conf;
		$facture = new Facture($this->db);
		$facture->fetch($ticketId);

		$client = new MakinaClient($this->db);
		$client->fetch($clientId);

		$result = new stdClass();
		$result->clientInfo = new stdClass();
		$result->clientInfo->name = $client->name;
		$result->clientInfo->solde = price($client->getBalance()->solde);
		if($client->getBlocking()){
			$result->clientInfo->blocage = $client->getBlocking()->getBlockCodeLabel();
			$result->clientInfo->plafond = price($client->getBlocking()->plafond);
			$result->clientInfo->depassement = $client->getBlocking()->depassement;
		}else{
			$result->clientInfo->blocage = "";
			$result->clientInfo->plafond = price(0);
			$result->clientInfo->depassement = yn(0);
		}
		$paid = 0;
		$result->historique = $client->getPaymentsForFacture($ticketId);
		$result->historique_num = count($result->historique);
		foreach ($result->historique as $p){
			$regl = $p['amount'];
			$regl = str_replace(" ","",$regl);
			$regl = str_replace(",",".",$regl);
			$paid += $regl;
		}
		$publicPriceTotal_ht = $this->_getFacturePublicPrice($facture);

		$result->ticketInfo = new stdClass();
		$result->ticketInfo->total_ht = price($facture->total_ht);
		$result->ticketInfo->total_ttc= price($facture->total_ttc);
		$result->ticketInfo->discount = round((1 - $facture->total_ht  / $publicPriceTotal_ht) * 100, 3);
		$result->ticketInfo->paid = price($paid);
		$result->ticketInfo->unpaid = price($facture->total_ttc - $paid);


		$result->warning = ($result->clientInfo->blocage && $result->clientInfo->solde + $result->ticketInfo->total_ttc > $result->clientInfo->plafond);
		$result->warning = $result->warning || ($conf->global->CASHDESK_ID_THIRDPARTY == $clientId);
		
		return $result;

	}

	private function _getFacturePublicPrice($facture){
		$facture->fetch_lines();
		$publicPriceTotal_ht = 0;
		foreach($facture->lignes as $l){
			$product = new MakinaProduct($this->db);
			$product->fetch($l->fk_product);
			$publicPriceTotal_ht += $product->getPublicPrice() * $l->qty;
		}
		return $publicPriceTotal_ht;
	}
}

?>