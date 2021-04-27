<div class="post-list-event">
	<div class="row mb-3">
		<div class="col-md-2">
			
			<div class="event-date">
				<div class="event-date-header">
					<span class="event-start-day">{event_start_day}.</span>
					<span class="event-start-month">{event_start_month_text}</span>
				</div>
				<span class="event-start-year">{event_start_year}</span>
				<div class="event-date-footer">
					<span class="event-end-date">{event_end_day}.{event_end_month}.{event_end_year}</span>
				</div>
			</div>
			

		</div>
		<div class="col-md-7">
			<span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span>
			<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
			{post_teaser}
			{post_voting}
		</div>
		<div class="col-md-3">
			
			<div class="teaser-image">
				<img src="{post_img_src}" class="img-fluid" alt="" title="{post_title}">
			</div>
			
		</div>
	</div>
	
	<div class="text-end">
		<p class="m-0 post-categories">{post_cats}</p>
		<p><a class="btn btn-primary {read_more_class}" href="{post_href}">{read_more_text}</a></p>
	</div>
	
</div>