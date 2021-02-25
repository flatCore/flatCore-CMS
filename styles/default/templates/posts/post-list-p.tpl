<div class="post-list-product">
	<div class="row">
		<div class="col-md-4">
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-fluid">
			</div>
		</div>
		<div class="col-md-8">
			
			<div class="price-tag">
				<div class="clearfix">
					<div class="price-tag-label">{post_product_price_label}</div>
				</div>
				<div class="price-tag-inner">
				{post_currency} {post_price_gross} <span class="product-unit">{post_product_unit}</span>
				</div>
			</div>
			
			<span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span>
			<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
			{post_teaser}
		</div>
	</div>
	<div class="text-end">
		<p class="m-0 post-categories">{post_cats}</p>
		<p><a class="btn btn-primary {read_more_class}" href="{post_href}">{read_more_text}</a></p>
	</div>
</div>