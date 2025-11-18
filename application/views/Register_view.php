<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


	<title>register page</title>



</head>

<body>


<div class ="header">

	<h1>הרשמה למערכת</h1>



</div>

<div class="reg-container">

	<label>הזן שם משתמש:</label><br>
	<input id="user-name"><br>
	<label>הזן חשבון אימייל</label><br>
	<input id="user-email"><br>
	<label>הזן סיסמא:</label><br>
	<input id = "user-pass"><br>
	<input type="submit" id="register">


</div>





<script>

$(document).ready(function (){

	$("#register").on("click", function () {

		const name = $('#user-name').val().trim();
		const email = $('#user-email').val().trim();
		const pass = $('#user-pass').val().trim();

		$.ajax({
			url: '<?php echo site_url("Register/register"); ?>',
			type: 'POST',
			data: {
				name: name,
				email: email,
				pass: pass
			},

			success: function (response) {
				console.log("post success", response.message);
			}


		})
	})})


</script>
</body>
