<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/registerStyle.css') ?>">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">


	<title>register page</title>


</head>

<body>

<div class="wrapper">

	<div class="header">

		<h1>הרשמה למערכת</h1>


	</div>
	<div class="reg-container">

		<label>הזן שם משתמש:</label>
		<div id="error-name" class="text-danger mt-2"></div>
		<input type="text" id="user-name"><br>
		<label>הזן חשבון אימייל</label>
		<div  id="error-email" class="text-danger mt-2"></div>
		<input type="email" id="user-email"><br>
		<label>הזן סיסמא:</label>
		<div id="error-pass" class="text-danger mt-2"></div>
		<input type="password" id="user-pass"><br>
		<input type="submit" id="register"><br>
		<label>יש כבר משתמש?</label>
		<button id="login">להתחברות</button>


	</div>
</div>


<script>

	$(document).ready(function () {

		$("#register").on("click", function () {

			const name = $('#user-name').val().trim();
			const email = $('#user-email').val().trim();
			const pass = $('#user-pass').val().trim();
			$("#error-name").text("");
			$("#error-email").text("");
			$("#error-pass").text("");

			$.ajax({
				url: '<?php echo site_url("Register/register"); ?>',
				type: 'POST',
				dataType: 'json',
				data: {
					name: name,
					email: email,
					pass: pass
				},

				success: function (response) {
					console.log("post ", response.status, response.message);
					if (response.status === "success") {

						sessionStorage.setItem('login_msg','משתמש נוסף בהצלחה')
						window.location.href = 'Login';
					}
					if (response.status === "error") {

						if(response.message ==='email already exist'){
							sessionStorage.setItem('login_msg','משתמש קיים. אתה יכול להתחבר')
							window.location.href='Login';
						}


						if (response.message.name) {
							$("#error-name").html(response.message.name);
						}
						if (response.message.email) {
							$("#error-email").html(response.message.email);
						}
						if (response.message.pass) {
							$("#error-pass").html(response.message.pass);

						}
						console.log(response.message)
					}

				}


			})
		})

		$('#login').on("click", function () {
				window.location.href = "Login";
			}
		)
	})


</script>
</body>
