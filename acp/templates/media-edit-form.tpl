<div class="container">
	<form role="form" action="{form_action}" id="media_form" method="POST">
		<div class="row">
			<div class="col-md-8">
				
				<div class="row">
						<div class="col-md-5">
							<div class="form-group">
						    <label>{label_title}</label>
						    <input type="text" class="form-control" name="title" value="{title}">
						  </div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
						    <label>{label_alt}</label>
						    <input type="text" class="form-control" name="alt" value="{alt}">
						  </div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
						    <label>{label_priority}</label>
						    <input type="text" class="form-control" name="priority" value="{priority}">
						  </div>
						</div>
					</div>
				<div class="row">
						<div class="col-md-6">
					<div class="form-group">
				    <label>{label_keywords}</label>
				    <input type="text" class="form-control" name="keywords" rows="4" value="{keywords}" data-role="tagsinput">
				  </div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
						    <label>{label_url}</label>
						    <input type="text" class="form-control" name="url" value="{url}">
						  </div>
						</div>
					</div>
				<div class="form-group">
			    <label>{label_text}</label>
			    <textarea class="form-control" name="text" rows="5">{text}</textarea>
			  </div>
			  <div class="row">
				   <div class="col-md-4">
						<div class="form-group">
					    <label>{label_license}</label>
					    <input type="text" class="form-control" name="license" value="{license}">
					  </div>
				   </div>
				   <div class="col-md-4">
						<div class="form-group">
					    <label>{label_credits}</label>
					    <input type="text" class="form-control" name="credit" value="{credit}">
					  </div>
				   </div>
				   <div class="col-md-4">
						<div class="form-group">
					    <label>{label_version}</label>
					    <input type="text" class="form-control" name="version" value="{version}">
					  </div>
				   </div>
			  </div>
				  
				<div class="form-group">
			    <label>{label_notes}</label>
			    <textarea class="form-control" name="notes" rows="5">{notes}</textarea>
			  </div>
				  
			  <input type="hidden" name="saveMedia" value="save">
			  <input type="hidden" name="realpath" value="{realpath}">
			  <input type="hidden" name="folder" value="{folder}">
			  <input type="hidden" name="set_lang" value="{set_lang}">
			  <input type="hidden" name="filesize" value="{filesize}">
			  <input type="hidden" name="file" value="{filename}">
			  <input  type="hidden" name="csrf_token" value="{token}">
			  <input type="submit" name="save" class="btn btn-save" value="{save}">
				  
			</div>
			<div class="col-md-4">
				<h4 class="small">{filename}</h4>
				<div class="well well-sm">
					{preview}	
				</div>
				
				<table class="table table-sm">
					<tr>
						<td class="text-right"><i class="far fa-clock"></i></td>
						<td>{edittime}</td>
					</tr>
					<tr>
						<td class="text-right"><i class="fas fa-external-link-alt"></i></td>
						<td><a href="{showpath}">{showpath}</a></td>
					</tr>
					<tr>
						<td class="text-right"><i class="fas fa-info-circle"></i></td>
						<td>{rfilesize} {image_dimensions}</td>
					</tr>
					<tr>
						<td class="text-right"><i class="fas fa-code"></i></td>
						<td><input type="text" class="form-control" name="bn" value="[{shortcode}={basename}][/{shortcode}]"></td>
					</tr>
					<tr>
						<td class="text-right"><i class="fas fa-language"></i></td>
						<td>{lang_switch}</td>
					</tr>
					<tr>
						<td colspan="2">
							{media_labels}
						</td>
					</tr>
				</table>
				{message}
			</div>
		</div>
	</form>
</div>