<div class="brightBox">
	<form action="{$smarty.server.SCRIPT_NAME}?p={$p}" method="POST">

		<b>{$legend_login}</b>

		<label>{$label_username}</label>
		<input class="login_name" type="text" name="login_name" value=""><br />
		<label>{$label_psw}</label>
		<input class="login_psw" type="password" name="login_psw" value=""><br />

		<input class="btn" type="submit" name="login" value="{$button_login}">
		<p>{$show_forgotten_psw_link}</p>
	</form>

	<hr>
	<p>{$msg_register}<br>{$show_register_link}</p>


</div>