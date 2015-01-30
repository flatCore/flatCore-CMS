<div class="container">
	<div class="row">
		<div class="col-md-8">
			<form role="form" action="{form_action}" id="media_form" method="POST">
				<input type="hidden" name="image" value="{filename}">
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
			  <input type="hidden" name="saveImage" value="save">
			  <input type="submit" name="save" class="btn btn-success" value="{save}">
			</form>
		</div>
		<div class="col-md-4">
			<h3>{filename}</h3>
			<div class="well">
				{preview}
			</div>
			{message}
		</div>
	</div>
</div>