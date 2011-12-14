$(document).ready(function() {
	initPrixBase();
});

function initPrixBase() {
	var pa = $('#cf_pa');
	var pamp = $('#cf_pamp');
	var prht = $('#cf_prht');
	var prmpht = $('#cf_prmpht');
	var coeff = $('#cf_coeff');
	var coeff_mp = $('#cf_coeff_mp');
	

	coeff.bind('blur', function() {
		calculatePR($(this));
		calculateTTC();
	});
	coeff_mp.bind('blur', function() {
		calculatePR($(this));
		calculateTTC();
	});

	pa.bind('blur', function() {
		calculateCoeff($(this));
	});
	pamp.bind('blur', function() {
		calculateCoeff($(this));
	});
	prht.bind('blur', function() {
		calculateCoeff($(this));
		calculateTTC();
	});
	prmpht.bind('blur', function() {
		calculateCoeff($(this));
		calculateTTC();
	});

}

function calculateCoeff(obj) {

	formatFields();
	
	var pa = getFieldFloatValue($('#cf_pa'));
	var prht = getFieldFloatValue($('#cf_prht'));
	var pamp = getFieldFloatValue($('#cf_pamp'));
	var prmpht = getFieldFloatValue($('#cf_prmpht'));

	if (pa && prht) {
		$('#cf_coeff').val(parseFloat(prht) / parseFloat(pa));
	}
	if (pamp && prmpht) {
		$('#cf_coeff_mp').val(parseFloat(prmpht) / parseFloat(pamp));
	}
	formatFields();

}

function formatFields() {

	valueFormat($("#cf_coeff"), 3);
	valueFormat($("#cf_pa"), 2);
	valueFormat($("#cf_prht"), 2);
	valueFormat($("#cf_coeff_mp"), 3);
	valueFormat($("#cf_pamp"), 2);
	valueFormat($("#cf_prmpht"), 2);
}

function calculatePR(obj) {

	formatFields();
	var pa = parseFloat($('#cf_pa').val());
	var coeff = parseFloat($('#cf_coeff').val());
	var prht = coeff * pa;
	$('#cf_prht').val(prht);

	var pamp = parseFloat($('#cf_pamp').val());
	var coeff_mp = parseFloat($('#cf_coeff_mp').val());
	var prmpht = coeff_mp * pamp;
	$('#cf_prmpht').val(prmpht);

	formatFields();
}

function adjustPriceByCoeff(obj, pr, level) {
	valueFormat(obj, 3);
	var coeff = obj.val().replace(",", ".").replace(" ", "");
	var newPrice = coeff * pr;
	var price = $('#price_' + level);
	price.val(newPrice);
	valueFormat(price, 2);
}

function adjustCoeffByPrice(obj, pr, level) {
	valueFormat(obj, 2);
	var price = obj.val().replace(",", ".").replace(" ", "");
	if (pr > 0) {
		var newcoeff = price / pr;
	} else {
		var newcoeff = 0;
	}
	var coeff = $('#coeff_vente' + level);
	coeff.val(newcoeff);
	valueFormat(coeff, 3);
}

function calculateTTC() {
	
	// get price HT
	var ht = getFieldFloatValue($('#cf_prht'));
	var ht_mp = getFieldFloatValue($('#cf_prmpht'));
	
	// get TVA and NPR
	if (typeof productNPR == 'undefined') {
		productNPR = 0;
	}
	if (typeof productTVA == 'undefined' || productNPR) {
		productTVA = 0;
	}
	
	//multiply and set
	$('#cf_prttc').val(   ht    + ht    * productTVA / 100);
	$('#cf_prmpttc').val( ht_mp + ht_mp * productTVA / 100);
	
	//format
	valueFormat($('#cf_prttc'), 2);
	valueFormat($('#cf_prmpttc'), 2);
}