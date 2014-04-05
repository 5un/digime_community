<!DOCTYPE html>
<html>
<head>
<title></title>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/main.css" />
</head>
<body>
	<div class="container">
		
		<?php $this->load->view('admin/navbar'); ?>
		
		<div id="page-heading">
			<h1>Users Management</h1>
			<p>Use this page add/delete users. purge faulty users that is in ejabberd</p>
		</div>
		<br>
		<h4>Users registered in Digime</h4>
		
		<?php if(count($digime_registered_users) >0) { ?>
			<table class="table">
				<thead>
					<tr><th>username</th><th>email</th><th>in_ejabberd</th><th>actions</th></tr>
				</thead>
				<tbody>
					<?php foreach($digime_registered_users as $user) { ?>
					<tr>
						<td><?php echo $user->username; ?></td>
						<td><?php echo $user->email_address; ?></td>
						<td>
							<?php echo $user->in_ejabberd; ?>
						</td>
						<td>
							<form action="<?php echo base_url();?>index.php/admin/users/deleteuser" method="POST">
							<input type="hidden" name="username" value="<?php echo $user->username; ?>" />
							<button type="submit" class="btn btn-danger">Delete</button>
							</form>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php }else {?>
			<p>There are currently 0 users</p>
		<?php } ?>
		<br>
		
		<h4>Users in ejabberd not in Digime Database</h4>
		<?php if(count($ejabberd_invalid_users) >0) { ?>
			<table class="table">
				<thead>
					<tr><th>username</th><th>status</th><th>actions</th></tr>
				</thead>
				<tbody>
					<?php foreach($ejabberd_invalid_users as $user) { ?>
					<tr>
						<td><?php echo $user['username']; ?></td>
						<td></td>
						<td>
							<form action="<?php echo base_url();?>index.php/admin/users/deleteejabberduser" method="POST">
							<input type="hidden" name="username" value="<?php echo $user['username']; ?>" />
							<button type="submit" class="btn btn-danger">Delete</button>
							</form>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } else { ?>
			<p>There are currently 0 users</p>
		<?php } ?>
		
		<div class="panel panel-default">
			<div class="panel-body">
				<h4>Register new user</h4>
					<label>username</label><br><input type="text" id="inputUsername" value=""/><br>
					<label>password</label><br><input type="text" id="inputPassword" value=""/><br>
					<label>email_address</label><br><input type="text" id="inputEmail" value=""/><br>
					<br><button class="btn btn-default" id="btn-register">Register</button>
					<div><br>Result<br>
					<div id="register-result"></div>
					</div>
			</div>
		</div>
		
	</div>
	<center>
	<p class="small-text">Soravis Prakkamakul (Sun) sun@digimagic.com.sg<br />Digimagic Communications Pte Ltd 2013</p>
	</center>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script>
		var host = '<?php echo base_url(); ?>index.php/';
	
		$(document).ready(function(){
			$('#btn-register').click(function(){
				$.post(host + 'auth/register',
				{
					'email_address' : $('#inputEmail').val(),
					'username': $('#inputUsername').val(),
					'password': $('#inputPassword').val()
				},
				function(data){
					$('#register-result').html(data);
				},
				'text');
			});
		});
	</script>
	
</body>
</head>