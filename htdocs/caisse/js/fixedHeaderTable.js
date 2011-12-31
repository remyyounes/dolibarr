/* http://www.alistapart.com/articles/zebratables/ */
function removeClassName(elem, className)
{
	elem.className = elem.className.replace(className, "").trim();
}

function addCSSClass(elem, className)
{
	removeClassName(elem, className);
	elem.className = (elem.className + " " + className).trim();
}

String.prototype.trim = function()
{
	return this.replace(/^\s+|\s+$/, "");
}

function stripedTable()
{
	if (document.getElementById && document.getElementsByTagName) {
		var allTables = document.getElementsByTagName('table');
		if (!allTables) {
			return;
		}

		for ( var i = 0; i < allTables.length; i++) {
			if (allTables[i].className.match(/[\w\s ]*scrollTable[\w\s ]*/)) {
				var trs = allTables[i].getElementsByTagName("tr");
				for ( var j = 0; j < trs.length; j++) {
					removeClassName(trs[j], 'alternateRow');
					addCSSClass(trs[j], 'normalRow');
				}
				for ( var k = 0; k < trs.length; k += 2) {
					removeClassName(trs[k], 'normalRow');
					addCSSClass(trs[k], 'alternateRow');
				}
			}
		}
	}
}

function fillTable(n)
{
	var tl = document.getElementById("ticket_list");
	var t = tl.getElementsByTagName("tr")[0];
	while (n > 0) {
		n--;
		var nt = document.createElement("tr");
		nt.innerHTML = t.innerHTML;
		tl.appendChild(nt);
		adjustTicketTableHeader();
	}
}

function adjustTicketTableHeader()
{
	var th = document.getElementById("ticket_header");
	var ti = document.getElementById("ticket_input");
	var t = document.getElementById("ticket_list");
	if (t.clientHeight >= t.parentNode.parentNode.clientHeight) {
		th.style.padding = "0px 14px  0px 0px";
		ti.style.padding = "0px 15px  0px 0px";
	}
}
function togglePadVisibility()
{
	var pad = document.getElementById("pad");
	pad.style.display = (pad.style.display != "none") ? "none" : "block";
}
function initPad()
{

	var okBtn = document.getElementById("pad_ok");
	var padToggleBtn = document.getElementById("padToggle");
	padToggleBtn.onclick = function()
	{
		togglePadVisibility();
	}
	okBtn.onclick = function()
	{
		togglePadVisibility();
	}
}

function initFormLabels()
{
	var inputs = document.querySelectorAll(".input input");
	for ( var i = 0; i < inputs.length; i++) {
		inputs[i].oninput = function()
		{
			var l = this.previousSibling;
			if (l.nodeName == "LABEL") {
				if (this.value != "") {
					l.classList.add("hide");
				} else {
					l.classList.remove("hide");
				}
			}
		}.bind(inputs[i]);
	}
}

var ticket = '';

function getTicket()
{
	return ticket;
}

function setTicket(t)
{
	ticket = t;
}

function getEditingItem(){
	return getTicket().ticket.editing.ticketItem;
}

window.onload = function()
{
	loading = false;

	stripedTable();
	initPad();
	// initFormLabels();
	// ticket = new Ticket();
	// ticket.init();
	tableTicket = new TableTicket();
	tableTicket.init(new Ticket());
	setTicket(tableTicket);
	ticket.cancel();
	initFormLabels();
	

	logout();
	showLogin();
}

// move this to lib
Array.prototype.getUniqueValues = function()
{
	var hash = new Object();
	for (j = 0; j < this.length; j++) {
		hash[this[j]] = true
	}
	var array = new Array();
	for (value in hash) {
		array.push(value)
	}
	return array;
}

var getVariables = function(fmcal)
{
	return fmcal.match(/[a-z]+/ig, "!").getUniqueValues();
}

Array.prototype.contains = function(element)
{
	var cont = false;
	for ( var i = 0; i < this.length; i++) {
		if (this[i] == element) {
			cont = true;
		}
	}
	return cont;
}

Array.prototype.remove = function(element)
{
	for ( var i = 0; i < this.length; i++) {
		if (this[i] == element) {
			this.splice(i, 1);
			return;
		}
	}
}

// evaluate String to calculation
function evaluate(str, mappings)
{
	return str.replace(/\#\{([^#]+)\}/g, function(match, key)
	{
		var result;
		with (mappings) {
			result = eval(key);
		}
		if (isNaN(result)) {
			result = 0;
		}
		return result;
	});
}