<div class="container">

	{message}

	<form role="form" action="{form_action}" id="pluginForm" method="POST">
		<input type="hidden" name="plugin" value="{plugin}">
		<input type="hidden" name="csrf_token" value="{token}">

		<div class="form-group">
			<label>{label_filename}</label>
			<input type="text" class="form-control" name="filename" value="{filename}" readonly>
		</div>
		
		<div class="form-group">
	    <label>{label_content}</label>
	    <textarea class="form-control aceEditor_code textEditor switchEditor" name="plugin_src" rows="12">{plugin_src}</textarea>
	    <div id="aceCodeEditor"></div>
	  </div>
	  
	  <div class="btn-group pull-right">
	  	<input type="submit" name="delete" class="btn btn-danger" id="btnDel" value="{delete}" onclick="return confirm('{delete_msg}')">
		<input type="submit" name="save" class="btn btn-success" id="btnSave" value="{save}">
	  </div>
	  
	</form>

</div>
