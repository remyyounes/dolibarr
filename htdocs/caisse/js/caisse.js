// ===============================
// =========== CAISSE ============
// ===============================

function Caisse() {

	this.init = function() {
		this.clientId = -1;
		this.sellerId = -1;
		this.clientName = "Client";
		this.sellerName = "Vendeur";

	}, this.setClient = function(clientId, clientName) {
		this.clientId = clientId;
		this.clientName = clientName;

	}, this.getClientId = function() {
		return this.clientId;

	}, this.getSellerId = function() {
		return this.sellerId;

	}, this.setSeller = function(id, name) {
		this.sellerId = id;
		this.sellerName = name;

	}, this.setWarning = function(warning) {
		this.warning = warning;

	}, this.getWarning = function() {
		return this.warning;

	}, this.setDepassement = function(depassement) {
		this.depassement = depassement;

	}, this.getDepassement = function() {
		return this.depassement;

	}

}

// ==============================
// ======== CAISSE DECO =========
// ==============================

function CaisseDeco() {

	this.init = function(caisse) {
		this.caisse = caisse;
		this.caisse.init();

	}, this.setClient = function(clientId, clientName) {
		this.caisse.setClient(clientId, clientName);
		document.querySelector("#client").innerHTML = clientName;

	}, this.getClientId = function() {
		return this.caisse.getClientId();

	}, this.getSellerId = function() {
		return this.caisse.getSellerId();

	}, this.setSeller = function(sellerId, sellerName) {
		this.caisse.setSeller(sellerId, sellerName);
		document.querySelector("#vendeur").innerHTML = sellerName;
	}, this.setWarning = function(warning) {
		this.caisse.setWarning(warning);
	}, this.getWarning = function() {
		return this.caisse.getWarning();

	}, this.setDepassement = function(depassement) {

		this.caisse.setDepassement(depassement);

	}, this.getDepassement = function() {

		return this.caisse.getDepassement();

	}

}

// ==============================
// =========== FUNCS ============
// ==============================

var caisse = new CaisseDeco();
caisse.init(new Caisse());
setCaisse(caisse);

function showMessage(title, message, color) {
	var box = getMessageBox();
	var header = "<header style='background-color: " + color + ";'><h1>" + title + "</h1></header>";
	box.innerHTML = header;
	data = document.createElement("p");
	data.innerHTML = message;

	var div = document.createElement("div");

	div.appendChild(data);

	box.appendChild(div);
	div.classList.add("scrollContent");
	box.style.display = "block";
	box.innerHTML += boxCancelBtn();
	box.classList.remove("hideSelection");
}

function showInfo(title, message) {
	showMessage(title, message, "#2C3B4F");
}

function showError(title, message) {
	showMessage(title, message, "red");
}

function setCaisse(caisse) {
	caisse = caisse;
}

function getCaisse() {
	return caisse;
}

function getEncaissementInfo() {
	var data = new Object();
	data.ticketId = getTicket().getId();
	data.clientId = getCaisse().getClientId();
	if (data.ticketId > 0 && data.clientId > 0) {
		sendCaisseAction("getEncaissementInfo", data);
	} else {
		showError("Encaissement", "Selectionnez un client et une facture.");
	}

}

function clearPaymentMethods() {
	var paymentTable = document.querySelector("#reglements");
	while (paymentTable.childNodes.length > 2) {
		paymentTable.removeChild(paymentTable.lastChild);
	}
}
function addPaymentMethod(meth) {
	
	var reglements = new Array();
	reglements["tip"] = "TIP";
	reglements["virement"] = "Virement";
	reglements["prelevement"] = "Prelevement";
	reglements["cash"] = "Espece";
	reglements["cb"] = "Carte Bleu";
	reglements["cheque"] = "Cheque";
	
	
	var methAmmount = document.querySelector('#reglement_' + meth);
	if (!methAmmount) {
		var paymentTable = document.querySelector("#reglements");
		if (meth == "cb" || meth == "cash") {
			var tr = document.createElement("tr");
			tr.innerHTML += "<td>" + reglements[meth] + "</td><td>" + "<input autofocus placeholder='Montant' type='number' min='0' id='reglement_" + meth + "' class='reglement'>"
					+ "</td><td></td>";
			paymentTable.appendChild(tr);
		} else {
			var tr1 = document.createElement("tr");
			var tr2 = document.createElement("tr");
			tr1.innerHTML += "<td>" + reglements[meth] + "</td><td>" + "<input autofocus placeholder='Montant' type='number' min='0' id='reglement_" + meth + "' class='reglement'>"
					+ "</td><td>" + "<input placeholder='Numero' type='text' id='reglement_" + meth + "_num''>" + "</td>";
			tr2.innerHTML += "<td>" + "" + "</td><td>" + "<input placeholder='Emetteur' type='text' id='reglement_" + meth + "_emetteur'>"
					+ "</td><td>" + "<input placeholder='Banque' type='text' id='reglement_" + meth + "_banque' >" + "</td>";
			paymentTable.appendChild(tr1);
			paymentTable.appendChild(tr2);
		}
	}
	methAmmount = document.querySelector('#reglement_' + meth);
	methAmmount.focus();
}
function showEncaissementBox(data) {

	document.querySelector("#encaissement_client_name").innerHTML = data.clientInfo.name;
	document.querySelector("#encaissement_client_solde").innerHTML = data.clientInfo.solde;
	document.querySelector("#encaissement_client_blocage").innerHTML = data.clientInfo.blocage;
	document.querySelector("#encaissement_client_plafond").innerHTML = data.clientInfo.plafond;
	document.querySelector("#encaissement_client_depassement").innerHTML = (parseInt(data.clientInfo.depassement)) ? "Oui" : "Non";

	document.querySelector("#encaissement_total_ht").innerHTML = data.ticketInfo.total_ht;
	document.querySelector("#encaissement_total_ttc").innerHTML = data.ticketInfo.total_ttc;
	document.querySelector("#encaissement_total_discount").innerHTML = data.ticketInfo.discount;
	document.querySelector("#encaissement_total_paid").innerHTML = data.ticketInfo.paid;
	document.querySelector("#encaissement_total_unpaid").innerHTML = data.ticketInfo.unpaid;

	if (data.warning) {
		document.querySelector("#encaissement_client_solde").classList.add("warning");
		document.querySelector("#encaissement_client_blocage").classList.add("warning");
		document.querySelector("#encaissement_client_plafond").classList.add("warning");
		document.querySelector("#encaissement_total_ttc").classList.add("warning");
	} else {
		document.querySelector("#encaissement_client_solde").classList.remove("warning");
		document.querySelector("#encaissement_client_blocage").classList.remove("warning");
		document.querySelector("#encaissement_client_plafond").classList.remove("warning");
		document.querySelector("#encaissement_total_ttc").classList.remove("warning");
	}
	var historiqueTable = document.querySelector("#encaissement_historique");
	historiqueTable.innerHTML = "";
	for ( var i = 0; i < data.historique_num; i++) {
		var h = data.historique[i];
		var row = document.createElement("tr");
		row.innerHTML = '<td width="50px">Info</td>';
		row.innerHTML += '<td width="200px"><div class="extra">' + h['date'] + '</div>' + h['type'] + '</td>';
		row.innerHTML += '<td width="100px">Montant</td>';
		row.innerHTML += '<td width="150px">' + h['amount'] + '</td>';

		historiqueTable.appendChild(row);
	}
	getCaisse().setWarning(data.warning);
	getCaisse().setDepassement(parseInt(data.clientInfo.depassement));

	getTicket().setAmountUnPaid(data.ticketInfo.unpaid);

	clearPaymentMethods();

	getEncaissementBox().classList.remove("hideSelection");
}

function hideEncaissementBox() {
	getEncaissementBox().classList.add("hideSelection");
}

function getEncaissementBox() {
	return document.querySelector("#encaissement");
}

function getMessageBox() {
	return document.querySelector("#messageBox");
}

function hideMessageBox() {
	getMessageBox().classList.add("hideSelection");
}

function hideLoginBox() {
	getLoginBox().classList.add("hideSelection");
}

function getSelectionList() {
	return document.querySelector("#selection");
}

function getLoginBox() {
	return document.querySelector("#loginBox");
}

function hideSelectionList() {
	getSelectionList().classList.add("hideSelection");
}

function getClientList() {
	var data = new Object();
	sendCaisseAction("getClientList", data);
}

function saveItem(ticketId, item) {
	var data = new Object();
	data.item = item;
	data.ticketId = ticketId;
	data.clientId = getCaisse().getClientId();
	sendCaisseAction("saveItem", data);
}

function checkLogin(event) {
	if (event.keyCode == 13) {
		login();
	}
}

function checkGetProducts(event) {
	if (event.keyCode == 13) {
		getProducts();
	}
}
function checkSave(event) {
	if (event.keyCode == 13) {
		getTicket().save();
	}
}

function getPaymentRemainder(inpt) {
	var inputs = document.querySelectorAll("input.reglement");
}
function getProducts() {
	var data = new Object();
	data.filters = new Object();
	data.filters.label = document.querySelector("#productQuery_label").value;
	data.filters.ref = document.querySelector("#productQuery_ref").value;
	data.filters.barcode = document.querySelector("#productQuery_barcode").value;
	sendCaisseAction("getProducts", data);
}

function login() {
	var data = new Object();
	data.username = document.querySelector("#login_username").value;
	data.password = document.querySelector("#login_password").value;
	// data.token = document.querySelector("#login_token").value;
	sendCaisseAction("login", data);
	hideLoginBox();
}

function logout() {
	var data = new Object();
	sendCaisseAction("logout", data);
}

function getItem(id) {

	var rowContainingProductId = getTicket().containsProduct(id);
	if (getTicket().isCumulable() && rowContainingProductId > 0) {
		getTicket().edit(rowContainingProductId);
		showInfo("Edition Ticket", "L'article existe deja dans le ticket en edition.");
	} else {
		var data = new Object();
		data.params = new Object();
		data.params.prodid = id;
		data.params.qty = 1;
		data.params.clientid = getCaisse().getClientId();
		sendCaisseAction("getItem", data);
	}
}

function getClientTickets() {
	if (getCaisse().getClientId() < 0) {
		showInfo("Chargement Ticket", "Client indefini");
		return;
	}
	var data = new Object();
	data.clientId = getCaisse().getClientId();
	sendCaisseAction("getClientTickets", data);
}

function showList(header, data) {
	var footer = listCancelBtn();
	var box = getSelectionList();
	showBox(box, header, data, footer)
}

function showLogin() {
	hideMessageBox();
	hideSelectionList();
	var header = "<header><h1>Authentification</h1></header>";
	var data = document.createElement('div');
	var footer = loginBtns();
	var box = getLoginBox();
	data.innerHTML += "<form>";
	// data.innerHTML += "<input type='hidden id='login_token' name='token'
	// value='" + token + "'>";
	var defaultPassword = 'younes';//"ets-pommez";
	var defaultUserName = "dolibarr_admin";//"jean";

	var onKeyPress = ' onkeypress="checkLogin(event);" ';
	data.innerHTML += "<input id='login_username' type='text' placeholder='username' name='username' " + onKeyPress + " value='" + defaultUserName + "' autofocus ><br>";
	data.innerHTML += "<input id='login_password' placeholder='password' type='password' name ='userpass' " + onKeyPress + " value='" + defaultPassword + "'>";
	data.innerHTML += "</form>";
	showBox(box, header, data, footer);
}

function showBox(box, header, data, footer) {
	box.innerHTML = header;

	var div = document.createElement("div");
	div.appendChild(data);
	box.appendChild(div);
	div.classList.add("scrollContent");

	box.style.display = "block";
	box.innerHTML += footer;
	box.classList.remove("hideSelection");

}

function getLoader() {
	return document.querySelector("#loader");
}

function showLoader() {
	loading = true;
	var loader = getLoader();
	loader.classList.remove("hideBlock");
}

function hideLoader() {
	loading = false;
	var loader = getLoader();
	loader.classList.add("hideBlock");
}
function deleteCurrentTicket() {
	var data = new Object();
	var ticket = getTicket();
	data.ticketId = ticket.getId();
	sendCaisseAction("deleteTicket", data);
}

function removeCurrentLine() {
	var data = new Object();
	data.item = new Object();
	data.ticketId = getTicket().getId();
	data.item.rowid = getTicket().getSelectedItem().getRowId();
	sendCaisseAction("deleteItem", data);
}

function showClientTicketList(tickets) {
	if(tickets == null){
		showInfo("Selection Ticket", "Il n'y a pas de tickets pour ce client.");
		return;
	}
	var table = document.createElement("table");
	var trs = new Array();
	var header = "<header><h1>Liste Tickets</h1></header>"
	var thead = "<thead><tr><th>Id</th><th>Numero Ticket</th></thead>"
	var table = document.createElement("table");
	for ( var i = 0; i < tickets.length; i++) {
		onclk = 'onclick="loadTicket(' + tickets[i].id + ', \'' + tickets[i].facnumber + '\');hideSelectionList()"';
		trs.push("<tr " + onclk + "><td>" + tickets[i].id + "</td><td>" + tickets[i].facnumber + "</td></tr>");
	}
	table.innerHTML = thead + trs.join("");

	showList(header, table);
}
function showClientList(clients) {
	var table = document.createElement("table");
	var trs = new Array();
	var header = "<header><h1>Liste Clients</h1></header>"
	var thead = "<thead><tr><th>Id</th><th>Nom</th></thead>"
	var table = document.createElement("table");
	for ( var i = 0; i < clients.length; i++) {
		onclk = 'onclick="getCaisse().setClient(' + clients[i].id + ',' + "'" + clients[i].name + "'" + ');hideSelectionList()"';
		trs.push("<tr " + onclk + "><td>" + clients[i].id + "</td><td>" + clients[i].name + "</td></tr>");
	}
	table.innerHTML = thead + trs.join("");

	showList(header, table);

}

function showProductList(products) {
	var table = document.createElement("table");
	var trs = new Array();
	var header = "<header><h1>Liste Produits</h1></header>"
	var thead = "<thead><tr><th>Reference</th><th>Libelle</th></thead>"
	var table = document.createElement("table");
	for ( var i = 0; i < products.length; i++) {
		onclk = 'onclick="getItem(' + products[i].rowid + ');hideSelectionList()"';
		trs.push("<tr " + onclk + "><td>" + products[i].ref + "</td><td>" + products[i].label + "</td></tr>");
	}
	table.innerHTML = thead + trs.join("");

	showList(header, table);
}

function listCancelBtn() {
	return '<a onclick="hideSelectionList()" class="awesome large red listcancel">Annuler</a>';
}

function boxCancelBtn() {
	return '<a onclick="hideMessageBox()" class="awesome large grey listcancel">Confirmer</a>';
}

function loginBtns() {
	return '<a onclick="login()" class="awesome large grey login">Entrer</a>' + '<a onclick="hideLoginBox()" class="awesome large red login">Annuler</a>';

}

function pay() {
	var data = new Object();
	data.ticketId = getTicket().getId();
	
	data.cash = document.querySelector("#reglement_cash") ? document.querySelector("#reglement_cash").value : 0;
	data.cb = document.querySelector("#reglement_cb") ? document.querySelector("#reglement_cb").value : 0;
	
	var payments = new Array("virement","prelevement","cheque","tip");
	for(var i = 0; i < payments.length; i++){
		
		if( document.querySelector("#reglement_"+payments[i]) ){
			data[payments[i]] =  document.querySelector("#reglement_"+payments[i]).value;
			data[payments[i]+"_banque"] = document.querySelector("#reglement_"+payments[i]+"_banque").value;
			data[payments[i]+"_emetteur"] = document.querySelector("#reglement_"+payments[i]+"_emetteur").value;
			data[payments[i]+"_num"] = document.querySelector("#reglement_"+payments[i]+"_num").value;
		}
	}
	
	var valid = validatePay();
	if (valid == 1) {
		sendCaisseAction("pay", data);
	}
	if (valid == -1) {
		showMessage("Validation Encaissement", "La somme des payments est superieur au montant de la facture");
	}
	if (valid == -2) {
		showMessage("Validation Encaissement", "Plafond atteint ou depasse. La totalite de cette facture doit etre reglee afin d'etre validee");
	}
}

function validatePay() {
	var unpaid = parseFloat(getTicket().getAmountUnPaid());
	var warning = getCaisse().getWarning();
	var depassement = getCaisse().getDepassement();
	var inputs = document.querySelectorAll("input.reglement");
	var total = 0;
	for ( var i = 0; i < inputs.length; i++) {
		if (inputs[i].value > 0) {
			total += parseFloat(inputs[i].value);
		}
	}

	var valid = 1;
	if (unpaid < total) {
		valid = -1;
	}

	if (warning && total < unpaid && !depassement) {
		valid = -2;
	}
	return valid;
}

function newTicket() {
	var clientId = getCaisse().getClientId();
	if (clientId < 0) {
		showInfo("Chargement Ticket", "Client indefini");
		return;
	}
	var data = new Object();
	data.clientId = clientId;
	sendCaisseAction("newTicket", data);
}
function loadTicket(ticketId, facnumber) {
	var data = new Object();
	data.ticketId = ticketId;
	data.facnumber = facnumber;
	sendCaisseAction("loadTicket", data);
}

function removeObjectFunctions(obj) {
	var j = new Object();
	for ( var i in obj) {
		if (!jQuery.isFunction(obj[i])) {
			if (jQuery.isArray(obj[i]) || typeof (obj[i]) == "object") {
				j[i] = removeObjectFunctions(obj[i]);
			} else {
				if (obj[i] != null) {
					j[i] = obj[i];
				}
			}
		}
	}
	return j;
}

function numToMaxDecimals(n, ndec) {
	ndec = parseInt(ndec);
	var s = n.toLocaleString();
	var dotPos = s.lastIndexOf(".");
	if (s.length != dotPos + 1) {
		var dec = s.split(".")[1];
		if (dec && dec.length > ndec) {
			n = parseFloat(n).toFixed(ndec);
		}
	}
	return n;
}

// =============================
// =========== AJAX ============
// =============================

function sendCaisseAction(action, data) {

	data.action = action;
	if (action) {

		$.ajax( {
			type : 'POST',
			url : 'dolibarrCaisse_ajax.php',
			dataType : 'json',
			data : data,
			success : function(data) {
				dispatchServerResponse(data);
			},
			error : function(XMLHttpRequest, textStatus, errorThrown) {

				showError("Server Error", XMLHttpRequest.status);
				hideLoader();
			}
		});
		showLoader();
	}
}

function dispatchServerResponse(data) {
	hideLoader();

	if (data.action == "loginRequired") {
		showLogin();
	} else if (data.action == "login") {
		getCaisse().setSeller(data.userData.rowid, data.userData.name);
		//set default cashdesk client
		getCaisse().setClient( data.defaultClient.id ,  data.defaultClient.name );
		
	} else if (data.action == "logout") {
		if (data.status == "ok") {
			getCaisse().setSeller(-1, "");
			getTicket().clear();
			hideMessageBox();
			hideSelectionList();
			showLogin();
		}
	} else if (data.action == "newTicket") {
		if (data.error) {
			showError("Ticket", "Probleme de creation de ticket");
		}
		var t = getTicket();
		t.clear();
		t.setId(data.ticketId, data.ticketNumber);

	} else if (data.action == "getClientList") {
		showClientList(data.clients);

	} else if (data.action == "getClientTickets") {
		showClientTicketList(data.tickets);

	} else if (data.action == "saveItem") {
		getTicket().saveSuccessful(data.itemStatus, data.item, data.saveAction);

	} else if (data.action == "getProducts") {
		showProductList(data.items);

	} else if (data.action == "loadTicket") {
		var tItems = new Array();
		for ( var i = 0; i < data.items.length; i++) {
			var item = data.items[i];
			var p = item.price_ht;
			var tItem = newTicketItem();
			tItem.loadFromJson(item);
			tItems.push(tItem);
		}
		getTicket().clear();
		getTicket().setId(data.ticketId, data.facnumber);
		getTicket().populateTicket(tItems);

	} else if (data.action == "getItem") {
		var newItem = newTicketItem();
		newItem.loadFromJson(data.item);
		getTicket().freeEntryForm(newItem);

	} else if (data.action == "deleteItem") {
		if (data.itemStatus == "ok") {
			getTicket().deleteItem(data.item.rowid);
		} else {
			showError("Suppression Ligne", data.error);
		}
	} else if (data.action == "deleteTicket") {
		if (data.ticketStatus == "ok") {
			var ticket = getTicket();
			ticket.cancel();
			ticket.setId('', '');
			ticket.clear();
		} else {
			showError("Suppression Ticket", data.error);
		}
	} else if (data.action == "getEncaissementInfo") {
		if (data.ticketStatus == "ok") {
			showEncaissementBox(data);
		} else {
			showError("Encaissement", data.error);
		}
	} else if (data.action == "pay") {
		if (data.ticketStatus == "ok") {
			hideEncaissementBox();
			showInfo("Encaissement", "La facture " + data.ticketNumber + " a ete validee");
			getTicket().setId(data.ticketId, data.ticketNumber);
		} else {
			showError("Payment Ticket", data.error);
		}
	}
}
