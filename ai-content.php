<?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-sessions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-db-connection.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-functions.php");
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-header.php");
?>

<main>

	<?php $user = getUsersDetails($member); ?>
	
	<?php
	
	if (!$user || $user['member_is_admin'] != "yes") {
		stderr("<strong>Protected</strong> page.");
		include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php");
		die();		
	}
	
	?>

	<div class="row">
	
		<div class="col-md-3">
		
			<div class="card">
				<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard.php"); ?>
			</div>
			
			<div class="card">
				<?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-dashboard-analytics.php"); ?>
			</div>
			
		</div>
		
		<div class="col-md-9">
		  <div class="card">
			<div class="card-body">
			  <form action="ai-content.php" id="ai-content-generator" method="POST">
				<div class="mb-3">
				  <label for="temperatureToken" class="form-label">Choose the temperature for the language model:</label>
				  <input type="range" class="form-range" min="0.1" max="1" step="0.1" value="0.5" id="temperatureToken" name="temperatureToken">
				  <span id="temperatureValue"></span>
				  <script>
					const slider = document.getElementById("temperatureToken");
					const output = document.getElementById("temperatureValue");
					output.innerHTML = "Temperature: " + slider.value;
					slider.oninput = function() {
					  output.innerHTML = "Temperature: " + this.value;
					}
				  </script>
				</div>
				<div class="mb-3">
				  <label for="maxTokens" class="form-label">Enter the maximum number of tokens to generate:</label>
				  <input type="number" class="form-control" id="maxTokens" name="maxTokens" min="1" max="2048" value="2048" required>
				</div>
				<div class="mb-3">
				  <label for="textInput" class="form-label">Enter the input prompt for the language model:</label>
				  <textarea class="form-control" id="textInput" name="textInput" placeholder="Enter input prompt here" rows="10" required>Say this is a test</textarea>
				</div>
				<div class="mb-3">
				  <button type="submit" name="chatGPTSubmit" class="btn btn-primary" id="generateBtn">Generate Text</button>
				</div>
			  </form>
			  <div class="mb-3">
				<label for="outputArea" class="form-label">Generated Text:</label>
				<textarea class="form-control" id="outputArea" name="outputArea" placeholder="Generated text will appear here" rows="10" readonly></textarea>
			  </div>
			</div>
		  </div>
		</div>

		<script>
		  $(document).ready(function() {
			$("#ai-content-generator").on('submit', function(e) {
			  e.preventDefault();
			  $('#generateBtn').prop('disabled', true); // disable the button
			  $.ajax({
				type: "POST",
				url: "/ajax-ai.php",
				data: {
				  "textInput": $("#textInput").val(),
				  "temperatureToken": $("#temperatureToken").val(),
				  "maxTokens": $("#maxTokens").val(),
				  "chatGPTSubmit": "true" // Add a flag to indicate that this is a GPT submission
				},
				success: function(results) {
				  $('#outputArea').val(results);
				},
				error: function(data) {
				  alert("Uh oh! AJAX error!");
				},
				complete: function() {
				  $('#generateBtn').prop('disabled', false); // enable the button
				}
			  });
			});
		  });
		</script>
		
	</div>

</main>

<?php 
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/inc-footer.php"); 
?>