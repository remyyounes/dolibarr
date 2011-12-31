// =========================
// ======== TICKET =========
// =========================

function TableTicket()
{

	// Initialization
	this.init = function(ticket)
	{
		this.ticket = ticket;
		this.ticket.init();
		this.formHolder = document.querySelector("#ticket_input tbody");
		this.formInfoHolder = document.querySelector("#ticket_input_info tbody");
		this.itemTable = document.querySelector("#ticket_list");
		this.ticketScroller = document.querySelector("#ticket_scroller");

	}, this.edit = function(rowid)
	{
		this.ticket.edit(rowid);
		this.formHolder.innerHTML = this.ticket.getItem(rowid).editForm();
		this.showFormInfo();
		this.formInfoHolder.innerHTML = this.ticket.getItem(rowid).editInfo();
		this.ticket.getItem(rowid).refreshFields();

	}, this.save = function()
	{
		if (!loading) {
			var result = this.ticket.save();
		}

	}, this.saveSuccessful = function(status, updatedItem, action)
	{
		var item = this.ticket.editing;
		if (status == "ok") {
			this.cancel();
			item.loadFromJson(updatedItem);
			if (action == "create") {
				this.append(item);
			} else if (action == "update") {
				this.updateRow(item);
			}
			this.scrollToItem(item);
			this.displayTotal();
		} else {
			showError("Erreur Ticket", status);
		}

	}, this.displayTotal = function()
	{
		var total = this.ticket.getTotal();
		var totalDisplay = document.querySelector("#total");
		totalDisplay.innerHTML = total;

	}, this.cancel = function()
	{
		if (!loading) {
			this.ticket.cancel();
			this.formHolder.innerHTML = this.printDefaultForm();
			this.hideFormInfo();
		}

	}, this.updateRow = function(item)
	{
		this.itemTable.replaceChild(item.printRow(), item.getTableRow());

	}, this.append = function(item)
	{
		this.ticket.append(item);
		this.itemTable.appendChild(item.printRow());

	}, this.scrollToItem = function(item)
	{
		this.ticketScroller.scrollTop = item.getTableRow().offsetTop;

	}, this.hideFormInfo = function()
	{
		this.formInfoHolder.classList.add("hideBlock");

	}, this.showFormInfo = function()
	{
		this.formInfoHolder.classList.remove("hideBlock");

	}, this.printDefaultForm = function()
	{
		var oninpt = ' onkeypress="checkGetProducts(event);" ';
		var form = "";
		form += '<tr>';
		form += '	<td colspan="6" class="search">';
		form += '		<input id="productQuery_label" placeholder="Libelle" type="search" autofocus ' + oninpt + ' >';
		form += '		<input id="productQuery_ref"  placeholder="Reference" type="search" ' + oninpt + ' >';
		form += '		<input id="productQuery_barcode"  placeholder="Code Barre"  type="search" ' + oninpt + ' >';
		form += '		<br>';
		form += '	<a class="awesome grey" onclick="getProducts();" style="width:150px">Chercher</a>';
		form += '	<a onclick="getTicket().freeEntryForm();" class="awesome blue" style="width:150px">Entree Libre</a>';
		form += '	</td>';
		form += '</tr>';
		return form;

	}, this.freeEntryForm = function(item)
	{
		if (item) {

			var freeItem = item;

		} else {
			var freeItem = newTicketItem();
		}

		this.formHolder.innerHTML = freeItem.editForm();
		this.ticket.items[freeItem.getRowId()] = freeItem;
		this.ticket.editing = freeItem;
		freeItem.refreshFields();
		if (item) {
			this.showFormInfo();
			this.formInfoHolder.innerHTML = this.ticket.editing.editInfo();
		}

	}, this.updateItemFields = function(field, extra)
	{
		this.ticket.updateItemFields(field, extra);
		this.ticket.editing.refreshFields();

	}, this.clear = function()
	{
		this.itemTable.innerHTML = '';
		this.ticket.clear();
		this.displayTotal();

	}, this.setId = function(id, facnumber)
	{
		this.ticket.setId(id);
		document.querySelector("#ticketId").innerHTML = facnumber;

	}, this.getId = function()
	{
		return this.ticket.getId();

	}, this.loadTicket = function(ticketId)
	{
		this.ticket.loadTicket(ticketId);

	}, this.populateTicket = function(items)
	{
		for ( var i = 0; i < items.length; i++) {
			var item = items[i];
			this.append(item);
		}
		this.displayTotal();
	}, this.containsProduct = function(productId)
	{
		return this.ticket.containsProduct(productId);

	}, this.isCumulable = function()
	{
		return this.ticket.isCumulable();

	}, this.setCumulable = function(cumulable)
	{
		this.ticket.setCumulable(cumulable);

	}, this.getSelectedItem = function()
	{
		return this.ticket.getSelectedItem();

	}, this.deleteItem = function(rowId)
	{
		var itemToDelete = this.ticket.getItem(rowId);
		if (this.getSelectedItem() == itemToDelete) {
			this.cancel();
		}
		this.itemTable.removeChild(this.ticket.getItem(rowId).getTableRow());
		this.ticket.deleteItem(rowId);

	}, this.setAmountUnPaid = function(unpaid)
	{
		this.ticket.setAmountUnPaid(unpaid);

	}, this.getAmountUnPaid = function()
	{
		return this.ticket.getAmountUnPaid(); 

	}
}

// =========================
// ====== HELPER FUNCS =====
// =========================
function newTicketItem()
{
	var ttItem = new TableTicketItem();
	ttItem.init(new TicketItem());
	return ttItem;
}

// =========================
// ====== TICKET ITEM ======
// =========================

function TableTicketItem()
{
	this.init = function(ticketItem)
	{
		this.ticketItem = ticketItem;
		this.ticketItem.init();
		this.initForm();

	}, this.initForm = function()
	{

		// labels
		this.labels = new Array();
		this.labels['libelle'] = "Libelle";
		this.labels['subprice'] = "Px HT";
		this.labels['qty'] = "Qte";
		this.labels['remise_percent'] = "%";
		this.labels['total_ht'] = "Total";
		this.labels['productSearch'] = "Libelle, Reference, CodeBarre";
		this.labels['qtyc'] = "Colisage";
		this.labels['qtyu'] = "Unite";
		// inputTypes
		this.inputOptions = new Array();
		this.defaultInputOptions = ' type="number" min="0" ';
		this.inputOptions['libelle'] = ' type="search" autofocus ';
		this.inputOptions['subprice'] = this.defaultInputOptions;
		this.inputOptions['qty'] = this.defaultInputOptions;
		this.inputOptions['remise_percent'] = ' type="number" min="0" step="1" ';
		this.inputOptions['total_ht'] = this.defaultInputOptions;

		// Disabled Fields
		this.disabledInputs = new Array("total_ht");

		// CSS style
		this.editClass = "edit";

	}, this.edit = function()
	{
		this.ticketItem.edit();
		if (this.getTableRow()) {
			this.getTableRow().classList.add(this.editClass);
		}

	}, this.cancelEdit = function()
	{
		this.ticketItem.cancelEdit();
		if (this.getTableRow()) {
			this.getTableRow().classList.remove(this.editClass);
		}

	}, this.printRow = function()
	{
		var fields = new Array();

		fields.push(this.showField("libelle"));
		fields.push(this.showExtraFields());
		fields.push(this.showField("subprice"));
		fields.push(this.showField("qty"));
		fields.push(this.showField("remise_percent"));
		fields.push(this.showField("total_ht"));

		// create Dom TR element
		var row = document.createElement("tr");
		var t = getTicket();
		var id = this.getRowId();
		row.onclick = function()
		{
			t.edit(id)
		};
		row.innerHTML = fields.join("");
		row.id = "rowid_" + this.getRowId();
		return row;

	}, this.createForm = function()
	{
		this.ticketItem.edit();
		var emptyTd = "<td></td>";
		var fields = new Array();

		this.checkLabelsAvailability();

		// create row
		fields.push(this.showInput("libelle"));
		fields.push(emptyTd);
		fields.push(this.showInput("subprice"));
		fields.push(this.showInput("qty"));
		fields.push(emptyTd);
		fields.push(emptyTd);
		fields.push(this.showInput("total_ht"));

		// print row/form
		var form = "<tr>" + fields.join("") + "</tr>";
		return form;

	}, this.editForm = function()
	{
		var fields = new Array();
		this.checkLabelsAvailability();
		// create row
		fields.push(this.showField("libelle"));
		fields.push(this.showExtraInputs());
		fields.push(this.showInput("subprice"));
		fields.push(this.showInput("qty"));
		fields.push(this.showInput("remise_percent"));
		fields.push(this.showInput("total_ht"));

		// print row/form
		var form = "<tr>" + fields.join("") + "</tr>";
		return form;

	}, this.editInfo = function()
	{

		var fields = new Array();
		var rows = new Array();
		var emptyField = "<td></td><td></td>";
		// create row

		var uniteVente = this.ticketItem.info.unite_vente;
		var stck = this.ticketItem.info.stck;
		var stcktheo = this.ticketItem.info.stcktheo;
		var public_price = this.ticketItem.info.public_price;
		var colisage = this.ticketItem.info.colisage;
		var discount_origin = this.ticketItem.extra.discount_origin;
		var discount_type = this.ticketItem.extra.discount_type;
		var discount_limit = this.ticketItem.extra.discount_limit;
		var discount_qtymin = this.ticketItem.extra.discount_qtymin

		if (!uniteVente) {
			uniteVente = "";
		}
		if (!stck) {
			stck = "";
		}
		if (!stcktheo) {
			stcktheo = "";
		}
		if (!public_price) {
			public_price = "";
		}
		if (!discount_origin) {
			discount_origin = "";
		}
		if (!discount_type) {
			discount_type = "";
		}
		if (!discount_qtymin) {
			discount_qtymin = "";
		}
		if (!discount_limit) {
			discount_limit = "";
		}

		fields.push("<td>Unite Vente:</td><td>" + uniteVente + "</td>");
		fields.push("<td>Stock:</td><td>" + stck + "</td>");
		fields.push("<td>Prix Public:</td><td>" + public_price + "</td>");
		rows.push("<tr>" + fields.join("") + "</tr>");

		fields = new Array();
		fields.push("<td>Colisage:</td><td>" + colisage + "</td>");
		fields.push("<td>Stock Theorique:</td><td>" + stcktheo + "</td>");
		fields.push(emptyField);
		rows.push("<tr>" + fields.join("") + "</tr>");

		fields = new Array();
		fields.push("<td>Promo:</td><td>" + discount_origin + "</td>");
		fields.push("<td>Qte.Min Promo :</td><td>" + discount_qtymin + "</td>");
		fields.push("<td>Limite Promo:</td><td>" + discount_limit + "</td>");
		rows.push("<tr>" + fields.join("") + "</tr>");

		// print row/form
		var form = rows.join("");
		return form;

	}, this.checkLabelsAvailability = function()
	{
		if (this.getColisage() || this.usesSpecialUnits()) {
			this.disableInput("qty");
		} else {
			this.enableInput("qty");
		}

	}, this.showField = function(field)
	{
		var v = this.ticketItem[field];
		var out = (!v) ? 0 : v;
		if (field == "libelle") {
			out = "<div class='extra'>" + this.ticketItem['product_ref'] + "</div> " + out;
		} else if (field == "total_ht" || field == "subprice") {
			out = parseFloat(v).toFixed(2);
		}
		return "<td>" + out + "</td>";

	}, this.showExtraFields = function()
	{
		var out = "";
		if (this.usesSpecialUnits()) {
			for ( var i = 0; i < this.ticketItem.unit.unitLabels.length; i++) {
				var f = this.ticketItem.unit.unitLabels[i];
				var v = this.ticketItem.extra["dim" + (i + 1)];
				v = (!v) ? 0 : v;
				out += '<div class="extra">';
				out += '<span class="info_header">' + f + '</span>' + v + '</div>';
			}
		} else if (this.getColisage()) {
			var c = this.getNumColis();
			var u = this.getNumUnites();
			c = (!c) ? 0 : c;
			u = (!u) ? 0 : u;
			out += '<div class="extra">';
			out += '<span class="info_header">' + "Colis" + '</span>' + c + '</div>';
			out += '<div class="extra">';
			out += '<span class="info_header">' + "Unites" + '</span>' + u + '</div>';
		}
		return "<td>" + out + "</td>";

	}, this.showExtraInputs = function()
	{
		var out = "";
		if (this.usesSpecialUnits()) {
			for ( var i = 0; i < this.ticketItem.unit.unitLabels.length; i++) {
				var f = "dim" + (i + 1);
				var v = this.ticketItem.extra[f];
				out += this.showInput(f);
			}
		} else if (this.getColisage()) {
			var v = this.getNumColis();
			out += this.showInput("qtyc")
			out += this.showInput("qtyu");
		}
		return "<td>" + out + "</td>";

	}, this.showInput = function(field)
	{
		// get correct value
		var value = '';
		if (extra = this.isExtraField(field)) {
			value = this.ticketItem.extra[field];
		} else {
			if (field == "qtyc" || field == "qtyu") {
				extra = true;
			}
			value = this.ticketItem[field];
		}

		// =====================
		// ===== IMPORTANT =====
		// =====================
		// Due to a Chromium bug, setting values with <input value="x">
		// will result in the input.value being reset to x if we try to empty it
		// Instead, values are initialized by TableTicketItem.refreshFields()
		value = (value == undefined) ? '' : value;
		// For now we set value to the empty string
		value = '';
		// =====================

		// Input Data
		var fieldName = ' name="' + field + '" ';
		var isDisabled = (this.isInputEnabled(field)) ? '' : ' disabled ';
		var hide = (value || value === 0) ? ' class = "hide"' : '';
		var extra = (extra) ? ", 'extra'" : '';
		var oninput = ' oninput="getTicket().updateItemFields(this' + extra + ');"  ';
		var onblur = ' onblur="getTicket().updateItemFields(this' + extra + ');"  ';
		var onKeyPress = ' onkeypress="checkSave(event);" ';

		var autofocus = (field == "dim1" || field == "qtyc") ? " autofocus " : "";
		autofocus = (field == "subprice" && !this.usesSpecialUnits() && !this.getColisage()) ? " autofocus" : "";
		// print
		var out = "";
		if (field == 'total_ht') {
			out = this.showInputButtons() + out;
		}
		out += '<div>';
		out += '<label' + hide + '>' + this.getFieldLabel(field) + '</label>';
		out += '<input ' + this.getInputOptions(field) + fieldName + isDisabled + onKeyPress + oninput + onblur + autofocus + ' value="'
				+ value + '"  />';
		out += '</div>';
		if (!extra) {
			out = "<td>" + out + "</td>"
		}
		return out;

	}, this.getFieldLabel = function(field)
	{
		if ((extraPos = this.isExtraField(field))) {
			return this.ticketItem.unit.unitLabels[extraPos - 1];
		}
		var l = this.labels[field];
		if (!l) {
			l = field;
		}
		return l;

	}, this.getInputOptions = function(field)
	{
		var t = this.inputOptions[field];
		if (!t) {
			t = this.defaultInputOptions;
		}
		return t;

	}, this.showInputButtons = function()
	{
		var out = "";
		out += '<a onclick="getTicket().save();" class="awesome confirm blue">OK</a>';
		out += '<a onclick="getTicket().cancel();" class="awesome confirm red cancel">C</a>';
		return out;

	}, this.getTableRow = function()
	{
		return document.querySelector("#rowid_" + this.getRowId());

	}, this.updateFields = function(f, extraField)
	{
		this.ticketItem.updateFields(f, extraField);
		this.refreshFields();

	}, this.refreshFields = function()
	{
		var formInputs = document.querySelectorAll("#ticket_input tbody input");
		for ( var i = 0; i < formInputs.length; i++) {
			var inpt = formInputs[i];
			var n = inpt.name;
			if (this.ticketItem[n] != undefined) {
				inpt.value = this.ticketItem[n];
			} else {
				// extra
				inpt.value = this.ticketItem.extra[n];
			}
			this.checkLabel(inpt);
		}

	}, this.checkLabel = function(inpt)
	{
		var l = inpt.previousSibling;
		if (l.nodeName == "LABEL") {
			if (inpt.value != "") {
				l.classList.add("hide");
			} else {
				l.classList.remove("hide");
			}
		}

	}, this.getRowId = function()
	{
		return this.ticketItem.getRowId();

	}, this.setRowId = function(id)
	{
		return this.ticketItem.setRowId(id);

	}, this.isInputEnabled = function(field)
	{
		return !this.disabledInputs.contains(field);

	}, this.disableInput = function(field)
	{
		if (this.isInputEnabled(field)) {
			this.disabledInputs.push(field);
		}

	}, this.enableInput = function(field)
	{
		this.disabledInputs.remove(field);

	}, this.isExtraField = function(field)
	{
		return this.ticketItem.isExtraField(field);

	}, this.loadFromJson = function(data)
	{
		this.ticketItem.loadFromJson(data);

	}, this.usesSpecialUnits = function()
	{
		return this.ticketItem.usesSpecialUnits();

	}, this.toCleanJson = function()
	{
		return this.ticketItem.toCleanJson();

	}, this.getProductId = function()
	{
		return this.ticketItem.getProductId();

	}, this.getColisage = function()
	{
		return this.ticketItem.getColisage();

	}, this.getNumColis = function()
	{
		return this.ticketItem.getNumColis();

	}, this.getNumUnites = function()
	{
		return this.ticketItem.getNumUnites();

	}
}