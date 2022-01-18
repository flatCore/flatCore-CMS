<form action='{formaction}' class='form-horizontal' id='editpage' method='post' name="editpage">
	<div class="row">
		<div class="col-md-9">
			<div class="card">
				<div class="card-header">
					<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
						<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#content">{post_tab_content}</a></li>
						<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#meta">{post_tab_meta}</a></li>
						<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#preferences">{post_tab_preferences}</a></li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content">
						<div class="tab-pane show fade active" id="content">
					<div class="row">
						<div class="col-md-8">
							<label>{label_title}</label> <input class="form-control" name="post_title" type="text" value="{post_title}"><br>
							<label>{label_description}</label> 
							<textarea class='mceEditor_small' name='post_teaser'>{post_teaser}</textarea>						
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>{label_upload}</label>
								<button type="button" class="form-control btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadGalModal" {disabled_upload_btn}>{btn_upload}</button>
								<p class="form-text text-muted">{gallery_upload_help_text}</p>
							</div>
						</div>
					</div>
					<!-- if we have uploaded images, show a thumbnail list -->
					{thumbnail_list_form}
				</div>
				
				<div class="tab-pane fade" id="meta">
					<div class="form-group">
						<label>{label_title}</label>
						<input class='form-control' name="post_meta_title" type="text" value="{post_meta_title}">
					</div>
					<div class="form-group">
						<label>{label_description}</label>
						<textarea class='form-control' rows="4" name="post_meta_description">{post_meta_description}</textarea>
					</div>
					<div class="form-group">
						<label>{label_keywords}</label>
						<input type="text" class='form-control' name="post_tags" data-role="tagsinput" value="{post_tags}">
					</div>		
				</div>
				
				<div class="tab-pane fade" id="preferences">
					<div class="form-group">
						<label>{label_author}</label>
						<input class='form-control' name="post_author" type="text" value="{post_author}">
					</div>
					<div class="form-group">
						<label>{label_source}</label>
						<input class='form-control' name="post_source" type="text" value="{post_source}">
					</div>
					<div class="form-group">
						<label>{label_slug}</label>
						<input class='form-control' name="post_slug" type="text" value="{post_slug}">
					</div>

					<fieldset>
						<legend>RSS</legend>
						<div class="form-group">
							<label>{label_rss}</label>
							{select_rss}
						</div>
						<div class="form-group">
							<label>{label_rss_url}</label>
							<input class='form-control' name="post_rss_url" type="text" value="{post_rss_url}">
						</div>
					</fieldset>
				</div>
			</div>
		</div>
		</div>
		

		
		</div>
		<div class="col-md-3">
			<div class="well well-sm">
				
				<fieldset>
					<legend>{label_language}</legend>
					<div class="">
						{checkboxes_lang}
					</div>
				</fieldset>
				
				<fieldset>
					<legend>{label_categories}</legend>
					<div class="scroll-container" style="max-height: 150px;">
						{checkbox_categories}
					</div>
				</fieldset>
				
				<fieldset>
					<legend>{label_releasedate}</legend> <input class='dp form-control' name="post_releasedate" type="text" value="{post_releasedate}">
				</fieldset>
				
				<fieldset>
					<legend>{label_priority}</legend> {select_priority} {checkbox_fixed}
				</fieldset>
				
				<fieldset>
					<legend>Status</legend> {select_status}
				</fieldset>
				<fieldset>
					<legend>{label_comments}</legend> {select_comments}
				</fieldset>
				<fieldset>
					<legend>{label_votings}</legend> {select_votings}
				</fieldset>
				<fieldset>
					<legend>{labels}</legend> {post_labels}
				</fieldset>	
				<input name="post_type" type="hidden" value="{post_type}">
				<input name="modus" type="hidden" value="{modus}">
				<input name="post_id" type="hidden" value="{post_id}">
				<input type="hidden" name="csrf_token" value="{token}">
				<input type="hidden" name="post_date" value="{post_date}">
				{submit_button}
			</div>
		</div>
	</div>
</form>


<!-- if we have a gallery id, show the upload form -->
{modal_upload_form}



