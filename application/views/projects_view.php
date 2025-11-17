<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/projectsStyle.css') ?>">
	<title></title>


</head>

<body>

<div>
<div class="jumboyron">
	<h1 class="display-4">הפרויקטים שלי</h1>
	<h2 class ="lead">כל הפרויקטים</h2>
	  <hr class="my-4">
 <p class="lead">
    <button type="button" class="modal-btn" data-bs-toggle="modal" data-bs-target="#addProject">
			הוסף פרויקט חדש
		</button>
  </p>
</div>


</div>
<!-- <div class = "header">
	<div class="title">
		<h1>הפרויקטים שלי</h1>
	</div>
	<div class="adder">
		
	</div>
</div> -->
<!-- Modal -->
<div class="modal fade" id="addProject" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
	 aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title fs-5" id="staticBackdropLabel">הוסף פרויקט חדש</h1>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="error-box" class="text-danger mt-2"></div>
				<label>שם פרויקט:</label><br>
				<input type="text" id="name-input" required>
				<label>הוסף תיאור:</label><br>
				<input type="text" id="desc-input">


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				<button type="button" class="add-btn">הוסף</button>

			</div>
		</div>
	</div>
</div>
<div class = "project-list " id="project-list"></div>


<script>
	function renderProject(project) {
		return `<div class = "card" >
        <div class="project" id="project-${project.id}">
            <h2>${project.name}</h2>
				<p> ${project.description}</p>
<div class = "project-btn">
 		<a href='tasks/index/${project.id}' class='btn btn-primary'>Show tasks</a>
<button class="delete-btn" data-id="${project.id}">מחק</button>
      </div>  </div></div>
    `;
	}

	$(document).ready(function () {
		// load all
		$.ajax({
			url: '<?php echo site_url("projects/get_projects"); ?>',
			type: 'GET',
			success: function (response) {
				console.log("hello")
				let projects = JSON.parse(response)
				for (let i = 0; i < projects.length; i++) {
					$('#project-list').append(renderProject(projects[i]))
				}

			},
			error: function () {
				$('#project-list').html('<p>eror</p>');
			}
		});

		$(document).on("click", ".delete-btn", function () {
			const projectId = $(this).data('id');
			const $projectDiv = $('#project-' + projectId);
			if (!confirm('בטוחה שברצונך למחוק את הפרויקט הזה?')) return;

			$.ajax({
				url: 'projects/delete/' + projectId,
				method: 'POST',
				// dataType:{id:id},
				success: function (response) {
					console.log("deleted");

					$projectDiv.fadeOut(300, function () {
						$(this).remove();
					})
				}

			})
		})

		$(document).on("click", ".add-btn", function () {
			const name = $('#name-input').val().trim();
			const desc = $('#desc-input').val().trim();

				$.ajax({
					url: 'projects/add',
					type: 'POST',
					dataType: 'json',
					data: {
						name: name,
						description: desc
					},
					success: function (response) {
						console.log(response);
						if (response.status === "error"){
							// let res =JSON.parse(response)
							$("#error-box").html(response.message).show();
							// alert(response.message);
							return;
						}
						if (response.status === "success"){
							let project = response;
							$('#project-list').append(renderProject(project))
							console.log(`Created at ${new Date(project.created_at * 1000).toLocaleString()}`);
							let modal = bootstrap.Modal.getInstance(document.getElementById('addProject'));

						modal.hide();
						 $('#name-input').val('');
						 $('#desc-input').val('');}


					},
					error: function () {
						alert('error');
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
