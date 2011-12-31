<?php
require_once(DOL_DOCUMENT_ROOT."/societe/class/societe.class.php");
require_once(DOL_DOCUMENT_ROOT."/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT."/categories/class/categorie.class.php");
require_once(DOL_DOCUMENT_ROOT."/prixbase/class/product_pricebase.class.php");
require_once(DOL_DOCUMENT_ROOT."/categorieremise/class/categorie_remise.class.php");
require_once(DOL_DOCUMENT_ROOT."/societeremiseproduit/class/societe_remise_prod.class.php");


/**
 *      \class      Societe_remise_prod
 *      \brief      Put here description of your class
 *		\remarks	Initialy built by build_class_from_table on 2010-09-23 00:22
 */
class ProductDiscount // extends CommonObject
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	//var $element='societe_remise_prod';			//!< Id that identify managed objects
	//var $table_element='societe_remise_prod';	//!< Name of table without prefix where object is stored

	function ProductDiscount($DB)
	{
		$this->db = $DB;
		return 1;
	}

	function getProductDiscount($idprod, $socid, $qty){
		global $conf;

		//Fetch Product and Soc Info
		$product = new MakinaProduct($this->db);
		$product->fetch($idprod);
		$soc = new Societe($this->db);
		$soc->fetch($socid);

		$publicPrice = $product->getPublicPrice();
		//Set min Price
		$price_min = $product->price_min;
		if ($conf->global->PRODUIT_MULTIPRICES){
			$pricelevel_min = $product->multiprices_min[$soc->price_level];
			$price_min = $pricelevel_min?$pricelevel_min:$price_min;
		}


		$maxAllowedDiscount = 0;
		$bestDiscount = 0;
		$allDiscounts = array();
		$discountLimits = array();
		$typeLimit = '';

		$c = new Categorie($this->db);
		$prodCats = $c->containing($product->id, 0);
		$socCats = $c->containing($soc->id, 2);



		// ===================
		// ==== DISCOUNTS ====
		// ===================

		//Cycle through Client/Product gategories to fetch associated discounts
		$socCats = $c->containing($soc->id, 2);
		foreach ($socCats as $socCat){
			$socRemiseProd = new Societe_remise_prod($this->db);
			$socRemiseProd->fetch($socCat->id, $product->id, "CategoryClientOnProduct");
			$allDiscounts[] = $socRemiseProd;//->getDiscountPct($product->id);

			foreach ($prodCats as $prodCat){
				$socRemiseProd = new Societe_remise_prod($this->db);
				$socRemiseProd->fetch($socCat->id, $prodCat->id, "CategoryClientOnCategoryProduct");
				$allDiscounts[] = $socRemiseProd;//->getDiscountPct($product->id);
			}
		}

		foreach ($prodCats as $prodCat){
			$socRemiseProd = new Societe_remise_prod($this->db);
			$socRemiseProd->fetch($soc->id, $prodCat->id, "ClientOnCategoryProduct");
			$allDiscounts[] = $socRemiseProd;//->getDiscountPct($product->id);
		}

		$socRemiseProd = new Societe_remise_prod($this->db);
		$socRemiseProd->fetch($soc->id, $product->id, "ClientOnProduct");
		$allDiscounts[] = $socRemiseProd;

		//Product Promo
		$socRemiseProd = new Societe_remise_prod($this->db);
		$socRemiseProd->fetch(null, $product->id, "Product");
		$allDiscounts[] = $socRemiseProd;

		//Client Default Discount
		if ($soc->remise_client){
			$socRemiseProd = new Societe_remise_prod($this->db);
			$socRemiseProd->setDiscountOrigin("ClientDefaultDiscount");
			$socRemiseProd->setDiscountType("coeff");
			$socRemiseProd->setDiscountValue($soc->remise_client);
			$allDiscounts[] = $socRemiseProd;
		}
		// ===================
		// ===== LIMITS ======
		// ===================

		// get all categories and find the lowest discount allowed
		$prodCats = $c->containing($product->id, 0);
		foreach ($prodCats as $cat){
			$catRemise = new categorie_remise($this->db);
			$catRemise->fetch($cat->id, 1);
			$discountLimits[] = array('ProductCategoryMaxDiscount', $catRemise->remisemax);
		}

		//Get Wholesale price to make sure we are not selling under it
		$productPriceBase = new Product_pricebase($this->db);
		$productPriceBase->fetch($product->id, 1);
		$productCost = $productPriceBase->prht;
		$productCostPct = (1 - $productCost/$publicPrice)*(100);
		$discountLimits[] = array('productCost',$productCostPct);

		//if pricemin exists, make sure discount is not lower
		if($price_min > 0){
			$productMinPricePct = (1- $price_min/$publicPrice)*(100);
			$minPrx =price2num($publicPrice)*(1-price2num($productMinPricePct)/100);
			$discountLimits[] = array('priceMin', $productMinPricePct);
		}


		//Get Strictest Discount Limitation
		$maxAllowedDiscount = 100;
		foreach($discountLimits as $l){
			if( $l[1] < $maxAllowedDiscount){
				$typeLimit = $l[0];
				$maxAllowedDiscount = $l[1];
			}
		}


		//get Best Discount
		$bestDiscount = null;
		$bestPct = 0;
		foreach($allDiscounts as $d){
			$dPct = $d->getDiscountPct($product->id);
			if($d->isDiscountValid() && $d->getMinQty() <= $qty && $dPct > $bestPct){
				$bestDiscount = $d;
				$bestPct = $dPct;
			}
		}

		//Set Price Level if Better
		if ($conf->global->PRODUIT_MULTIPRICES){
			$clientTarif = $product->multiprices[$soc->price_level];
			if($bestDiscount){
				$bestPrice = $bestDiscount->getDiscountPrice($product->id);
			}else{
				$bestPrice = $product->getPublicPrice();
			}
			if($bestPct <= 0 || $bestPrice >= $clientTarif){
				$socRemiseProd = new Societe_remise_prod($this->db);
				$socRemiseProd->setDiscountType("level");
				$socRemiseProd->setDiscountValue($soc->price_level);
				$socRemiseProd->setDiscountOrigin("ClientLevel");
				$bestDiscount = $socRemiseProd;
				$bestPct = $socRemiseProd->getDiscountPct($product->id);
			}
		}

		//Apply restriction to best appliable discount
		$pct = ( $maxAllowedDiscount > 0 && $maxAllowedDiscount < $bestPct) ? $maxAllowedDiscount : $bestPct;
		$pct = number_format($pct,2);
		
		//set default discount if there is no discount available
		if(!$bestDiscount){
			$socRemiseProd = new Societe_remise_prod($this->db);
			$socRemiseProd->setDiscountType("level");
			$socRemiseProd->setDiscountValue($soc->price_level);
			$socRemiseProd->setDiscountOrigin("ClientLevel");
			$bestDiscount = $socRemiseProd;
			$bestPct = $socRemiseProd->getDiscountPct($product->id);
		}

		$result = array();
		$result['discountPrice'] = $bestDiscount->getDiscountPrice($product->id);
		$result['discountType'] = $bestDiscount->getDiscountType();
		$result['discountOrigin'] = $bestDiscount->getDiscountOrigin();
		$result['discountPct'] = $pct;
		$result['discountLimit'] = $typeLimit;
		$result['discountQtyMin'] = $bestDiscount->getMinQty();

		return $result;
	}


	function getProductDiscount_ORIG($idprod,$socid, $currentDiscount,$qty){
		global $conf;

		//Fetch Product and Soc Info
		$product = new Product($this->db);
		$product->fetch($idprod);
		$soc = new Societe($this->db);
		$soc->fetch($socid);

		//Set initPrice to Public Price
		if ($conf->global->PRODUIT_MULTIPRICES){
			$initPrice = $product->multiprices[1];
			$price_min = $product->multiprices_min[$soc->price_level];
		}else{
			$initPrice = $product->price;
			$price_min = $product->price_min;
		}


		$maxAllowedDiscount = 0;
		$bestDiscount = 0;
		$discountLimits = array();
		$allDiscounts = array();
		//TODO: $typeDiscount should be a param
		$typeDiscount = 'pct';
		$typeLimit = '';
		//Collect all Discounts
		$allDiscounts[] = array($typeDiscount, $currentDiscount);

		if($conf->categorieremise->enabled){
			$c = new Categorie($this->db);
			//get all categories and find the lowest discount allowed
			$cats = $c->containing($product->id, 0);
			foreach ($cats as $cat){
				$catRemise = new categorie_remise($this->db);
				$catRemise->fetch($cat->id, 1);
				$discountLimits[] = array('catMaxDiscount', $catRemise->remisemax);
				$socRemiseProd = new Societe_remise_prod($this->db);
				$socRemiseProd->fetch($product->id, $soc->id, "productsoc");
				$allDiscounts[] = array($socRemiseProd->getTypeDiscount(), $socRemiseProd->productDiscountAsPct($product));
				$socRemiseProd->fetch($product->id, $cat->id, "categorysoc");
				$allDiscounts[] = array($socRemiseProd->getTypeDiscount(), $socRemiseProd->productDiscountAsPct($product));
			}
		}


		//Product Promo
		$socRemiseProd = new Societe_remise_prod($this->db);
		$socRemiseProd->fetch($product->id, '', "product");
		if($socRemiseProd->qte <= $qty){
			$allDiscounts[] = array($socRemiseProd->getTypeDiscount(), $socRemiseProd->productDiscountAsPct($product));
		}

		//PriceLevel Discount
		if ($conf->global->PRODUIT_MULTIPRICES){

		}

		//Get Wholesale price to make sure we are not selling under it
		$productPriceBase = new Product_pricebase($this->db);
		$productPriceBase->fetch($product->id, 1);
		$productCost = $productPriceBase->prht;
		$productCostPct = ($initPrice - $productCost)*(100/$initPrice);
		$discountLimits[] = array('productCost',$productCostPct);


		//if pricemin exists, make sure discount is not lower
		if($price_min > 0){
			$productMinPricePct = ($initPrice- $price_min)*(100/$initPrice);
			$minPrx =price2num($initPrice)*(1-price2num($productMinPricePct)/100);
			$discountLimits[] = array('priceMin', $productMinPricePct);
		}


		//Get Strictest Discount Limitation
		$maxAllowedDiscount = 0;
		foreach($discountLimits as $l){
			if( $l[1] < $maxAllowedDiscount){
				$typeLimit = $l[0];
				$maxAllowedDiscount = $l[1];
			}
		}

		//get Best Discount
		$bestDiscount = 0;
		foreach($allDiscounts as $d){
			if( $d[1] > $bestDiscount){
				$typeDiscount = $d[0];
				$bestDiscount = $d[1];
			}
		}

		if ($conf->global->PRODUIT_MULTIPRICES){
			//If Client price level is smaller than public price, add it to available discounts
			$productPriceLevelDiscount = ($initPrice - $product->multiprices[$soc->price_level])*($initPrice)/100 ;
			if($bestDiscount == 0 || $productPriceLevelDiscount > $bestDiscount){
				$typeDiscount = 'priceLevel';
				$bestDiscount = $productPriceLevelDiscount;
			}
		}

		//Apply restriction to best appliable discount
		$pct = ( $maxAllowedDiscount > 0 && $maxAllowedDiscount < $bestDiscount) ? $maxAllowedDiscount : $bestDiscount;
		$pct = number_format($pct,2);

		$result = array();
		$result['typeDiscount'] = $typeDiscount;
		$result['typeLimit'] = $typeLimit;
		$result['pct'] = $pct;

		return $result;
	}
}
?>