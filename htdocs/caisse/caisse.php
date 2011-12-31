<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css"
	href="../caisse/css/fixedHeaderTable.css"
	media="all">
<link rel="stylesheet" type="text/css"
	href="../caisse/css/playground.css"
	media="all">
<script type="text/javascript"
	src="../caisse/js/fixedHeaderTable.js"></script>
<script type="text/javascript"
	src="../caisse/js/ticket.js"></script>
<script type="text/javascript"
	src="../caisse/js/ticketDecorator.js"></script>
<script type="text/javascript"
	src="../caisse/js/caisse.js"></script>
<script type="text/javascript"
	src="../includes/jquery/jquery.min.js"></script>
<script type="text/javascript"
	src="../includes/jquery/ui/jquery-ui.min.js"></script>
<script type="text/javascript" 
	src="../includes/jquery/js/jquery-latest.min.js"></script>
<script type="text/javascript" 
	src="../includes/jquery/js/jquery-ui-latest.custom.min.js"></script>
<title>caisse</title>
</head>

<body><div id="page"><header></header>

<section class="centerize">

<section class="column ticket left"><header>
<h1 style="float: right;">TOTAL:<span class="total" id="total">00,00</span></h1>
<h1>Client: <span id="client">None</span></h1>
<h3>Vendeur: <span id="vendeur">None</span></h3>
<h3 style="float: right;">Ticket: <span id="ticketId">0</span></h3>
<br>
</header>
<table class="fixedHeader" id="ticket_header">
	<thead>
		<tr>
			<th>Libelle</th>
			<th>Info</th>
			<th>HT Unit.</th>
			<th>Qte.</th>
			<th>%</th>
			<th>Total</th>
		</tr>
	</thead>
</table>
<div class="scrollContent" id="ticket_scroller">
<table class="scrollTable">
	<tbody class="scrollContent" id="ticket_list">

	</tbody>
</table>
</div>
<table class="input" id="ticket_input">
	<tbody class="scrollContent">

	</tbody>
</table>
<table id="ticket_input_info">
	<tbody>
	</tbody>
</table>
<div class="spacer">_________ _________ _________ _________ _________
_________ _________ _________ _________ _________ _________ _________ _________ _________ _________ _________ _________
_________ _________ _________ _________ _________ _________ _________
_________ _________</div>
</section>

<section class="column left info"><a class="awesome info red"
	onclick="logout();"><img src="img/exit.png"></a> <a
	class="awesome info grey" onclick="showLogin();"><img
	src="img/seller.png"></a> <a id="clientBtn" class="awesome info grey"
	onclick="getClientList()"><img src="img/customers.png"></a> <a
	class="awesome info grey"><img src="img/params.png"></a> <a
	class="awesome info blue" id="padToggle"><img src="img/calc.png"></a> <br>
</section>

<section class="column left controls"><a class="awesome large red"
	onclick="newTicket();">Nouveau Ticket</a><a
	onclick="getClientTickets();" class="awesome large darkblue">Rappeler
Ticket</a> <a class="awesome large darkblue notready">Placer Ticket en
Attente</a> <a class="awesome large grey" onclick="removeCurrentLine()">Effacer
Ligne</a><a class="awesome large grey " onclick="deleteCurrentTicket()">Supprimer
Ticket</a> <a class="awesome large darkblue notready">Retrait Caisse</a>
<a class="awesome large darkblue notready">Entree Caisse</a> <a
	class="awesome large important blue" onclick="getEncaissementInfo()">Encaisser</a></section>

<div class="clear"></div>
</section>

<section id="encaissement" class="selection ticket hideSelection"><header>
<h1>Encaissement</h1>
</header>
<table>
	<thead>
		<tr>
			<th colspan="2">Information Client</th>
			<th colspan="2">Information Ticket</th>
		</tr>
	</thead>
	<tr>
		<td width="100px">Nom</td>
		<td width="150px" id="encaissement_client_name"></td>
		<td width="100px">Total HT</td>
		<td width="150px" id="encaissement_total_ht"></td>
	</tr>
	<tr>
		<td>Solde</td>
		<td id="encaissement_client_solde"></td>
		<td>Total TTC</td>
		<td id="encaissement_total_ttc"></td>
	</tr>
	<tr>
		<td>Blocage</td>
		<td id="encaissement_client_blocage"></td>
		<td>Remise HT %</td>
		<td id="encaissement_total_discount"></td>
	</tr>
	<tr>
		<td>Plafond</td>
		<td id="encaissement_client_plafond"></td>
		<td>Montant Paye</td>
		<td id="encaissement_total_paid"></td>
	</tr>
	<tr>
		<td>Depassement</td>
		<td id="encaissement_client_depassement"></td>
		<td>Reste a Payer</td>
		<td id="encaissement_total_unpaid"></td>
	</tr>
</table>

<br>

<table>
	<thead>
		<tr>
			<th colspan="4">Historique des reglements</th>
		</tr>
	</thead>
	<tbody id="encaissement_historique">
		<tr>
			<td width="100px">Type</td>
			<td width="150px" id="encaissement_client_name"></td>
			<td width="100px">Montant</td>
			<td width="150px" id="encaissement_total_ht"></td>
		</tr>
	</tbody>
</table>

<br>

<table>
	<thead>
		<tr>
			<th colspan="4">Reglement</th>
		</tr>
	</thead>
	<tbody id="reglements">
		<tr>
			<td width="100px">Ajout Reglement</td>
			<td colspan="3" align="center">
			<a onclick="addPaymentMethod('cash');" class="awesome reglement darkblue" >Espece</a>
			<a onclick="addPaymentMethod('tip');" class="awesome reglement darkblue">TIP</a> 
			<a onclick="addPaymentMethod('cb');" class="awesome reglement darkblue">Carte Bancaire</a> 
			<a onclick="addPaymentMethod('virement');" 	class="awesome reglement darkblue">Virement</a> 
			<a onclick="addPaymentMethod('prelevement');" class="awesome reglement darkblue">Prelevement</a> 
			<a onclick="addPaymentMethod('cheque');" class="awesome reglement darkblue">Cheque</a></td>
		</tr>
		<tr>
			<td width="100px">Espece</td>
			<td width="150px"><input type="number" min="0" id="reglement_cash" class="reglement"></td>
			<td width="150px"></td>
		</tr>
		<tr>
			<td width="100px">Carte Bancaire</td>
			<td width="150px"><input type="number" min="0" id="reglement_cb" class="reglement"></td>
			<td width="150px"></td>
		</tr>
	</tbody>
</table>
<center>
<a class="awesome large grey encaissement" onclick="pay();">Valider</a>
<a class="awesome large red encaissement" onclick="hideEncaissementBox();">Annuler</a>
</center>
</section>


<!--section class="centerize productsearch" </secction -->



<section class="selection ticket hideSelection" id="selection"></section>
<section class="selection ticket hideSelection" id="loginBox"></section>
<section id="loader" class="hideBlock"><img src="img/ajax-loader.gif"></section>
<section class="pad" id="pad"><a class="awesome key darkblue">7</a> <a
	class="awesome key darkblue">8</a> <a class="awesome key darkblue">9</a>
<a class="awesome func grey">+/-</a> <a class="awesome key darkblue">4</a>
<a class="awesome key darkblue">5</a> <a class="awesome key darkblue">6</a>
<a class="awesome func grey">Suiv.</a> <a class="awesome key darkblue">1</a>
<a class="awesome key darkblue">2</a> <a class="awesome key darkblue">3</a>
<a class="awesome func grey">Prec.</a> <a class="awesome key darkblue">C</a>
<a class="awesome key darkblue">0</a> <a class="awesome key darkblue">,</a>
<a class="awesome ok  blue" id="pad_ok">OK</a> <input type="text"
	name="padValue" id="padValue" /></section>
<section class="selection ticket hideSelection" id="messageBox"
	onclick="hideMessageBox();"></section>
</body>
</html>
