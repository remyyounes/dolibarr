var afterProductSelect = function(){
	//alert("afterProductSelect: Remise");
}

function setProdId(pid){
	prodId = pid;
};
function setProdType(ptype){
	prodType = ptype;
};
function setTypeRemise(rtype){
	typeRemise = rtype;
};
function updateId(){
	prodId = document.getElementsByName(prodId_field)[0].value;
};

function updateFinalPrice(remiseValue){
	//updateId();
	//alert(url);
	var successVal = 'ajdynfield'+getDiscountField();
	var option = "&prodType="+prodType+"&typeRemise="+typeRemise+"&prodId="+prodId+"&socPriceLevel="+priceLevel;
	var myAjax = new Ajax.Updater( {
		 success: successVal },
		 url, {
			method: 'get',
			parameters: ks+"="+$F(getDiscountField())+"&htmlname="+getDiscountField()+option
		 });
		 
}
/*
function updateProductInfo(serializedForm){
	alert('upd');
	return;
	var fields = serializedForm.split("&");
	prodId = fields[0].split("=")[1];
	prodRef = fields[1].split("=")[1];
	var prodDesc = fields[2].split("=")[1];
	if(prodId <= 0){
		return;
	}
	$(prodId_field).value = prodId;
	$("product_info").innerHTML =  "   --- " + prodRef + " -- " + unescape(prodDesc);
}
*/

function isValidDiscount(){
	//updateId();	
	if( prodId <= 0 && prodType == 'product' ){
		displayFormError(SelectProduct);
		return false;
	}
	if( typeRemise <= 0 ){
		displayFormError(SelectDiscountType);
		return false;
	}
	var discount = getDiscountField();
	discountVal = $F(discount).replace(/^\s*|\s*$/g,'');
	if( discountVal.length <= 0 ){
		displayFormError(SetDiscountValue);
		return false;
	}
	return true;
}

function getDiscountField(){
	return typeRemises[typeRemise];
}

function displayFormError(err){
	var errEl = $("formError");
	errEl.innerHTML = err;
	errEl.className="error";
}