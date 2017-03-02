<div style="padding-left: {item-indent}">
	<div class="page-list-controls page-list-item {item-class}">
		<div class="label-page-status" title="{status-label}"></div>
	
		<div class="row">
			<div class="col-lg-8">
				<h4><a href="{frontend-link}" title="{frontend-link}">{item-linkname}</a> <small>{item-title} {item-mod}</small></h4>
				<div class="small info-collapse info-show">{item-description}<p>
						<span class="glyphicon glyphicon-link"></span> {item-permalink} <span class="text-primary">{item-redirect}</span><br>
						<span class="glyphicon glyphicon-time"></span> {item-lastedit}		
					</p>
				</div>
			</div>
			<div class="col-lg-4">
		
				<p class="text-muted small info-collapse info-show">
					<span class="glyphicon glyphicon-sort-by-attributes"></span> {item-pagesort} | {item-lang}<br>
					<span class="glyphicon glyphicon-file"></span> {item-template}<br>
					{page_labels}
				</p>
				
				<div class="controls-container">
					<div class="btn-group btn-group-justified">
						{edit-btn}
						{duplicate-btn}
						{comment-btn}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>