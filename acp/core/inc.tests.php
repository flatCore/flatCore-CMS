<?php
//prohibit unauthorized access
require 'core/access.php';
?>

<h3>UI Tests</h3>

<div class="row">
	<div class="col-md-9">

		<h4 id="headings">Headings</h4>
		
		<h1>.h1 flatCore Content Management System</h1>
		<h2>.h2 flatCore Content Management System</h2>
		<h3>.h3 flatCore Content Management System</h3>
		<h4>.h4 flatCore Content Management System</h4>
		<h5>.h5 flatCore Content Management System</h5>
		<h6>.h6 flatCore Content Management System</h6>
		<p>&lt;p&gt; flatCore Content Management System</p>
		<p>This is <code>Code &lt;code&gt;</code></p>
		<hr>
		
		<h4 id="buttons">Buttons</h4>
		
		<p><a href="#" class="btn btn-fc">btn fc</a> <a href="#" class="btn btn-fc text-success">btn fc text-success</a> <a href="#" class="btn btn-primary">btn primary</a> <a href="#" class="btn btn-success">btn success</a> <a href="#" class="btn btn-danger">btn danger</a> <a href="#" class="btn btn-secondary">btn secondary</a> <a href="#" class="btn btn-info">btn info</a></p>
		<p><a href="#" class="btn btn-outline-primary">btn btn-outline-primary</a> <a href="#" class="btn btn-outline-secondary">btn btn-outline-secondary</a> <a href="#" class="btn btn-outline-success">btn btn-outline-success</a></p>
		<p><a href="#" class="btn btn-fc">btn fc</a> <a href="#" class="btn btn-save">btn save</a></p>
		
		<hr>
		
		<h4 id="container">Container</h4>
		
		<div class="well">well</div>
		<div class="well well-sm">well sm</div>
		
		<div class="alert alert-success">alert success</div>
		<div class="alert alert-danger">alert danger</div>
		<div class="alert alert-warning">alert warning</div>
		<div class="alert alert-info">alert info</div>
		<div class="alert alert-secondary">alert secondary</div>
		<div class="alert alert-fc">alert fc</div>
		
		<fieldset class="mt-4">
			<legend>fieldset .mt-4</legend>
			<p>flatCore Content Management System</p>
			<p><a href="#" class="btn btn-fc">btn fc</a> <a href="#" class="btn btn-save">btn save</a></p>
			
			<div class="card">
				<div class="card-header">Card</div>
				<div class="card-body">
					<p class="card-text">Card Body Text</p>
				</div>
				<div class="list-group list-group-flush">
					<a href="#" class="list-group-item list-group-item-ghost p-1 px-2">Link #1</a>
					<a href="#" class="list-group-item list-group-item-ghost p-1 px-2 active">Link #2 active</a>
					<a href="#" class="list-group-item list-group-item-ghost p-1 px-2">Link #3</a>
				</div>
			</div>
			
		</fieldset>
		
		<pre>&lt;pre&gt; -> flatCore Content Management System</pre>
		
		<hr>
		
		<h4 id="icons">Icons</h4>
		
		<div class="scroll-container">
		<?php
			
			echo '<div class="card-columns custom-columns">';
			foreach($icon as $k => $v) {
				
				echo '<div class="card text-center mb-2">';
				echo '<div class="card-body">';
				echo '<span class="text-secondary h1">'.$v.'</span>';
				echo '<p class="card-text"><code>$icon[\''.$k.'\']</code></p>';
				echo '</div>';
				echo '</div>';
				
			}
			echo '<div class="w-100 p-4"></div>';
			echo '</div>';
					
		?>
		</div>
		
		<hr class="shadow-line">
		
		<h4 id="tables">Tables</h4>
		
		<p>class=&quot;table&quot;</p>
		<table class="table">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Addon</th>
		      <th scope="col">Theme</th>
		      <th scope="col">Module</th>
		      <th scope="col">Plugin</th>
		    </tr>
		  </thead>
		  <tbody>
		    <tr>
		      <th scope="row">1</th>
		      <td>publisher.mod</td>
		      <td>no</td>
		      <td>yes</td>
		      <td>no</td>
		    </tr>
		    <tr>
		      <th scope="row">2</th>
		      <td>cookies.mod</td>
		      <td>no</td>
		      <td>yes</td>
		      <td>no</td>
		    </tr>
		  </tbody>
		</table>

		<p>class=&quot;table table-sm table-striped&quot;</p>
		<table class="table table-sm table-striped table-hover">
		  <thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Addon</th>
		      <th scope="col">Theme</th>
		      <th scope="col">Module</th>
		      <th scope="col">Plugin</th>
		    </tr>
		  </thead>
		  <tbody>
		    <tr>
		      <th scope="row">1</th>
		      <td>publisher.mod</td>
		      <td>no</td>
		      <td>yes</td>
		      <td>no</td>
		    </tr>
		    <tr>
		      <th scope="row">2</th>
		      <td>cookies.mod</td>
		      <td>no</td>
		      <td>yes</td>
		      <td>no</td>
		    </tr>
		    <tr>
		      <th scope="row">2</th>
		      <td>default</td>
		      <td>yes</td>
		      <td>no</td>
		      <td>no</td>
		    </tr>
		  </tbody>
		</table>		

	</div>
	<div class="col-md-3">
		<ul>
		<li><a href="#headings">headings</a></li>
		<li><a href="#buttons">buttons</a></li>
		<li><a href="#container">container</a></li>
		<li><a href="#icons">icons</a></li>
		<li><a href="#tables">tables</a></li>
		</ul>
	</div>
</div>