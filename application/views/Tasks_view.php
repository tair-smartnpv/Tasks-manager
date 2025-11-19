<!DOCTYPE html>
<html lang="he" dir="rtl">

<head>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?= base_url('assets/tasksStyle.css') ?>">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">

	<style></style>
</head>

<body>

<div class="header">

	<div>
		<a href="http://localhost/TaskManager/index.php/projects">חזרה לרשימת פרויקטים</a>
		<div>
			<h1 class="title">פרויקט: <?= $project->name ?> </h1>

		</div>
	</div>
</div>
<div class="adder">
	<p>הוספת משימה חדשה:</p>
	<div id="error-box" class="text-danger mt-2"></div>

	<input id='title-input'>
	<input type="date">
	<button id='add-btn'>הוסף משימה</button>


</div>
<ul id="tasks-list">
</ul>


<script>
	let project_id = "<?php echo $project_id; ?>";


	function renderTasks(task) {
		return `<li >
        <div class="task" id="task-${task.id}">
            <h3>${task.title}</h3>
<p>נוצר בתאריך: ${new Date(task.created_at * 1000).toLocaleDateString()}</p>
<div class = "task-controls">

				<input type = "checkbox" class= "task-status" data-id = "${task.id}"   ${task.status === 'completed' ? "checked" : ""}>
		<button class="delete-btn" data-id="${task.id}">מחק</button>
        </div></div></li>
    `;
	}

	$(document).ready(function () {
		$(function () {
			$('#tasks-list').sortable();
			$('#tasks-list').disableSelection();

		})
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

				},
				success: function (response) {
					console.log(response);
					if (response.status === "error") {
						$("#error-box").html(response.message).show();
						return;
					}
					if (response.status === "success") {
						let task = response;
						$('#tasks-list').append(renderTasks(task))
						console.log(`Created at ${new Date(task.created_at * 1000).toLocaleString()}`);
						$('#title-input').val('');
						console.log($("#task-" + task.id + " .task-status").data("id"));
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
