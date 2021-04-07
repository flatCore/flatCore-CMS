<form action='{formaction}' class='form-horizontal' id='editpage' method='post' name="editpage">
	<div class="row">
		<div class="col-md-9">
			<div class="card">
				<div class="card-header">
			<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
				<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#intro">{post_tab_intro}</a></li>
				<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#content">{post_tab_content}</a></li>
				<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#product">{post_tab_product}</a></li>
				<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#prefs">{post_tab_preferences}</a></li>
			</ul>
				</div>
				<div class="card-body">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="intro">
					<div class="row">
						<div class="col-md-6">
							<label>{label_title}</label> <input class="form-control" name="post_title" type="text" value="{post_title}"> <label>{label_description}</label> 
							<textarea class='mceEditor_small' name='post_teaser'>{post_teaser}</textarea>
						</div>
						<div class="col-md-6">
							<div class="well well-sm">
								<label>{label_image}</label> <input class="filter-images form-control" name="filter-images" placeholder="Filter ..." type="text">
								<div class="images-list scroll-container">
									{widget_images}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="content">
					<textarea class='mceEditor' name='post_text'>{post_text}</textarea>
					
					<fieldset>
						<legend>{label_product_snippet_text}</legend>
						{snippet_select_text}
					</fieldset>
					
				</div>

				<div class="tab-pane fade" id="product">
					
					<div class="form-group">
						<label>{label_product_number}</label>
						<input class='form-control' name="post_product_number" type="text" value="{post_product_number}">
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>{label_product_manufacturer}</label>
								<input class='form-control' name="post_product_manufacturer" type="text" value="{post_product_manufacturer}">
							</div>
						</div>
						<div class="col-md-6">				
							<div class="form-group">
								<label>{label_product_supplier}</label>
								<input class='form-control' name="post_product_supplier" type="text" value="{post_product_supplier}">
							</div>
						</div>
					</div>
					
					<div class="row">
						
						<div class="col-md-2">
							<div class="form-group">
								<label>{label_product_currency}</label>
								<input class='form-control' name="post_product_currency" type="text" value="{post_product_currency}">
							</div>						
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>{label_product_price_label}</label>
								<input class='form-control' name="post_product_price_label" type="text" value="{post_product_price_label}">
							</div>
						</div>
						
					</div>
					
					<div class="row">
						
						<div class="col-md-2">
							<div class="form-group">
								<label>{label_product_amount}</label>
								<input class='form-control' name="post_product_amount" type="text" value="{post_product_amount}">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>{label_product_unit}</label>
								<input class='form-control' name="post_product_unit" type="text" value="{post_product_unit}">
							</div>	
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>{label_product_tax}</label>
								{select_tax}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>{label_product_price_net}</label>
								<input class='form-control' id="price" name="post_product_price_net" type="text" value="{post_product_price_net}">
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>{label_product_price_gross}</label>
								<input class='form-control' id="price_total" name="post_product_price_gross" type="text" value="{post_product_price_gross}">
							</div>
						</div>
					</div>
					
					<fieldset>
						<legend>{label_product_snippet_price}</legend>
						{snippet_select_pricelist}
					</fieldset>
										

					
				</div> <!-- #product -->
				
				
				<div class="tab-pane fade" id="prefs">
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
					<div class="form-group">
						<label>{label_keywords}</label>
						<textarea class='form-control' name="post_tags">{post_tags}</textarea>
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
					
				</div><!-- #prefs -->
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
					<legend>{label_releasedate}</legend>
					<input class='dp form-control' name="post_releasedate" type="text" value="{post_releasedate}">
				</fieldset>
				<fieldset>
					<legend>{label_priority}</legend> {select_priority} {checkbox_fixed}
				</fieldset>
				<fieldset>
					<legend>{label_status}</legend> {select_status}
				</fieldset>
				<fieldset>
					<legend>{label_comments}</legend> {select_comments}
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