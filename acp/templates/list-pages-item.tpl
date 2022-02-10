<div style="padding-left: {item-indent}">

	<div class="page-list-controls page-list-item {item-class}">
		<div class="label-page-status" title="{status-label}"></div>
	
		<div class="row">
			<div class="col-lg-7">
				
				<div class="row">
					<div class="col-lg-2 d-none d-lg-block">
						<img src="{item-tmb-src}" class="img-fluid">
					</div>
					<div class="col-lg-10">
						<h4 class="mb-0"><a href="{frontend-link}" title="{frontend-link}">{item-linkname}</a></h4><small>{item-title}</small>
					</div>
				</div>
						<div class="small info-collapse info-show">
							{item-description}
							<p><i class="fas fa-link"></i> {item-permalink} <span class="text-primary">{item-redirect}</span><br><i class="fas fa-clock"></i> {item-lastedit}</p>
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