<div id="content">
	{$msg_content nocache}
	{$page_content}
</div>

{if $show_page_comments != ''}
	<hr class="shadow">
	<div class="page_comments_form">
		{$comment_form}
	</div>
	{$comments_intro}
	<div id="page_comments">
		{$comments_thread}
	</div>
{/if}