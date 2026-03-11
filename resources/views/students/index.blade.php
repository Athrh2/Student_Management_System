<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container">
        <h2 class="mb-4">Student List</h2>

        <form action="/students" method="GET" class="mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>

        <div class="col-md-3">
            <select name="per_page" class="form-select" onchange="this.form.submit()">
                <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>5 per page</option>
                <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10 per page</option>
                <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 per page</option>
                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Show All</option>
            </select>
        </div>
        
        @if(request('search') || request('per_page'))
            <div class="col-md-2">
                <a href="/students" class="btn btn-secondary w-100">Reset</a>
            </div>
        @endif
    </div>
</form>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

        <table class="table table-bordered table-striped bg-white">
            <thead class="table-dark">
                <tr>
                    <th>#</th> 
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Actions</th> 
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td>{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->course }}</td>
                    <td>{{ $student->year }}</td>
                    
                    <td>
                        <a href="/students/{{ $student->id }}/edit" class="btn btn-warning btn-sm">Edit</a>

                        <form action="/students/{{ $student->id }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this student?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-3">
            {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        
        <br>
        <a href="/students/create" class="btn btn-success">Add Student</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>