<div class="container">
	<div class="row">
		<div class="col-md-6">
			<form role="form" action="{form_action}" id="media_form" method="POST">
				<input type="hidden" name="file" value="{filename}">
				<div class="form-group">
			    <label>{label_title}</label>
			    <input type="text" class="form-control" name="title" value="{title}">
			  </div>
				<div class="form-group">
			    <label>{label_description}</label>
			    <textarea class="form-control" name="description">{description}</textarea>
			  </div>
				<div class="form-group">
			    <label>{label_keywords}</label>
			    <input type="text" class="form-control" name="keywords" rows="4" value="{keywords}">
			  </div>
				<div class="form-group">
			    <label>{label_text}</label>
			    <textarea class="form-control" name="text" rows="8">{text}</textarea>
			  </div>
			  <input type="hidden" name="saveMedia" value="save">
			  <input type="hidden" name="realpath" value="{realpath}">
			  <input type="hidden" name="folder" value="{folder}">
			  <input type="submit" name="save" class="btn btn-success" value="{save}">
			</form>
		</div>
		<div class="col-md-6">
			<h3>{filename}</h3>
			<div class="well well-sm">
				{preview}	
			</div>
			<hr>
			<table class="table table-condensed">
				<tr>
					<td class="text-right"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></td>
					<td>{edittime}</td>
				</tr>
				<tr>
					<td class="text-right"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></td>
					<td><a href="{showpath}">{showpath}</a></td>
				</tr>
				<tr>
					<td class="text-right"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span></td>
					<td>{filesize}</td>
				</tr>
			</table>
			{message}
		</div>
	</div>
</div>