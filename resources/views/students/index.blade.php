<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2 class="mb-4 text-primary fw-bold">Student Dashboard</h2>

        <hr class="my-4">

        <form action="/students" method="GET" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control shadow-sm" placeholder="Search name, email, course or year..." value="{{ request('search') }}">
                        <button class="btn btn-primary shadow-sm" type="submit">Search</button>
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="per_page" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>Show 5 per page</option>
                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Show All</option>
                    </select>
                </div>
                
                @if(request('search') || request('per_page'))
                    <div class="col-md-2">
                        <a href="/students" class="btn btn-outline-secondary shadow-sm w-100">Reset</a>
                    </div>
                @endif
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th> 
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th class="text-center">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                            <td class="fw-bold">{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{$student->course}}</td>
                            <td>Year {{ $student->year }}</td>
                            <td class="text-center">
                                <a href="/students/{{ $student->id }}/edit" class="btn btn-warning btn-sm shadow-sm">Edit</a>

                                <form action="/students/{{ $student->id }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Delete this student?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No student records match your search.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            <a href="/students/create" class="btn btn-success fw-bold shadow-sm">Add Student<br></a>
        </div>
    </div>

    <br>

            <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm h-100 bg-primary text-white border-0">
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                        <h6 class="text-uppercase small fw-bold">Total Students</h6>
                        <h2 class="display-4 fw-bold">{{ \App\Models\Student::count() }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-header bg-white border-0 fw-bold">Students per Course Distribution</div>
                    <div class="card-body" style="height: 250px; display: flex; justify-content: center;">
                        <canvas id="coursePieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

<script>
    // Wrapping in quotes helps VS Code ignore the special Laravel characters
    const rawLabels = JSON.parse('{!! json_encode($chartLabels) !!}');
    const rawValues = JSON.parse('{!! json_encode($chartValues) !!}');

    const ctx = document.getElementById('coursePieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: rawLabels,
            datasets: [{
                label: 'Total Students',
                data: rawValues,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ],
                hoverOffset: 15,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>