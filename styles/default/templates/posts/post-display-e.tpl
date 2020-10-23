<!-- event -->
<div class="post-list-event">
	<h1>{post_title}</h1>
	<p><span class="post-author">{post_author}</span> <span class="post-releasedate">{post_releasedate}</span></p>	
		<div class="row">
		<div class="col-md-2">
			<div class="event-date">
				<div class="event-date-header">
					<span class="event-start-day">{event_start_day}</span>
					<span class="event-start-month">{event_start_month_text}</span>
				</div>
				<span class="event-start-year">{event_start_year}</span>
				<div class="event-date-footer">
					<span class="event-end-date">{event_end_day}.{event_end_month}.{event_end_year}</span>
				</div>
			</div>
		</div>
		<div class="col-md-7">
			{post_teaser}
			{post_text}
		</div>
		<div class="col-md-3">
			<p><img src="{post_img_src}" class="img-fluid"></p>
		</div>
		</div>
		
		{post_tpl_event_prices}
		{post_event_price_note}
		{post_tpl_event_hotline}

	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
	</div>
</div>