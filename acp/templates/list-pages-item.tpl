<div style="padding-left: {item-indent}">
	<div class="page-list-controls page-list-item {item-class}">
		<div class="label-page-status" title="{status-label}"></div>
	
		<div class="row">
			<div class="col-lg-7">
				<h4><a href="{frontend-link}" title="{frontend-link} [{item-pi}]">{item-linkname}</a> <small>{item-title}</small></h4>
				<div class="small info-collapse info-show">{item-description}<p>
						<i class="fas fa-link"></i> {item-permalink} <span class="text-primary">{item-redirect}</span><br>
						<i class="fas fa-clock"></i> {item-lastedit}		
					</p>
				</div>
			</div>
			<div class="col-lg-5">
		
				<p class="text-muted small info-collapse info-show">
					<i class="fas fa-sort"></i> {item-pagesort} | {item-lang}<br>
					<i class="fas fa-pencil-ruler"></i> {item-template}<br>
					{item-mod}
					{page_labels}
				</p>
				
				<div class="controls-container clearfix">
					<div class="btn-group d-flex" role="group">
						{edit-btn}
						{duplicate-btn}
						{comment-btn}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>