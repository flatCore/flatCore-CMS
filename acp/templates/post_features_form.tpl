
<div class="card p-2 mb-3">

<form action="" method="POST">	
	<div class="row">
		<div class="col-md-8">
			<div class="form-group">
				<label>{label_title}</label>
				<input type="text" name="feature_title" value="{feature_title}" class="form-control">
			</div>
			<div class="form-group">
		    <label>{label_text}</label>
		    <textarea class="form-control mceEditor switchEditor" name="feature_text" rows="5">{feature_text}</textarea>
		  </div>
  
		</div>
		<div class="col-md-4">
			
			
				<fieldset>
					<legend>{label_language}</legend>
					<div class="">
						{select_lang}
					</div>
				</fieldset>
			
			<div class="form-group">
				<label>{label_priority}</label>
				<input type="text" name="feature_priority" value="{feature_priority}" class="form-control">
			</div>			
			<hr>
			{btn_send_form}
		</div>
	</div>
	
	
	{hidden_csrf}
</form>

</div>