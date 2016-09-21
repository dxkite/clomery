<!doctype html>
<html>
<head>
	<title><?php Env::echo(isset($hello) ? $hello : 'No Value') ?></title>
	<?php Env::include("head") -> render(); ?>
	<link rel="stylesheet" href="/css/main.css">
	<script>
		$(function() {
			$('#captcha').click(function() {
				this.src = this.src + '?' + new Date();
			});
		});
	</script>
</head>
<body>
	<?php /*  注释  */ ?>
	<?php Env::include("layout.header") -> render(); ?>
	<h3 class="custom-heading">Request</h3>
	<form action="/auth/request" method="POST">
		<table class="custom-table auth-request-table">
			<?php if(isset($errors)): ?>
				<?php foreach($errors->all() as $error): ?>
					<tr>
						<td colspan="3"><span class="label label-warning"><?php Env::echo( $error ) ?></span></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if(isset($info)): ?>
				<tr>
					<td colspan="3"><span class="label label-info"><?php Env::echo( $info ) ?></span></td>
				</tr>
				<?php else: ?>
				<tr>
					<td colspan="3"><span class="label label-info"><?php Env::echo( $infoelse ) ?></span></td>
				</tr>
			<?php endif; ?>
			<tr>
				<td>Email</td>
				<td><input class="form-control" name="email" type="text" /></td>
			</tr>
			<tr>
				<td>Captcha</td>
				<td><input class="form-control" name="captcha" type="text" /></td>
				<td><img id="captcha" src="<?php Env::echo( captcha_src("flat") ) ?>" alt="Captcha" /></td>
			</tr>
			<tr>
				<td></td>
				<td class="text-right"><input type="submit" value="Reset"/></td>
			</tr>
		</table>
	</form>
	<p class="text-center">If you do not found email in 5 minutes, check for spam list</p>
</body>
</html>