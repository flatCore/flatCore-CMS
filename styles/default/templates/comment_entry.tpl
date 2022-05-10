{foreach $comments as $comment => $value}
<div class="card comment-entry">
  <div class="row no-gutters">
    <div class="col-md-1">
      {$value.avatar}
    </div>
    <div class="col-md-11">
      <div class="card-body">
            <span class="entry-nbr">#{$value.id}</span>
            <h5 class="card-title">{$value.author}</h5>
            <p class="card-text">{$value.text}</p>
      </div>
    </div>
  </div>
  <div class="card-footer text-muted">
		{$value.time}
		<a href="{$value.url_answer_comment}" class="btn btn-sm btn-outline-primary float-end">{$lang_answer}</a>
	</div>
</div>
{/foreach}