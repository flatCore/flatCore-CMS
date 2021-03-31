<div class="form-container" id="comment-form">
	
	<p class="h3">{$comment_form_title}</p>
	<p>{$comment_form_intro}</p>
	<form action="{$form_action}#comment-form" method="POST">
		<div class="form-group">
	    <label for="input_name">{$label_name}</label>
	    <input type="text" class="form-control" id="input_name" name="input_name" value="{$input_name}" {$comment_name_readonly}>
	  </div>
	  
		<div class="form-group">
	    <label for="input_mail">{$label_mail}</label>
	    <input type="email" class="form-control" id="input_mail" aria-describedby="mail_help" name="input_mail" value="{$input_mail}" {$comment_mail_readonly}>
	    <small id="mail_help" class="form-text text-muted">{$label_mail_helptext}</small>
	  </div>
	  
	  <div class="form-group mb-2">
	    <label for="input_comment">{$label_comment}</label>
	    <textarea class="form-control" id="input_comment" rows="3" name="input_comment"></textarea>
	  </div>
	  
	  <input class="btn btn-primary" type="submit" name="send_user_comment" value="{$btn_send_comment}">
	  
	  <input type="hidden" name="page_id" value="{$page_id}">
	  <input type="hidden" name="post_id" value="{$post_id}">
		<input type="hidden" name="parent_id" value="{$parent_id}">
	</form>
	
	<div class="m-3">
	{$form_response}
	</div>
	
</div>