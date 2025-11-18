<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<title>login page</title>

	</head>


<body>
<div class ="header">

	<h1>כניסה למערכת</h1>



</div>

<div class="log-container">

	<label>הזן חשבון אימייל</label><br>
	<input id="user-email"><br>
	<label>הזן סיסמא:</label><br>
	<input id = "user-pass"><br>
	<input type="submit" id="login">


</div>


<script>
	$(document).ready(function (){

		$("#login").on("click", function () {

			const email = $('#user-email').val().trim();
			const pass = $('#user-pass').val().trim();

			$.ajax({
				url: '<?php echo site_url("Login/login"); ?>',
				type: 'POST',
				dataType: 'json',
				data: {
					email: email,
					pass: pass
				},

				success: function (response) {
					console.log("post " , response.status, response.message);
					if(response.status === 'success' ){
						window.location.href = "Projects";
					}
					if (response.status === 'fail'){
						console.log(response.message);
					}


				},
				error:function (){
					console.log("error")
				}


			})
		})})


</script>
</body>
