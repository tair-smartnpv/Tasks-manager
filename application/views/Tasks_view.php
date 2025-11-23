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
<!--<div class="adder">-->
<!--	<p>הוספת משימה חדשה:</p>-->
<!--	<div id="error-box" class="text-danger mt-2"></div>-->
<!--	<label>שם משימה:</label>-->
<!--	<label for='title-input'></label><input id='title-input'>-->
<!--	<label>לתאריך:</label>-->
<!--	<label for="deadline"></label><input type="date" id="deadline" min="-->
<?php //echo date('Y-m-d'); ?><!--" required>-->
<!--	<button id='add-btn'>הוסף משימה</button>-->
<!---->
<!---->
<!--</div>-->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-modal">
	הוסף משימה
</button>
<div class="modal fade" id="add-modal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">הוספת משימה חדשה</h5>
			</div>
			<div class="modal-body">
				<p>הוספת משימה חדשה:</p>
				<div id="error-box" class="text-danger mt-2"></div>
				<label>שם משימה:</label>
				<label for='title-input'></label><input id='title-input'>
				<label>לתאריך:</label>
				<label for="deadline"></label><input type="date" id="deadline" min="<?php echo date('Y-m-d'); ?>"
													 required>
			</div>
			<div class="modal-footer">
				<button class='add-btn'>הוסף משימה</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="edit-modal">
	<div class="modal-dialog" role="dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">עריכת משימה</h5>
			</div>
			<div class="modal-body">
				<label>עריכת שם:</label>
				<input id="title-update">
				<label>עריכת תאריך:</label>
				<input type="date" id="date-update" min="<?php echo date('Y-m-d'); ?>">
			</div>
			<div class="modal-footer">
				<button id="edit-task">עדכן</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
	 aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
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


<h3>משימות שלא סיימתי: (<span id="open-count"></span>)</h3>
<ul id="open-tasks">
</ul>

<h3>משימות שסיימתי: (<span id="completed-count"></span>)</h3>
<ul id="completed-tasks"></ul>


<script>
	let project_id = "<?php echo $project_id; ?>";

	function updateCounters() {

		const openTasks = $("#open-tasks li").length;
		console.log(openTasks);
		$('#open-count').text(openTasks)
		const completedTasks = $("#completed-tasks li").length;
		$('#completed-count').text(completedTasks);
		console.log(completedTasks);
	}


	function askConfirmation() {
		return new Promise((resolve) => {

			// פתיחת מודאל
			$("#delete-modal").modal("show");

			// לחיצה על אישור
			$("#confirmDelete").one("click", function () {
				$("#delete-modal").modal("hide");
				resolve(true);
			});

			// לחיצה על ביטול
			$("#cancelDelete").one("click", function () {
				$("#delete-modal").modal("hide");
				resolve(false);
			});
		});
	}

	function renderTasks(task) {
		let id = task.id;
		let title = task.title;
		let date = task.created_at;
		let created_date = new Date(date * 1000).toISOString().split("T")[0]
		let status = task.status;
		let timestamp = task.deadline;
		let deadline = new Date(timestamp * 1000).toISOString().split("T")[0];

		return `<li >
        <div class="task" id="task-${id}">
			<input type = "checkbox" class= "task-status" data-id = "${id}"   ${status === 'completed' ? "checked" : ""}>
            <h3>${title}</h3>
			<em>נוצר בתאריך: ${created_date}</em>
			<em>להגשה בתאריך : <span  class="deadline">${deadline}</span></em>
			<div class = "task-controls">

		<button class="edit-btn" data-id ="${id}">ערוך</button>
		<button class="delete-btn" data-id="${id}">מחק</button>
        </div></div></li>
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
					const taskHtml = renderTasks(tasks[i]);

					if (tasks[i].status === 'completed') {
						$('#completed-tasks').append(taskHtml);
					} else {
						$('#open-tasks').append(taskHtml);
					}
				}
				updateCounters();

			},
			error: function () {
				alert("error");
			}
		})

		$(document).on("click", '.add-btn', function () {
			const title = $('#title-input').val().trim();
			const date = $('#deadline').val();
			$.ajax({
				url: "<?php echo site_url('Tasks/add')?>",
				method: 'POST',
				dataType: 'json',
				data: {
					title: title,
					p_id: project_id,
					date: date

				},
				success: function (response) {
					console.log(response);
					if (response.status === "error") {
						$("#error-box").html(response.message).show();
						return;
					}
					if (response.status === "success") {
						let task = response;
						$('#open-tasks').append(renderTasks(task))
						console.log(`Created at ${new Date(task.created_at * 1000).toLocaleString()}`);
						$('#title-input').val('');
						$('#deadline').val('')
						console.log($("#task-" + task.id + " .task-status").data("id"));
						let modal = bootstrap.Modal.getInstance(document.getElementById('add-modal'));
						modal.hide();
						updateCounters();
					}
				}


			})

		})
		//edit task

		$(document).on("click", ".edit-btn", async function () {
			const taskId = $(this).data('id');
			const title = $("#task-" + taskId).find('h3').text();
			const deadline = $("#task-" + taskId).find('.deadline').text();//data("timestamp")
			console.log(title, deadline);
			$("#title-update").val(title);
			$("#date-update").val(deadline);
			$("#edit-modal").data("task-id", taskId);

			$("#edit-modal").modal("show");
		})


		$('#edit-task').on("click", function () {
				const taskId = $("#edit-modal").data('task-id');
				const title = $("#title-update").val();
				const date = $("#date-update").val();
				const $taskElement = $("#task-" + taskId);

				console.log(taskId, title, date, $taskElement.html())
				$.ajax({
					url: '<?php echo site_url('Tasks/update_task') ?>',
					method: 'POST',
					dataType: 'json',
					data: {
						id: taskId,
						title: title,
						deadline: date

					},
					success: function (response) {
						console.log(response)
						$taskElement.find('h3').text(title);
						$taskElement.find('.deadline').text(date);

					},
					error: function () {
						console.log("didn't update")
					}

				})

				console.log(taskId)
				let modal = bootstrap.Modal.getInstance(document.getElementById('edit-modal'));
				modal.hide();
			}
		)
		//delete task

		$(document).on("click", ".delete-btn", async function () {
			const taskId = $(this).data('id');
			const $taskDiv = $('#task-' + taskId);
			const $li = $taskDiv.closest('li');
			const confirm = await askConfirmation();


			if (!confirm) return;
			$.ajax({
				url: "<?php echo site_url('Tasks/delete') ?>",
				method: 'POST',
				data: {id: taskId},
				success: function (response) {
					console.log("deleted");
					$li.fadeOut(300, function () {
						$(this).remove();
						updateCounters();
					})


				},
				error: function () {
					console.log("Delete failed")
				}
			})

		})
		$(document).on("change", ".task-status", function () {
			let taskId = $(this).data("id");
			// let status = $(this).is(":checked") ? "completed" : "pending";
			let status = $("#task-" + taskId + " .task-status").is(":checked")
				? "completed"
				: "pending";
			console.log("Task", taskId, "new status:", status);
			$.ajax({
				url: "<?php echo site_url('Tasks/update_status')?>",
				method: 'POST',
				data: {
					task_id: taskId,
					status: status
				},
				success: function (response) {
					console.log("response:", response);
					$("#task-" + taskId).toggleClass("completed", status === 'completed');
					if (status === "completed") {
						// $("#completed-tasks").append($("#task-" + taskId));
					$('#task-'+taskId).closest('li').appendTo("#completed-tasks")
					} else {
						// $("#open-tasks").append($("#task-" + taskId));
						$('#task-'+taskId).closest('li').appendTo("#open-tasks")

					}
					updateCounters();

				},
				error: function () {
					console.log("Status didn't update")
				}
			})

		})


	})


</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>


</html>
