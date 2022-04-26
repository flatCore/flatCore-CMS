<div style="padding-left: {item-indent}">

	<div class="page-list-controls page-list-item {item-class}">
		<div class="label-page-status" title="{status-label}"></div>
	
		<div class="row">
			<div class="col-sm-7 col-lg-8">
				
				<div class="row">
					<div class="col-sm-2 col-lg-3 d-none d-lg-block">
						<img src="{item-tmb-src}" class="img-fluid">
					</div>
					<div class="col-sm-10 col-lg-9">
						<h4 class="mb-0"><a href="{frontend-link}" title="{frontend-link}">{item-linkname}</a></h4>
						<p class="strong mb-0">{item-title}</p>
						<p class="small">{item-description}</p>
						<p class="small">
							<i class="bi bi-link-45deg"></i> {item-permalink} <span class="text-primary">{item-redirect}</span><br>
							<i class="bi bi-clock"></i> {item-lastedit}</p>
						</p>
					</div>
				</div>
			</div>
			<div class="col-sm-5 col-lg-4">
		
				<p class="text-muted small info-collapse info-show">
					<i class="bi bi-sort-down"></i> {item-pagesort} | {item-lang}<br>
					<i class="bi-palette"></i> {item-template}<br>
					<i class="bi bi-tag"></i> {page_labels}
					{item-mod}
				</p>
				
				<div class="controls-container clearfix">
					<form action="?tn=pages&sub=edit" method="POST" class="d-inline">
						<div class="btn-group d-flex" role="group">
							{edit-btn}
							{duplicate-btn}
							{comment-btn}
							{info-btn}
							{hidden_csrf_tokken}
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

</div>