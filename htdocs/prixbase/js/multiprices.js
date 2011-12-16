$(document).ready(function() {
	initPrices();
});
function initPrices() {
	var px = new Array();
	var priceInput = $('#price');
	for ( var i = 1; i <= numPrices ; i++) {

		var coeffTr = document.createElement('tr');
		var coeffTdLabel = document.createElement('td');
		var coeffTdInput = document.createElement('td');
		var coeffInput = document.createElement('input');

		var priceInput = $(numPrices > 1 ? '#price_' + i : '#price');
		priceInput.bind('blur', function() {
			adjustCoeffByPrice($(this));
		});
		priceInput.attr('level', i);
		
		
		coeffTdLabel.innerHTML = "Coeff Pr/Pv " + (numPrices > 1 ? i : '');
		coeffInput.id = numPrices > 1 ? "coeff_" + i : "coeff";
		coeffInput.setAttribute('price', i);
		if(isTTC(priceInput)){
			coeffInput.value = (getFieldFloatValue(priceInput) / prttc );
		}else{
			coeffInput.value = (getFieldFloatValue(priceInput) / prht  );
		}
		valueFormat($(coeffInput),3);
		
		coeffInput.onblur = function(e) {
			coeffBlurr($('#' + e.target.id));
		};
		coeffTdInput.appendChild(coeffInput);
		coeffTr.appendChild(coeffTdLabel);
		coeffTr.appendChild(coeffTdInput);

		px[i] = document.getElementById(numPrices > 1 ? 'price_' + i : 'price');
		holdingTr = px[i].parentNode.parentNode;
		holdingTr.parentNode.insertBefore(coeffTr, holdingTr);

		
		priceBaseCombo = $('select[name="price_base_type"]');
		if (numPrices > 1) {

			var priceBaseCombo = $('#price_' + i).next('select');
			priceBaseCombo.bind('change',
					function(e) {
						adjustCoeffByPrice($('#'
								+ e.target.previousElementSibling.id));
					});
			var priceMin= $('#price_' + i).parent().parent().next().children(1).children(0);
		} else {
			var priceBaseCombo = $('select[name="price_base_type"]');
			priceBaseCombo.bind('change', function(e) {
				adjustCoeffByPrice($('#price'));
			});
			var priceMin= $('#price').parent().parent().next().children(1).children(0);
		}
		priceMin.val(prht);
		valueFormat(priceMin,2);
	}
}


function coeffBlurr(coeffInput) {
	valueFormat(coeffInput, 3);
	if (numPrices < 2) {
		var priceInput = $('#price');
	} else {
		var priceInput = $('#price_' + coeffInput.attr('price'));
	}
	var revien = isTTC(priceInput) ? prttc : prht;
	var price = 0;
	if (prht > 0) {
		price = coeffInput.val() * revien;
	}
	priceInput.val(price);
	valueFormat(priceInput, 2);

}
function adjustCoeffByPrice(priceInput) {
	valueFormat(priceInput, 2);
	var price = priceInput.val().replace(",", ".").replace(" ", "");
	var revien = isTTC(priceInput) ? prttc : prht;
	if (revien > 0) {
		var newcoeff = price / revien;
	} else {
		var newcoeff = 0;
	}
	if (numPrices < 2) {
		var coeffInput = $('#coeff');
	} else {
		var coeffInput = $('#coeff_' + priceInput.attr('level'));
	}
	coeffInput.val(newcoeff);
	valueFormat(coeffInput, 3);
}

function isTTC(priceInput) {
	if (numPrices < 2) {
		var priceBaseCombo = $('select[name="price_base_type"]');
	} else {
		var priceBaseCombo = priceInput.next('select');
	}
	return priceBaseCombo.val() == "TTC";

}