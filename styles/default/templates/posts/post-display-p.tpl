<div class="post-product">
	<h1>{post_title}</h1>
	
	<div class="card">
		<p><img src="{post_img_src}" class="img-fluid"><br><small>{post_img_caption}</small></p>
		<div class="card-img-overlay">
			<div class="price-tag">
				<div class="clearfix">
					<div class="price-tag-label">{post_product_price_label}</div>
				</div>
				<div class="price-tag-inner">
					{post_currency} {post_price_gross} <span class="product-amount">{post_product_amount}</span> <span class="product-unit">{post_product_unit}</span>
				</div>
			</div>
		</div>
	</div>
		
	
	<p><span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span></p>
	{post_teaser}
	<p></p>
	
	<div class="post-text">
		{post_text}
	</div>
	
	<div class="post-snippet-text">
		{post_snippet_text}
	</div>

	<div class="post-snippet-price">
		{post_snippet_price}
	</div>
	
	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
	</div>
</div>