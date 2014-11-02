<script type='text/javascript'>
	
$(window).load(function(){
	
	Chart.defaults.global.responsive = true;
	var helpers = Chart.helpers;
	
	
	var dnUserData = [
				{
					value: {cnt_user_verified},
					color:"#5cb85c",
					highlight: "#5cb85c",
					label: "{label_user_verified}"
				},
				{
					value: {cnt_user_waiting},
					color: "#5bc0de",
					highlight: "#5bc0de",
					label: "{label_user_waiting}"
				},
				{
					value: {cnt_user_paused},
					color: "#f0ad4e",
					highlight: "#f0ad4e",
					label: "{label_user_paused}"
				},
				{
					value: {cnt_user_deleted},
					color: "#d9534f",
					highlight: "#d9534f",
					label: "{label_user_deleted}"
				}
				];
				
	var dnPagesData = [
				{
					value: {cnt_pages_public},
					color:"#5cb85c",
					highlight: "#5cb85c",
					label: "{label_pages_public}"
				},
				{
					value: {cnt_pages_draft},
					color: "#666666",
					highlight: "#666666",
					label: "{label_pages_draft}"
				},
				{
					value: {cnt_pages_ghost},
					color: "#f0ad4e",
					highlight: "#f0ad4e",
					label: "{label_pages_ghost}"
				},
				{
					value: {cnt_pages_private},
					color: "#d9534f",
					highlight: "#d9534f",
					label: "{label_pages_private}"
				}
				];


				
			
			var pagesChart = new Chart(document.getElementById("pages-chart-area").getContext("2d")).Doughnut(dnPagesData, {
			    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
			    animateRotate: true
			});
				
			var PagesLegendHolder = document.getElementById("pages-chart-legend");
			PagesLegendHolder.innerHTML = pagesChart.generateLegend();


			helpers.each(PagesLegendHolder.firstChild.childNodes, function (legendNode, index) {
			    helpers.addEvent(legendNode, "mouseover", function () {
			        var activeSegment = pagesChart.segments[index];
			        activeSegment.save();
			        activeSegment.fillColor = activeSegment.highlightColor;
			        pagesChart.showTooltip([activeSegment]);
			        activeSegment.restore();
			    });
			});
			
			helpers.addEvent(PagesLegendHolder.firstChild, "mouseout", function () {
			    pagesChart.draw();
			});
			


			var userChart = new Chart(document.getElementById("user-chart-area").getContext("2d")).Doughnut(dnUserData, {
			    tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
			    animateRotate: true
			});
				
			var UserLegendHolder = document.getElementById("user-chart-legend");
			UserLegendHolder.innerHTML = userChart.generateLegend();


			helpers.each(UserLegendHolder.firstChild.childNodes, function (legendNode, index) {
			    helpers.addEvent(legendNode, "mouseover", function () {
			        var activeSegment = userChart.segments[index];
			        activeSegment.save();
			        activeSegment.fillColor = activeSegment.highlightColor;
			        userChart.showTooltip([activeSegment]);
			        activeSegment.restore();
			    });
			});
			
			helpers.addEvent(UserLegendHolder.firstChild, "mouseout", function () {
			    userChart.draw();
			});
			
				
				
});
</script>