<!DOCTYPE html>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/main.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="<?php echo base_url();?>assets/js/docData.js"></script>
<script src="<?php echo base_url();?>assets/js/doc.js"></script>
</head>
<body>
	<div class="container">
		
		<?php $this->load->view('admin/navbar'); ?>
		
		<div id="page-heading">
			<h1>Digime REST API Documantation</h1>
			<p>Use this page to explore the functions of the API.</p>
		</div>
		<div id="nav-bar">
			<button class="btn btn-default" id="btn-intro"><b>Intro</b></button>
			<button class="btn btn-default" id="btn-apiref"><b>API Reference</b></button>
		</div>
		<div id="tab0-page-content">
			<h2>Introduction</h2>
			<p>
				DigiMe is a prototype system for personal smart device in events. It is be able to show the users useful information like schedules, news, announcement, speech sessions, geolocation point of interests (POI). In live sessions, it can also show real-time presentation slide which can be bookmarked and kept on the device. User could also ask and questions in sessions and vote in polls. It is aimed to be modular, easily customizable and be able operate without the presence of internet connection (rely on local network only). Thus, third-party web services like Google Cloud Messaging (GCM) and Google Maps API cannot be used.
			</p>
			<h3>current modules</h3>
			<ul id="ul-modules">
			
			</ul>
			<h2>Getting Started</h2>
			<div class="subsection">
			<p>The Digime backend REST service is written in PHP with CodeIgniter. 
			First step to get you started is to know the end point of your digime installation.
			The end point is located at index.php under digime folder. If you put the folder digime under your www_root.
			You can access it from {YOUR_WWW_ROOT}/digime/index.php. GET request to this url will return the API version in json.</p>
			<div class="code-box">{"version":"1.0","host":"server"}</div>
			</div>
			<h2>Getting Access Token</h2>
			<div class="subsection">
			<p>Most of the API GET end points doesn't require access token.
			 There are some end points that need it such as live_session/attend or live_poll/v. Access token can be accquired by calling POST to the end point.</p>
			<div class="code-box">/auth/access_token</div></br />
			<b>Required Parameters</b>
			<table  border="0" cellspacing="0" class="params-table">
				<tr><td>username</td><td>string username of the user</td></tr>
				<tr><td>password</td><td>string password of the user</td></tr>
				<tr><td>client_key</td><td>client key of your application</td></tr>
				<tr><td>client_secret</td><td>client secret of your application</td></tr>
			</table>
			<p>The <code>client_key</code> and <code>client_secret</code> can only be add via the database in the <code>oauth_client</code> table.
			Or you can use default key/secret as <b>Digime Mobile Client</b> as </p>
			<code>client_key	=	1c6e2f528db2fb0f0d2930bd86de64892c90d3da<br />client_secret	=	1df76bef272aa713bdafe57221ac4fd46a6a2bdc</code> 
			<p>You will get the following json string as a result if the request is successful.</p>
			<div class="code-box">{"access_token":"9e10eff6efaa66d7fef167311b0e0c66834da95f"}</div>
			<p>You can keep the <code>access_token</code> to make further requests</p>
			<p>Otherwise, if the request has failed. Returned json will have the <code>error</code> object describing the error ocurred. For example.</p>
			<div class="code-box">
			{"error":{"code":1007,"message":"missing parameter username"}}<br />
			{"error":{"code":1009,"message":"invalid username or password"}}<br />
			{"error":{"code":1010,"message":"invalid client key or secret"}}
			</div>
			</div>
			<h2>Registering the user</h2>
			<div class="subsection">
			<p>To register a new user the email_address, username, password must be submitted to</p>
			<div class="code-box">/auth/register</div><br />
			Optional fields can be added but there are only three mandatory fields. They are
			<table border="0" cellspacing="0" class="params-table">
				<tr><td>email_address</td><td>email_address of the user</td></tr>
				<tr><td>username</td><td>desired username of the user</td></tr>
				<tr><td>password</td><td>desired password of the user</td></tr>
			</table>
			<p>If successful, a json response containing user data will be returned.</p>
			<div class="code-box">{"id":462,"username":"sundigi3","password":"sundigi3","email_address":"sundigi3@digimagic.com.sg"}</div>
			<p>If ejabberd is running then the username of the same name will also be created in ejabberd</p>
			<div class="code-box">{"error":{"code":1006,"message":"cannot create jabber"}}</div><br />
			<p>The error signifies your ejabberd server is not running or there is a problem making the connection with ejabberd.
			In this case, please refer to the backend installation manual.
			<br />Other than this, there are several errors that could happen such as.</p>
			<div class="code-box">
				{"error":{"code":1008,"message":"username already exists"}}<br />
				{"error":{"code":1006,"message":"email address already exists"}}<br />
				{"error":{"code":1007,"message":"missing parameter password"}}<br />
			</div>
			</div>
		</div>
		<?php $this->load->view('admin/apireference'); ?>
	</div>
	<center>
	<p class="small-text">Soravis Prakkamakul (Sun) sun@digimagic.com.sg<br />Digimagic Communications Pte Ltd 2013</p>
	</center>
</body>
</head>