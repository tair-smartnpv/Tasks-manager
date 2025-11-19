<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/loginStyle.css') ?>">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">


	<title>login page</title>

</head>


<body>

<div class="wrapper">
	<div class="header">

		<h1>כניסה למערכת</h1>


	</div>

	<div class="log-container">

		<label>הזן חשבון אימייל:</label>
		<div id="error-email" class="text-danger mt-2"></div>
		<input type="email" id="user-email"><br>
		<label>הזן סיסמא:</label>
		<div id="error-pass" class="text-danger mt-2"></div>
		<input type="password" id="user-pass"><br>
		<input type="submit" id="login"><br>
		<label>אין עדיין משתמש?</label>
		<button id="register">הרשמה</button>


	</div>
</div>

<script>
	$(document).ready(function () {

		$("#login").on("click", function () {

				const email = $('#user-email').val().trim();
				const password = $('#user-pass').val().trim();
				$("#error-email").text("");
				$("#error-pass").text("");


				$.ajax({
					url: '<?php echo site_url("Login/login"); ?>',
					type: 'POST',
					dataType: 'json',
					data: {
						email: email,
						password: password
					},

					success: function (response) {
						console.log("post ", response.status, response.message);
						if (response.status === 'success') {
							window.location.href = "Projects";
						}
						if (response.status === 'fail') {
							console.log(response.message);
							if(response.message.email){
								$("#error-email").html(response.message.email);

							}
							if(response.message.password){
								$("#error-pass").html(response.message.password)
							}
						}


					},
					error: function () {
						console.log("error")
					}


				})
			}
		)
		$("#register").on("click", function () {
			window.location.href = "Register";
		})

	})


</script>
</body>
