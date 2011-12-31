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
		this.forms = new Array();
		this.editing;
		this.numItems = 0;
		this.ticketId = -1;
		this.cumulable = 1;
		this.backedUpItem;
	}, this.edit = function(rowid)
	{
		this.cancel();
		var item = this.getItem(rowid);
		item.edit();
		this.editing = item;
		this.backedUpItem = this.editing.toCleanJson();

	}, this.save = function()
	{
		saveItem(this.ticketId, this.editing.toCleanJson());

	}, this.cancel = function()
	{
		if (this.editing) {
			this.editing.cancelEdit();
			if (this.editing.getRowId() > 0) {
				this.editing.loadFromJson(this.backedUpItem);
			}
			this.editing = '';
		}

	}, this.getItem = function(rowid)
	{
		return this.items[rowid];

	}, this.getItems = function()
	{
		return this.items;

	}, this.deleteItem = function(rowid)
	{
		this.items[rowid] = null;
		this.numItems--;

	}, this.append = function(item)
	{
		if (item.getRowId() < 1) {
			if (this.numItems == 0) {
				item.setRowId(1);
			} else {
				item.setRowId(this.items.length);
			}
		}
		this.items[item.getRowId()] = item;
		this.numItems++;

	}, this.updateItemFields = function(field, extra)
	{
		var item = this.editing;
		item.updateFields(field, extra);

	}, this.clear = function()
	{
		this.items = new Array();
		this.numItems = 0;

	}, this.setId = function(id)
	{
		this.ticketId = id;

	}, this.getId = function()
	{
		return this.ticketId;

	}, this.loadTicket = function(ticketId)
	{
		var data = new Object();
		data.ticketId = ticketId;
		sendCaisseAction("loadTicket", data);

	}, this.containsProduct = function(productId)
	{
		var items = this.getItems();
		var n = items.length;
		for ( var i = 0; i < n; i++) {
			if (items[i] != null && items[i].getProductId() == productId) {
				return i;
			}
		}
		return -1;
	}, this.isCumulable = function()
	{
		return this.cumulable;

	}, this.setCumulable = function(cumulable)
	{
		if (cumulable) {
			this.cumulable = true;
		} else {
			this.cumulable = false;
		}
	}, this.getSelectedItem = function()
	{
		return this.editing;

	}, this.getTotal = function()
	{
		var total = 0;
		var items = this.getItems();
		var n = items.length;
		for ( var i = 0; i < n; i++) {
			if (items[i] != null) {
				total += parseFloat(items[i].ticketItem.total_ht);
			}
		}
		return parseFloat(total).toFixed(2);

	}, this.setAmountUnPaid = function(unpaid)
	{
		this.unpaid = parseFloat(unpaid.replace(" ","").replace(",","."));

	}, this.getAmountUnPaid = function()
	{
		return this.unpaid;

	}
}

// =========================
// ====== TICKET ITEM ======
// =========================

function TicketItem()
{
	this.init = function()
	{
		this.libelle = "Unlabelled";
		this.product_ref;
		this.fk_product = -1;
		this.qty = 1;
		this.subprice;
		this.remisePct;
		this.prixRemise;
		this.total_ht;
		this.rowid = -1
		this.rang;
		this.unit = new Array();
		this.extra = new Array();
		this.info = new Array();

	}, this.edit = function()
	{

	}, this.cancelEdit = function()
	{

	}, this.updateFields = function(f, extraField)
	{
		if (extraField) {
			if (f == "qtyc" || f == "qtyu") {
				this.extra[f.name] = numToMaxDecimals(f.value, 1);
			} else
				this.extra[f.name] = numToMaxDecimals(f.value, this.getExtraDecimals());
			this.updateQuantite();
		} else {

			var val = numToMaxDecimals(f.value, 2);

			if (f.name == "qty") {
				this.setQuantite(val);
			}
			if (f.name == "remise_percent") {
				this.setRemiseManuelle();
				this.setRemisePct(val);
			}
			if (f.name == "subprice") {
				this.setRemiseManuelle();
				this.setPrice(val);
			}
			// checkRemise
			if (this.remise_percent > 100) {
				this.setRemiseMax();
			} else {
				// remaining "zero" fix
				this[f.name] = val;
			}

		}
		this.updateTotal();

	}, this.setPrice = function(price)
	{
		price = numToMaxDecimals(price, 2);
		if (price > 0) {
			this.subprice = price;
		} else {
			this.subprice = 0;
		}
		// refresh discount
		this.setRemisePct(this.remise_percent);

	}, this.setRemiseMax = function()
	{
		if (this.subprice > 0) {
			this.setRemisePct(100);
		} else {
			this.setRemisePct(0);
		}

	}, this.setRemisePct = function(remisePct)
	{
		remisePct = numToMaxDecimals(remisePct, 2);
		if (remisePct > 0) {
			this.remise_percent = remisePct;
			this.prixRemise = this.subprice * (1 - this.remise_percent / 100);
		} else {
			this.remise_percent = 0;
			this.prixRemise = this.subprice;
		}

	}, this.setQuantite = function(quantite)
	{
		quantite = numToMaxDecimals(quantite, 2);
		this.qty = quantite ? quantite : 0;

	}, this.updateTotal = function()
	{
		this.total_ht = numToMaxDecimals(this.subprice * this.qty * (1 - this.remise_percent / 100), 2);

	}, this.updateQuantite = function()
	{
		if (this.unit.numUnits) {
			var mappings = new Object();
			for ( var i = 0; i < this.unit.unitLabels.length; i++) {
				mappings[this.unit.unitLabels[i]] = this.extra["dim" + (i + 1)];
			}
			var r = evaluate(this.unit.fmcalJS, mappings);
			this.qty = numToMaxDecimals(r, this.getExtraDecimals());
		} else if (this.getColisage()) {
			var qtyc = this.getNumColis();
			var qtyu = this.getNumUnites();
			var colisage = parseFloat(this.getColisage());
			this.qty = qtyc * colisage + qtyu;
		}

	}, this.setRemiseManuelle = function()
	{
		this.extra.discount_origin = "Manual";
		this.extra.discount_type = "coeff";

	}, this.getRowId = function()
	{
		return this.rowid;

	}, this.setRowId = function(id)
	{
		this.rowid = id;

	}, this.setLibelle = function(libelle)
	{
		this.libelle = libelle;

	}, this.getLibelle = function()
	{
		return this.libelle;

	}, this.setExtraDecimals = function(dec)
	{
		this.unit.nbdec = dec;

	}, this.getExtraDecimals = function()
	{
		return this.unit.nbdec;

	}, this.isExtraField = function(field)
	{
		if (field == "dim1")
			return 1;
		if (field == "dim2")
			return 2;
		if (field == "dim3")
			return 3;
		return 0;

	}, this.loadFromJson = function(data)
	{
		for (field in data) {
			if (field == "info" || field == "unit" || field == "extra") {
				this[field] = new Object();
				for (subfield in data[field]) {
					this[field][subfield] = data[field][subfield];
					if (this[field][subfield] == null) {
						this[field][subfield] = "";
					}
				}
			} else {
				this[field] = data[field];
			}
		}
		this.setPrice(this.subprice);
		this.setRemisePct(this.remise_percent);
		this.updateTotal();

		this.setUnits();

	}, this.setUnits = function()
	{
		if (this.unit.fmcal) {
			this.unit.fmcalJS = "#{" + this.unit.fmcal + "}";
			this.unit.unitLabels = getVariables(this.unit.fmcalJS);
			this.unit.numUnits = this.unit.unitLabels.length;
		} else {
			this.unit.numUnits = 0;
		}

	}, this.usesSpecialUnits = function()
	{
		return this.unit.numUnits;

	}, this.toCleanJson = function()
	{
		return removeObjectFunctions(this);

	}, this.getProductId = function()
	{
		return this.fk_product;

	}, this.getColisage = function()
	{
		return this.extra.nbcv;

	}, this.getNumColis = function()
	{
		this.extra.qtyc = this.extra.qtyc ? this.extra.qtyc : 0;
		return parseFloat(this.extra.qtyc);

	}, this.getNumUnites = function()
	{
		this.extra.qtyu = this.extra.qtyu ? this.extra.qtyu : 0;
		return parseFloat(this.extra.qtyu);

	}
}