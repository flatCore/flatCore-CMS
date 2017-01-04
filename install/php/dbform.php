<form class="form-horizontal" id="database" action="index.php?step3" method="post">
	<div class="form-group">
		<label class="col-sm-4 control-label">Datenbankhost:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="dbhost" value="localhost">
			<span class="help-block">(in der Regel localhost)</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Datenbankname:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="dbname">
			<span class="help-block">(der Name der Datenbank)</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Datenbankbenutzername:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="dbuser">
			<span class="help-block">(Der Benutzername f&uuml;r die Datenbank)</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Datenbankpasswort:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="dbpass">
			<span class="help-block">(Das Passwort f&uuml;r den Datenbankzugriff)</span>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pr&auml;fix:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="dbpref" value="fc_">
			<span class="help-block">(wird den Datenbanktabellen vorangestellt, kann frei gew&auml;hlt werden, es d&uuml;rfen keine Umlaute oder Sonderzeichen verwendet werden)</span>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-offset-4 col-sm-8">
			<a class="btn btn-default" href="#" onClick="document.getElementById('database').reset()">zur&uuml;cksetzen</a>
			<a class="btn btn-success" href="#" onClick="document.getElementById('database').submit()">speichern</a>
		</div>
	</div>
</form>