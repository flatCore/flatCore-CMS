<div class="card comment-entry">
  <div class="row no-gutters">
    <div class="col-md-1">
      {comment_avatar}
    </div>
    <div class="col-md-11">
      <div class="card-body">
	      <span class="entry-nbr">#{comment_id}</span>
        <h5 class="card-title">{comment_author}</h5>
        <p class="card-text">{comment_text}</p>

      </div>
    </div>
  </div>
  <div class="card-footer text-muted">
		{comment_time}
		<a href="{url_answer_comment}" class="btn btn-sm btn-outline-primary float-end">{lang_answer}</a>
	</div>
</div>