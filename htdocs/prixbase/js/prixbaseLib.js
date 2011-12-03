function calculateCoeff(obj){
	
	formatFields();
	var pa = $F('pb_pa');
	var prht = $F('pb_prht');
	var coeff = parseFloat(prht) / parseFloat(pa);
	$('pb_coeff').value = coeff;
	
	var pamp = $F('pb_pamp');
	var prmpht = $F('pb_prmpht');
	var coeff_mp = parseFloat(prmpht) / parseFloat(pamp);
	$('pb_coeff_mp').value = coeff_mp;
	
	formatFields();
	
	if(obj.id == 'pb_prht' || obj.id == 'pb_prmpht'){
		calculateTTC(obj);
	}
}

function formatFields(){
	valueFormat($("pb_coeff"),3);
	valueFormat($("pb_pa"),2);
	valueFormat($("pb_prht"),2);
	valueFormat($("pb_coeff_mp"),3);
	valueFormat($("pb_pamp"),2);
	valueFormat($("pb_prmpht"),2);
}


function calculatePR(obj){
	
	formatFields();
	var pa = parseFloat($F('pb_pa'));
	var coeff = parseFloat($F('pb_coeff'));
	var prht = coeff * pa;
	$('pb_prht').value = prht;
	
	var pamp = parseFloat($F('pb_pamp'));
	var coeff_mp = parseFloat($F('pb_coeff_mp'));
	var prmpht = coeff_mp * pamp;
	$('pb_prmpht').value = prmpht;
	
	formatFields();
	
	if(obj.id == 'pb_prht' || obj.id == 'pb_prmpht'){
		calculateTTC(obj);
	}
}



function adjustPriceByCoeff(obj,pr,level){
	valueFormat(obj, 3);
	var coeff = obj.value.replace(",",".").replace(" ","");
	var newPrice = coeff * pr;
	var price = $('price_'+level);
	price.value = newPrice;
	valueFormat(price, 2);
}
function adjustCoeffByPrice(obj,pr,level){
	valueFormat(obj,2);
	var price = obj.value.replace(",",".").replace(" ","");
	if(pr > 0){
		var newcoeff = price / pr;
	}else{
		var newcoeff = 0;
	}
	var coeff = $('coeff_vente'+level);
	coeff.value = newcoeff;
	valueFormat(coeff,3);
}

function calculateTTC(obj){
	var ttcField = "";
	var ht = obj.value.replace(",",".").replace(" ","");
	if(obj.id == 'pb_prht'){
		ttcField = 'pb_prttc';
	}else{
		ttcField = 'pb_prmpttc';
	}
	ttcElm = $(ttcField);
	if(productNPR || productTVA <= 0){
		ttcElm.value = ht;
	}else{
		ttcElm.value = parseFloat(ht) + parseFloat(ht * productTVA / 100);
	}
	valueFormat(ttcElm,2);
		
}