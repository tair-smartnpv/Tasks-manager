<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/tasksStyle.css') ?>">

	<style></style>
</head>

<body>

<div class="header">

	<div>
		<a href="http://localhost/TasksManager/index.php/Projects">חזרה לרשימת פרויקטים</a>
	<div>
		<h1 class="title">ניהול משימות-<?= $project->name ?> </h1>
		
	</div>
	</div>
</div>
<div class = "adder">
	<p>הוספת משימה חדשה:</p><br>
	<div id="error-box" class="text-danger mt-2"></div>

	<label for='title-input'></label><input id='title-input'>
	<button id='add-btn'>add task</button>

</div>
<div id="tasks-list">
</div>


<script>
	let project_id = "<?php echo $project_id; ?>";


	function renderTasks(task) {
		return `
        <div class="task" id="task-${task.id}">
            <h1>${task.title}</h1>
<div class = "task-controls">

				<input type = "checkbox" class= "task-status" data-id = "${task.id}"   ${task.status === 'completed' ? "checked" : ""}>
		<button class="delete-btn" data-id="${task.id}">מחק</button>
        </div></div>
    `;
	}

	$(document).ready(function () {
		//load all by id
		$.ajax({
			url: "<?php echo site_url('Tasks/get_by_project/'); ?>" + project_id,
			type: 'GET',
			success: function (response) {
				console.log("response", response);
				let tasks = JSON.parse(response);
				for (let i = 0; i < tasks.length; i++) {
					$('#tasks-list').append(renderTasks(tasks[i]))
				}
			},
			error: function () {
				alert("error");
			}
		})

		$('#add-btn').on("click", function () {
			const title = $('#title-input').val();
			const p_id = project_id;
		
				$.ajax({
					url: "<?php echo site_url('Tasks/add')?>",
					method: 'POST',
					dataType: 'json',
					data: {
						title: title,
						p_id: p_id,
						task_status: 'pending'
					},
					success: function (response) {
						console.log(response);
						if(response.status === "error"){
							$("#error-box").html(response.message).show();
							return;
						}
						if(response.status === "success"){
							let task = response;
							$('#tasks-list').append(renderTasks(task))
							console.log(`Created at ${new Date(task.created_at * 1000).toLocaleString()}`);
							$('#title-input').val('');
					}
					}


				})
			
		})


		$(document).on("click", ".delete-btn", function () {
			const taskId = $(this).data('id');
			const $taskDiv = $('#task-' + taskId);
			if (!confirm('Sure you want to delete?')) return;
			$.ajax({
				url: "<?php echo site_url('Tasks/delete') ?>",
				method: 'POST',
				data: {id: taskId},
				success: function (response) {
					console.log("deleted");
					$taskDiv.fadeOut(300, function () {
						$(this).remove();
					})

				}
			})

		})
		$(document).on("change", ".task-status", function () {
			let taskId = $(this).data("id");
			let status = $(this).is(":checked") ? "completed" : "pending";
			console.log("Task", taskId, "new status:", status);
			$.ajax({
				url: "<?php echo site_url('Tasks/update')?>",
				method: 'POST',
				data: {
					task_id: taskId,
					status: status
				},
				success: function (response) {
					console.log("response:", response);
					$("#task-" + taskId).toggleClass("completed", status);
				}
			})

		})


	})


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>
