/* exported vote */
/* vote() is used in posts */

function vote($val){

	var data = $val.split("-");
	var post_id = data[data.length - 1];

	$.ajax({
  	type: 'POST',
    url: '/core/ajax.votings.php',
    data: { 
    	val: $val
    },
    success: function(response) { 
	    
			var votes = JSON.parse(response);
			
			var upvote_element_id = 'vote-up-nbr-'+post_id;
			var dnvote_element_id = 'vote-dn-nbr-'+post_id;
			
			var cnt_upv = document.getElementById(upvote_element_id);
			cnt_upv.innerHTML = votes.upv;
			var cnt_dnv = document.getElementById(dnvote_element_id);
			cnt_dnv.innerHTML = votes.dnv;
			
    }
  });
}

/* exported sign_guestlist */
/* sign_guestlist() is used in posts type events */

function sign_guestlist($val){

	$.ajax({
  	type: 'POST',
    url: '/core/ajax.guestlist.php',
    data: { 
    	val: $val
    },
    success: function(response) { 
	    		
			var commiters = JSON.parse(response);
			var cnt_commit = document.getElementById('nbr-commitments');
			cnt_commit.innerHTML = commiters.evc;
		
    }
  });
}