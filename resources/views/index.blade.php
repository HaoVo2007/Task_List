@extends('layout.app')

@section('content')
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: black" id="exampleModalLabel">New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="task_id">
                        <div class="mb-3">
                            <label for="title" class="col-form-label">Task : </label>
                            <input class="form-control" name="task" value="{{ old('task') }}" type="text"
                                id="task">
                        </div>

                        <div class="form-group mb-3">
                            <label for="exampleFormControlSelect1" class="col-form-label">Category Task : </label>
                            <select class="form-control" id="category-task">
                                <option value="<i class=&quot;fas fa-book-open fa-lg&quot;></i>">Education</option>

                                <option value="<i class=&quot;fas fa-dumbbell fa-lg&quot;></i>">Fitness</option>

                                <option value="<i class=&quot;fas fa-paint-brush fa-lg&quot;></i>">Creativity</option>

                                <option value="<i class=&quot;fas fa-hands-helping fa-lg&quot;></i>">Volunteering</option>

                                <option value="<i class=&quot;fas fa-user-tie fa-lg&quot;></i>">Career</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description-long" class="col-form-label">Priority level : </label>
                            <select class="form-control" id="priority-level">

                                <option value="0">Low priority</option>

                                <option value="1">Middle priority</option>

                                <option value="2">High priority</option>

                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="add-task" type="button" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
    <section class="vh-100 gradient-custom-2">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-12 col-xl-10">
                    <div class="card mask-custom">
                        <div class="card-body p-4 text-white">
                            <div class="text-center pt-3 pb-2">
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-todo-list/check1.webp"
                                  alt="Check" width="60">
                                <h2 class="my-4">Task List</h2>
                            </div>
                            <button type="button" id="btn-new-task" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">NewTask</button>
                            <table class="table text-white mb-0">
                                <thead>
                                    <tr>
                                      <th scope="col">Category Task</th>
                                      <th scope="col">Task</th>
                                      <th scope="col">Priority</th>
                                      <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="tasks-list"></tbody>
                            </table>
                            <ul class="pagination justify-content-end mt-3" id="pagination"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('session-content')
    <script>
        function fetchTasks(page = 1) {
            $.ajax({
                url: 'api/task?page=' + page,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    var tasksList = $('#tasks-list');
                    tasksList.empty();
                    $.each(response.data.data, function(index, task) {
                        let priorityValue = parseInt(task.priority, 10);
                        let priorityText;
                        let badgeClass;
                        switch (priorityValue) {
                            case 0:
                                priorityText = 'Low priority';
                                badgeClass = 'bg-success';
                                break;
                            case 1:
                                priorityText = 'Middle priority';
                                badgeClass = 'bg-warning';
                                break;
                            case 2:
                                priorityText = 'High priority';
                                badgeClass = 'bg-danger';
                                break;
                            default:
                                priorityText = 'Unknown priority';
                                badgeClass = '';
                        }

                        tasksList.append(
                            `
                                <tr class="fw-normal">
                                    <th>
                                        ${task.category}
                                    </th>
                                    <td class="align-middle">
                                        <span data-task-id="${task.id}" class="task-link ${task.complete == 1 ? 'completed-task' : ''}">${task.task}</span>
                                    </td>
                                    <td class="align-middle">
                                        <h6 class="mb-0"><span class="badge ${badgeClass}">${priorityText}</span></h6>
                                    </td>
                                    <td class="align-middle">
                                        <a href="#!" data-mdb-tooltip-init title="Done" data-task-id="${task.id}" class="task-checkbox">
                                            <i class="fas fa-check fa-lg text-success me-3"></i>
                                        </a><a href="#!" data-mdb-tooltip-init title="Remove" data-task-id="${task.id}" class="task-delete">
                                            <i class="fas fa-trash-alt fa-lg text-warning"></i>
                                        </a>
                                    </td>

                                </tr>
                            `
                        );
                    });

                    $('.task-link').on('click', function(e) {
                        e.preventDefault();
                        var taskId = $(this).data('task-id');
                        showModal(taskId);
                    });

                    $('.task-delete').on('click', function(e) {
                        e.preventDefault();
                        var taskId = $(this).data('task-id');
                        swal({
                                title: "Are you sure?",
                                text: "Once deleted, you will not be able to recover this imaginary file!",
                                icon: "warning",
                                buttons: true,
                                dangerMode: true,
                            })
                            .then((willDelete) => {
                                if (willDelete) {
                                    deleteTask(taskId);
                                    swal("Poof! Your imaginary file has been deleted!", {
                                        icon: "success",
                                    });
                                }
                            });
                    })

                    var pagination = $('#pagination');
                    pagination.empty();

                    var currentPage = response.data.current_page;
                    var lastPage = response.data.last_page;

                    // Nút Previous
                    if (currentPage > 1) {
                        pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (
                                currentPage - 1) +
                            '">Previous</a></li>');
                    }

                    // Nút số trang
                    for (var i = 1; i <= lastPage; i++) {
                        pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' +
                            i + '">' + i +
                            '</a></li>');
                    }

                    // Nút Next
                    if (currentPage < lastPage) {
                        pagination.append('<li class="page-item"><a href="#" class="page-link" data-page="' + (
                                currentPage + 1) +
                            '">Next</a></li>');
                    }


                    // Xử lý khi người dùng nhấn vào số trang
                    $('.page-link').on('click', function(e) {
                        e.preventDefault();
                        var selectedPage = $(this).data('page');
                        fetchTasks(selectedPage); // Gọi lại hàm fetchTasks với trang đã chọn
                    });
                    
                    $('.task-checkbox').on('click', function() {
                        var taskId = $(this).data('task-id');
                        changeComplete(taskId, currentPage);
                    })

                },
                error: function(xhr, status, error) {
                    swal("Something Went Wrong", "", "error");
                }
            });
        }

        function showModal(taskId) {
            $.ajax({
                url: 'api/task/' + taskId,
                type: 'GET',
                dataType: 'JSON',
                success: function(response) {
                    var task = response.data;
                    $('#task').val(task.task);
                    $('#category-task').val(task.category);
                    $('#priority-level').val(task.priority);

                    $('#exampleModal').modal('show');

                    $('#add-task').text('Update').off('click').on('click', function() {
                        updateTask(task.id);
                    });
                },

                error: function(xhr, status, error) {
                    swal("Something Went Wrong", "", "error");
                }
            })
        }

        function updateTask(taskId) {
            var updateTask = {
                task: $('#task').val(),
                category: $('#category-task').val(),
                priority: $('#priority-level').val(),
            }

            $.ajax({
                url: 'api/task/' + taskId,
                type: 'PUT',
                dataType: 'json',
                data: updateTask,
                success: function(response) {
                    $('#exampleModal').modal('hide');
                    swal(response.message, "", response.status);
                    fetchTasks()
                },
                error: function(xhr, status, error) {
                    swal("Something Went Wrong", "", "error");
                }
            })
        }

        function deleteTask(taskId) {
            $.ajax({
                url: 'api/task/' + taskId,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    swal(response.message, "", response.status);
                    fetchTasks()
                },
                error: function(xhr, status, error) {
                    swal("Something Went Wrong", "", "error");
                }
            })
        }

        function changeComplete(taskId, page) {
            $.ajax({
                url: 'api/task/' + taskId + '/toggle-completion',
                type: 'PUT',
                success: function(response) {
                    swal(response.message, "", "success");
                    fetchTasks(page);
                },
                error: function(xhr, status, error) {
                    swal("Something went wrong", "", "error");
                }
            })
        }

        $(document).ready(function() {
            fetchTasks(page = 1);

            $('#btn-new-task').on('click', function() {
                $('#task_id').val('');
                $('#task').val('');
                $('#category-task').val('');
                $('#priority-level').val('');
            })

            $('#add-task').click(function() {
                const task = $('#task').val();
                const category_task = $('#category-task').val();
                const priority_level = $('#priority-level').val();
                $.ajax({
                    url: 'api/task',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        task: task,
                        category: category_task,
                        priority: priority_level,
                    }),

                    success: function(response) {
                        swal(response.message, "", response.status);
                        $('#exampleModal').modal('hide');
                        fetchTasks();
                    },

                    error: function(xhr, status, error) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            const firstErrorKey = Object.keys(errors)[0];
                            const firstErrorMessage = errors[firstErrorKey][0];

                            swal(firstErrorMessage, "", "error");

                        } else {
                            swal("Something Went Wrong", "", "error");
                        }
                    }
                })
            })
        });
    </script>
@endsection
