<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/projectsStyle.css') ?>">
	<title> projects page</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">



</head>

<body>

<div>
	<div class="jumboyron">
		<div>
			<div class="user">
				<label>שלום, <?php echo $username ?> </label>
				<button id="logout">התנתקות</button>
			</div>
		</div>
		<h1 class="display-4">הפרויקטים שלי</h1>
		<h2 class="lead">כל הפרויקטים</h2>
		<hr class="my-4">
		<p class="lead">
			<button type="button" class="modal-btn" data-bs-toggle="modal" data-bs-target="#addProject">
				הוסף פרויקט חדש
			</button>
		</p>

	</div>


</div>
<div class="modal fade" id="addProject" tabindex="1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">

				<h1 class="modal-title fs-5" id="staticBackdropLabel">הוסף פרויקט חדש</h1>
			</div>
			<div class="modal-body">
				<div id="error-box" class="text-danger mt-2"></div>
				<label>שם פרויקט:</label><br>
				<input type="text" id="name-input" required><br>
				<label>הוסף תיאור:</label><br>
				<input type="text" id="desc-input">


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-btn">הוסף</button>

			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog"
	 aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">בטוח שאתה רוצה למחוק?</h5>
			</div>

			<div class="modal-footer">
				<button class="btn btn-danger" id="confirmDelete">מחק</button>
				<button class="btn btn-secondary" data-bs-dismiss="modal">בטל</button>
			</div>
		</div>

	</div>

</div>

<div class="project-list " id="project-list"></div>


<script>
	function renderProject(project) {
		return `
        <div class="project" id="project-${project.uuid}">
            <h2>${project.name}</h2>
				<p> ${project.description}</p>

<div class="progress">
  <div id="progress-${project.uuid}" class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
</div>
<div class = "project-btn">
 		<a href='tasks/index/${project.uuid}' class='btn btn-primary'>משימות</a>
<button class="delete-btn" data-id="${project.uuid}">מחק</button>
      </div>  </div>
    `;
	}

	function progressBar(total, completed, projectID) {
		let percent = (completed / total) * 100;
		console.log(total, completed)
		$("#progress-" + projectID).css("width", percent + "%");
		$("#progress-" + projectID).attr("aria-valuenow", percent);
	}

	function askConfirmation() {
		return new Promise((resolve) => {

			$("#delete-modal").modal("show");

			$("#confirmDelete").one("click", function () {
				$("#delete-modal").modal("hide");
				resolve(true);
			});

			$("#cancelDelete").one("click", function () {
				$("#delete-modal").modal("hide");
				resolve(false);
			});
		});
	}


	$(document).ready(function () {
		// load all
		$.ajax({
			url: '<?php echo site_url("projects/get_projects_by_user"); ?>',
			type: 'GET',
			success: function (response) {
				// console.log("hello")
				let projects = JSON.parse(response)
				for (let i = 0; i < projects.length; i++) {
					let projectID = projects[i].id;
					let total = projects[i].total_tasks;
					let completed = projects[i].completed_tasks;

					$('#project-list').append(renderProject(projects[i]))
					console.log(projects[i])
					progressBar(total, completed, projectID);
				}

			},
			error: function () {
				$('#project-list').html('<p>error</p>');
			}
		});

		//add project
		$('#add-btn').on("click",  function () {
			const name = $('#name-input').val().trim();
			const desc = $('#desc-input').val().trim();


			$.ajax({
				url: '<?php echo site_url("Projects/add")?>',
				type: 'POST',
				dataType: 'json',
				data: {
					name: name,
					description: desc
				},
				success: function (response) {
					console.log(response);
					if (response.status === "error") {
						$("#error-box").html(response.message).show();
						return;
					}
					if (response.status === "success") {
						let project = response;
						$('#project-list').append(renderProject(project))
						console.log(`Created at ${new Date(project.created_at * 1000).toLocaleString()}`);
						let modal = bootstrap.Modal.getInstance(document.getElementById('addProject'));

						modal.hide();
						$('#name-input').val('');
						$('#desc-input').val('');
					}


				},
				error: function (response) {
					// alert('error');
					console.log(response)
				}
			})

		})
		//delete project
		$(document).on("click", ".delete-btn", async function () {
			const projectId = $(this).data('id');
			console.log(projectId)
			const $projectDiv = $('#project-' + projectId);
			const confirm = await askConfirmation();
			if (!confirm) return;
			$.ajax({
				url: '<?php echo site_url("Projects/delete"); ?>',// + projectId,
				method: 'POST',
				data: {uuid: projectId},
				dataType: 'json',
				success: function (response) {
					console.log("deleted");

					$projectDiv.fadeIn("slow", function () {
						$(this).remove();
					})
				},
				error: function () {
					alert("error")
				}

			})
		})

		$('#logout').on("click", function () {
			$.ajax({
				url: 'Login/logout',
				method: 'POST',
				dataType: 'json',
				data: {
					logout: 'logout'
				},
				success: function (response) {
					window.location.href = "Login";
				}
			})
		})
		$(document).on("click", ".modal_btn", function () {

		})


	});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
