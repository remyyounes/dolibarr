// =========================
// ======== TICKET =========
// =========================

function Ticket()
{

	// Initialization
	this.init = function()
	{

		// STRUCTURE
		this.items = new Array();
		this.formHolder = document.querySelector("#ticket_input tbody");
		this.itemTable = document.querySelector("#ticket_list");
		this.forms = new Array();
		this.editing;

		// CSS style
		this.rowClass = "alternateRow";
		this.formLabelClass = "inline_label";

	}, this.edit = function(rowid)
	{
		var item = this.items[rowid];
		this.formHolder.innerHTML = item.edit();
		this.editing = item;

	}, this.save = function()
	{
		alert("saving");

	}, this.cancel = function()
	{
		if (this.editing) {
			this.editing.cancelEdit();
		}
		this.ediding = '';
		this.formHolder.innerHTML = this.printDefaultForm();

	}, this.append = function(item)
	{
		this.itemTable.innerHTML += item.printRow();
		this.items[item.rowid] = item;

	}, this.printDefaultForm = function()
	{
		var form = "";
		form += '<tr class="alternateRow">';
		form += '	<td colspan="7" class="search">';
		form += '		<input type="search" autofocus>';
		form += '	<a class="awesome grey">Chercher</a>';
		form += '	<a onclick="getTicket().freeEntryForm();" class="awesome blue">Entree Libre</a>';
		form += '	</td>';
		form += '</tr>';
		return form;

	}, this.freeEntryForm = function()
	{
		var freeItem = new TicketItem();
		freeItem.init();
		freeItem.prodId = -1;
		freeItem.rowid = -1;
		this.formHolder.innerHTML = freeItem.createForm();
		this.items[freeItem.rowid] = freeItem;
		this.editing = freeItem;

	}
}

// =========================
// ====== TICKET ITEM ======
// =========================

function TicketItem()
{
	this.init = function()
	{
		this.initForm();
		this.libelle = "Unlabelled";
		this.prodId;
		this.qte = 1;
		this.ht;
		this.tax;
		this.remise;
		this.remisePct;
		this.extra = new Array();
		this.extra.push(new Array("Longueur", ''));
		this.extra.push(new Array("Largeur", ''));
		this.extra.push(new Array("epaisseur", ''));
		this.prixRemise;
		this.total;
		this.rowid = 1;
		this.fmcal = "#{" + "Longueur * Largeur * Epaisseur" + "}";
	}, this.initForm = function()
	{

		// labels
		this.labels = new Array();
		this.labels['libelle'] = "Libelle";
		this.labels['ht'] = "Px HT";
		this.labels['qte'] = "Qte";
		this.labels['rempct'] = "%";
		this.labels['rem'] = "Rem";
		this.labels['total'] = "Total";
		this.labels['productSearch'] = "Libelle, Reference, CodeBarre";

		// inputTypes
		this.inputTypes = new Array();
		this.inputTypes['libelle'] = "search";
		this.inputTypes['ht'] = "number";
		this.inputTypes['qte'] = "number";
		this.inputTypes['rempct'] = "number";
		this.inputTypes['rem'] = "number";
		this.inputTypes['total'] = "number";
		this.defaultInputType = "number";

		// CSS style
		this.rowClass = "alternateRow";
		this.formLabelClass = "inline_label";
	}, this.edit = function()
	{
		this.getTableRow().classList.add("edit");
		return this.editForm();

	}, this.cancelEdit = function()
	{
		this.getTableRow().classList.remove("edit");

	}, this.printRow = function()
	{
		var options = new Array();
		var fields = new Array();
		options['type'] = 'text';

		fields.push(this.showField("libelle", options));
		fields.push(this.showField("extra", options));
		fields.push(this.showField("ht", options));
		fields.push(this.showField("qte", options));
		fields.push(this.showField("rempct", options));
		fields.push(this.showField("rem", options));
		fields.push(this.showField("total", options));

		var row = "<tr onclick='getTicket().edit(" + this.rowid + ");' id='rowid_" + this.rowid + "'>" + fields.join("") + "</tr>";
		return row;

	}, this.createForm = function()
	{
		var options = new Array();
		var emptyTd = "<td></td>";
		options['autofocus'] = 'autofocus';
		options['disabled'] = '';
		options['min'] = '';
		options['btns'] = '';
		var fields = new Array();

		options['type'] = 'input';
		fields.push(this.showField("libelle", options));
		options['min'] = ' min = "0" ';
		fields.push(emptyTd);
		fields.push(this.showField("ht", options));
		fields.push(this.showField("qte", options));
		fields.push(emptyTd);
		fields.push(emptyTd);
		options['disabled'] = 'disabled';
		options['btns'] = this.showInputButtons();
		fields.push(this.showField("total", options));

		var form = "<tr class='" + this.rowClass + "'>" + fields.join("") + "</tr>";
		return form;

	}, this.editForm = function()
	{
		var options = new Array();
		options['autofocus'] = 'autofocus';
		options['disabled'] = '';
		options['min'] = '';
		options['btns'] = '';
		var fields = new Array();

		options['type'] = 'text';
		fields.push(this.showField("libelle", options));
		options['type'] = 'input';
		options['min'] = ' min = "0" ';

		fields.push(this.showField("extra", options));
		options['autofocus'] = '';
		fields.push(this.showField("ht", options));
		fields.push(this.showField("qte", options));
		fields.push(this.showField("rempct", options));
		fields.push(this.showField("rem", options));
		options['disabled'] = 'disabled';
		options['btns'] = this.showInputButtons();
		fields.push(this.showField("total", options));

		var form = "<tr class='" + this.rowClass + "'>" + fields.join("") + "</tr>";
		return form;
	}, this.showField = function(field, options)
	{
		// extra content needs to be treated slightly differently
		if (field == "extra") {
			return this.showExtraFields(options);
		}

		var out = "";
		var placeMiddle = "";
		if (options['type'] == "text") {
			out += this[field];
			placeMiddle = " class='middle' ";

		} else if (options['type'] == "input") {
			out += this.showInput(field, this[field], options);
		}

		if (options['btns']) {
			out = "<td class='confirm'>" + options['btns'] + out + "</td>";
		} else {
			out = "<td" + placeMiddle + ">" + out + "</td>";
		}
		return out;

	}, this.showExtraFields = function(options)
	{

		var out = "";
		if (options['type'] == "text") {
			for ( var i = 0; i < this.extra.length; i++) {
				var f = this.extra[i][0];
				var v = this.extra[i][1];

				out += '<div class="extra">';
				out += '<span class="info_header">' + f + '</span>' + v + '</div>';
			}
			out = "<td>" + out + "</td>";

		} else if (options['type'] == "input") {
			options['extra'] = "extra";
			for ( var i = 0; i < this.extra.length; i++) {
				
				var f = this.extra[i][0];
				var v = this.extra[i][1];

				out += this.showInput(f, v, options);
			}
			options['extra'] = "";
			out = "<td class='extra_input'>" + out + "</td>";
		}
		return out;

	},
	// if autofocus is set it will be unset on return
	this.showInput = function(field, value, options)
	{

		var hide = '';
		if (value) {
			hide = ' class = "hide"';
		}

		var extra = '';
		if (options['extra']) {
			extra = ", 'extra'";
		}
		out = '<div class="' + this.formLabelClass + '">';
		out += '<label' + hide + '>' + this.getFieldLabel(field) + '</label>';
		out += '<input type="' + this.getInputType(field) + '" value="' + value + '" ' + options['autofocus'] + ' ' + options['disabled']
				+ ' ' + options['min'] + ' name="' + field + '"  oninput="getTicket().editing.updateFields(this' + extra + ');" />';
		out += '</div>';
		if (options['autofocus']) {
			options['autofocus'] = '';
		}
		return out;

	}, this.getFieldLabel = function(field)
	{
		var l = this.labels[field];
		if (!l) {
			l = field;
		}
		return l;

	}, this.getInputType = function(field)
	{
		var t = this.inputTypes[field];
		if (!t) {
			t = this.defaultInputType;
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
		var tr = document.querySelector("#rowid_" + this.rowid);
		return tr;
	}, this.updateFields = function(f, extraField)
	{
		if (extraField) {
			for ( var i in this.extra) {
				if (this.extra[i][0] == f.name) {
					this.extra[i][1] = f.value;
				}
			}
			this.updateQuantite();
		} else {
			this[f.name] = f.value;
		}

		if (f.name == "rem") {
			this.updateRemisePct();
		}

		if (f.name == "rempct") {
			this.updateRemise();
		}

		if (f.name == "ht") {
			this.updatePrixRemise();
		}

		// checkRemise
		if (this.prixRemise < 0) {
			this.setRemiseMax();
		}
		this.updateTotal();
		this.refreshFields();

	}, this.setRemiseMax = function()
	{
		if (this.ht > 0) {
			this.rem = this.ht;
			this.updateRemisePct();
		} else {
			this.rem = 0;
			this.rempct = 0;
			this.prixRemise = 0;
		}
		this.updateTotal();

	}, this.updateRemise = function()
	{
		if (this.rempct) {
			this.rem = this.ht * this.rempct / 100;
		}
		this.updatePrixRemise();

	}, this.updateRemisePct = function()
	{
		if (this.rem) {
			this.rempct = this.rem * 100 / this.ht;
		}
		this.updatePrixRemise();

	}, this.updatePrixRemise = function()
	{
		if (this.rem) {
			this.prixRemise = this.ht - this.rem;
		} else {
			this.prixRemise = this.ht;
		}

	}, this.updateTotal = function()
	{
		if(!this.prixRemise){
			this.prixRemise = this.ht;
		}
		this.total = this.prixRemise * this.qte;

	}, this.updateQuantite = function()
	{
		if (this.fmcal) {
			var mappings = new Object();
			var variables = getVariables(this.fmcal);

			var nbdec = 3;// ('nbdec').value;
			for ( var i = 0; i < variables.length; i++) {
				mappings[variables[i]] = this.extra[i][1];
				// valueFormat($('dim'+(i+1)),nbdec);
			}
			var r = evaluate(this.fmcal, mappings);
			this.qte = r;
		}
		
	}, this.refreshFields = function()
	{
		var formInputs = document.querySelectorAll("#ticket_input tbody input");
		for ( var i = 0; i < formInputs.length; i++) {
			var inpt = formInputs[i];
			var n = inpt.name;
			if (this[n]) {
				inpt.value = this[n];
			} else {
				// extra
				this.refreshExtraField(inpt);
			}
			this.checkLabel(inpt);
		}

	}, this.refreshExtraField = function(inpt)
	{
		for ( var i = 0; i < this.extra.length; i++) {
			var ex = this.extra[i];
			if ( ex[0] == inpt.name){
				this.extra[i][1] = inpt.value;
			}
		}

	}, this.checkLabel = function(i)
	{
		var l = i.previousSibling;
		if (l.nodeName == "LABEL") {
			if (i.value != "") {
				l.classList.add("hide");
			} else {
				l.classList.remove("hide");
			}
		}
	}
}