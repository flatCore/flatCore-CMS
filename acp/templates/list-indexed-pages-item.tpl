<div class="well well-sm clearfix">
	<p class="mb-0"><strong>{title}</strong></p>
	<p class="mb-0">{description}</p>
	<p class="small mb-0"><a href="{url}">{url}</a></p>

	<div class="row">
		<div class="col-md-8">
						
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-sm btn-fc" data-toggle="modal" data-target="#modalHeadlines_{id}">Headlines {cnt_headlines} ({cnt_headline_errors})</button>
					<button type="button" class="btn btn-sm btn-fc" data-toggle="modal" data-target="#modalLinks_{id}">Links {cnt_links} ({cnt_links_errors})</button>
					<button type="button" class="btn btn-sm btn-fc" data-toggle="modal" data-target="#modalImages_{id}">Images {cnt_images} ({cnt_images_errors})</button>
				</div>
				
		</div>
		<div class="col-md-4">
			
			<div class="btn-group float-right" role="group">
				<a href="acp.php?tn=pages&sub=index&a=start&id={id}" class="btn btn-fc btn-sm" title="{title_start_index}">{btn_start_index}</a>
				<a href="acp.php?tn=pages&sub=index&a=update&id={id}" class="btn btn-fc btn-sm" title="{title_update_info}">{btn_update_info}</a>
				<a href="acp.php?tn=pages&sub=index&a=show_info&id={id}" class="btn btn-fc btn-sm" title="{title_show_info}">{btn_show_info}</a>
			</div>
			
		</div>
	</div>
</div>


<!-- modal for links -->
<div class="modal fade" id="modalLinks_{id}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Links {item_url}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {link_str}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- modal for headlines -->
<div class="modal fade" id="modalHeadlines_{id}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Headlines {item_url}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {headline_str}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<!-- modal for images -->
<div class="modal fade" id="modalImages_{id}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Images {item_url}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        {images_str}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>