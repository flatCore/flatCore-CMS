<form id="database" action="index.php?step3" method="post">
                                <fieldset id="form">
                                    <label><span class="text-form">Datenbankhost:</span>
                                        <input type="text" name="dbhost" value="localhost">
                                        <span class="form-desc">(in der Regel localhost)</span></label>
                                    <label><span class="text-form">Datenbankname:</span>
                                        <input type="text" name="dbname">
                                        <span class="form-desc">(der Name der Datenbank)</span></label>
                                    <label><span class="text-form">Datenbankbenutzername:</span>
                                        <input type="text" name="dbuser">
                                        <span class="form-desc">(Der Benutzername f&uuml;r die Datenbank)</span></label>
                                    <label><span class="text-form">Datenbankpasswort:</span>
                                        <input type="text" name="dbpass">
                                        <span class="form-desc">(Das Passwort f&uuml;r den Datenbankzugriff)</span></label>
                                    <label><span class="text-form">Pr&auml;fix:</span>
                                        <input type="text" name="dbpref" value="meteor_">
                                        <span class="form-desc">(wird den Datenbanktabellen vorangestellt, kann frei gew&auml;hlt werden, es d&uuml;rfen keine Umlaute oder Sonderzeichen verwendet werden)</span></label>
                                    <div class="clear"></div>
                                    <div class="buttons"> <a class="button" href="#" onClick="document.getElementById('database').reset()">zur&uuml;cksetzen</a> <a class="button" href="#" onClick="document.getElementById('database').submit()">speichern</a> </div>
                                </fieldset>
                            </form>