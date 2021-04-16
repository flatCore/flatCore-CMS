
<div class="row row-cols-1 row-cols-md-3 g-4">
	<div class="col">
		
		<div class="card h-100">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
					<li class="nav-item"><a class="nav-link active" href="#" data-bs-target="#pages_list" data-bs-toggle="tab">{tab_pages}</a></li>
					<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#pages_stats" data-bs-toggle="tab">{tab_pages_stats}</a></li>
				</ul>
			</div>
			<div class="card-body p-0">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="pages_list">
					{pages_list}
				</div>
				<div class="tab-pane fade" id="pages_stats">
					<div class="p-3">
					{pages_stats}
					</div>
				</div>				
			</div>
			</div>
			<div class="card-footer p-1 text-center d-flex">
				{btn_page_overview} {btn_new_page} {btn_update_index} {btn_delete_cache}
			</div>
		</div>
		
	</div>
	<div class="col">

		<div class="card h-100">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
					<li class="nav-item"><a class="nav-link active" href="#" data-bs-target="#post_list" data-bs-toggle="tab">{tab_posts}</a></li>
					<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#comment_list" data-bs-toggle="tab">{tab_comments}</a></li>
				</ul>
			</div>
			<div class="card-body p-0">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="post_list">
					{posts_list}
				</div>
				<div class="tab-pane fade" id="comment_list">
					{comments_list}
				</div>				
			</div>
			</div>
			<div class="card-footer p-1 text-center d-flex">
				{btn_post_overview} {btn_new_post} {btn_comments_overview}
			</div>
		</div>
		
	</div>
	<div class="col">		

		<div class="card h-100">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs" id="bsTabs" role="tablist">
					<li class="nav-item"><a class="nav-link active" href="#" data-bs-target="#user_list" data-bs-toggle="tab">{tab_user}</a></li>
					<li class="nav-item"><a class="nav-link" href="#" data-bs-target="#user_stats" data-bs-toggle="tab">{tab_user_stats}</a></li>
				</ul>
			</div>
			<div class="card-body p-0">
			<div class="tab-content">
				<div class="tab-pane fade show active" id="user_list">
					{user_list}
				</div>
				<div class="tab-pane fade" id="user_stats">
					<div class="p-3">
						{user_stats}
					</div>
				</div>				
			</div>
			</div>
			<div class="card-footer p-1 text-center d-flex">
				{btn_user_overview} {btn_new_user}
			</div>
		</div>

	</div>
</div>