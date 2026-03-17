<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    /* 1. Overall Container adjustment */
    .modern-nav-container {
        gap: 20px;
    }

    /* 2. Style for the Minimalist Menu Links (List, Analytics, Admin) */
    .modern-menu {
        border: none !important;
        background: none !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    .modern-menu .nav-link {
        color: #4b5563 !important; /* Muted gray text like Beyond */
        font-weight: 500;
        padding: 10px 15px !important;
        background: none !important;
        border: none !important;
        position: relative; /* Needed for the underline */
        transition: color 0.2s ease;
    }

    /* Standard Hover State (No background change) */
    .modern-menu .nav-link:hover {
        color: #1a1a1a !important; /* Darker on hover */
    }

    /* 3. The Minimalist Underline for the Active Page */
    .modern-menu .nav-link.active {
        color: #0d6efd !important; /* Bootstrap primary blue */
        font-weight: 600;
    }

    /* This adds the blue line just like the "About" link */
    .modern-menu .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 15px; /* Aligns with the padding of the text */
        right: 15px; /* Aligns with the padding of the text */
        height: 3px;
        background-color: #0d6efd; /* Blue line */
        border-radius: 2px;
    }

    /* Remove the standard active button background from pills */
    .modern-menu .nav-link.active:focus {
        background: none !important;
        box-shadow: none !important;
    }

    /* 4. Style for the Custom Logout Button (like Sign Up) */
    .btn-modern-action {
        background-color: #f1f5f9; /* Soft off-white/gray background */
        color: #dc3545 !important; /* Use red for Logout */
        border: 2px solid #e2e8f0; /* Soft border */
        border-radius: 50px !important; /* Full rounded corners like Sign Up */
        transition: all 0.2s ease;
    }

    /* Hover effect for the button */
    .btn-modern-action:hover {
        background-color: #dc3545; /* Turns red on hover */
        color: #ffffff !important;
        border-color: #dc3545;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

</head>
<body class="p-4 bg-light">
    <div class="mb-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="text-primary fw-bold display-5 mb-0" style="letter-spacing: -1.5px;">Student Dashboard</h1>
            <h6 class="text-secondary mt-0 fw-medium">Welcome, {{ Auth::user()->name }}.</h6>
        </div>

        <div class="d-flex align-items-center modern-nav-container">
            <ul class="nav modern-menu me-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">
                        Student List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('students.visualize') ? 'active' : '' }}" href="{{ route('students.visualize') }}">
                        Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('students.admin') ? 'active' : '' }}" href="{{ route('students.admin') }}">
                        Admin
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center modern-nav-container">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-modern-action px-4 py-2 fw-bold">Logout</button>
            </form>
        </div>
    </div>
</div>

    <hr class="mt-0 mb-4 opacity-25">

        <form action="{{ route('students.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search name, email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-1">
                <select name="gender" class="form-select">
                    <option value="">Gender</option>
                    <option value="Male" {{ request('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ request('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="col-md-1">
                <select name="year" class="form-select">
                    <option value="">Year</option>
                    <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>Year 1</option>
                    <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>Year 2</option>
                    <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>Year 3</option>
                    <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>Year 4</option>
                </select>
            </div>

            <div class="col-md-auto d-flex gap-1"> 
                <button type="submit" class="btn btn-primary btn-sm px-5">Filter</button>
    
                @if(request('search') || request('gender') || request('year') || request('per_page'))
                    <a href="/students" class="btn btn-outline-secondary btn-sm px-5">Reset</a>
                @endif
            </div>

            <div class="col-md-3">
                <select name="per_page" class="form-select shadow-sm" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page') == '5' ? 'selected' : '' }}>Show 5 per page</option>
                    <option value="20" {{ request('per_page') == '20' ? 'selected' : '' }}>Show 20 per page</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Show All</option>
                </select>                
            </div>
                
        </form>
    
        </div>
            </form>
                <div>
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-3" style="border-left: 5px solid #0dcaf0 !important;">
                        <div class="me-3">
                            <span class="fs-4">📊</span>
                        </div>
                    <div>
                        <span class="fw-bold">Table View:</span> 
                            Sorting by <span class="badge bg-white text-dark border">{{ ucfirst(request('sort', 'name')) }}</span> 
                            in <span class="badge bg-white text-dark border">{{ strtoupper(request('direction', 'asc')) }}</span> order.
                            @if(request('search'))
                              | Filtered by: <span class="badge bg-warning text-dark">"{{ request('search') }}"</span>
                            @endif
                    </div>
                </div> 

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
                            <th style="width: 1%;" class="text-nowrap text-center">
                                <div class="d-flex align-items-center justify-content-between ">
                                    <span>ID</span>
                                    <div class="d-flex flex-column ms-2 " style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'id' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'id' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>
                            
                            <th>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Name</span>
                                    <div class="d-flex flex-column ms-2" style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'name' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'name' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>

                            <th>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Email</span>
                                    <div class="d-flex flex-column ms-2" style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'email' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'email', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'email' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>

                            <th>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Gender</span>
                                    <div class="d-flex flex-column ms-2" style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'gender', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'gender' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'gender', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'gender' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>

                            <th>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Course</span>
                                    <div class="d-flex flex-column ms-2" style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'course', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'course' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'course', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'course' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>

                            <th>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Year</span>
                                    <div class="d-flex flex-column ms-2" style="font-size: 0.7rem; line-height: 1;">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'year', 'direction' => 'asc']) }}" 
                                            class="{{ (request('sort') == 'year' && request('direction') == 'asc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▲</a>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'year', 'direction' => 'desc']) }}" 
                                            class="{{ (request('sort') == 'year' && request('direction') == 'desc') ? 'text-warning' : 'text-white-50' }} text-decoration-none">▼</a>
                                    </div>
                                </div>
                            </th>

                            <th style="width: 1%;" class="text-nowrap">Risk Status</th>

                            <th class="text-center">Actions</th>

                            <div class="container-fluid mb-4">
            </div>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($students as $student)
                        <tr>
                            <td>{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td class="fw-bold">{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->course }}</td>
                            <td>Year {{ $student->year }}</td>

                            <td>
                                @if($student->risk_level == 'High')
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                        🔴 High Risk
                                    </span>
                                @elseif($student->risk_level == 'Medium')
                                    <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                                        🟡 Medium Risk
                                        </span>
                                @else
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                        🟢 Low Risk
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="/students/{{ $student->id }}/edit" class="btn btn-warning btn-sm shadow-sm">Edit</a>

                               <!-- <form action="/students/{{ $student->id }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm shadow-sm" onclick="return confirm('Delete this student?')">Delete</button>
                                </form> -->
                                <button type="button" 
                                    class="btn btn-danger btn-sm shadow-sm btn-delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal" 
                                    data-id="{{ $student->id }}" 
                                    data-name="{{ $student->name }}">
                                    Delete
                                </button>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No student records match your search.</td>
                        </tr>
                        @endforelse

                        <td>
                            <span class="fw-bold text-dark">{{ $student->name }}</span>
                            @if($student->risk_level == 'High')
                                <span class="ms-2 badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill small" style="font-size: 0.7rem;">
                                    <i class="fas fa-flag me-1"></i> FLAG
                                </span>
                            @endif
                        </td>

                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>

            <div class="d-inline-flex gap-2">
                <a href="/students/create" class="btn btn-success fw-bold shadow-sm">Add Student</a>

                <a href="/students/export" class="btn btn-outline-success fw-bold shadow-sm">
                    <i class="bi bi-download"></i> Export CSV
                </a>

                <a href=" {{route('students.bulkManage') }}" class="btn btn-outline-danger">Bulk Delete</a>
            </div>
        </div>
    </div>

    <br>

        <div class="row g-3 mb-4">
        
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p>Are you sure you want to delete <strong id="studentNameDisplay"></strong>?</p>
                            <p class="text-danger small"><i class="bi bi-exclamation-triangle"></i> This action cannot be undone.</p>
                        </div>
                       <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <form id="deleteForm" method="POST" action="">
                                @csrf
                                @method('DELETE')
                            <button type="submit" class="btn btn-danger">Yes, Delete Record</button>
                                 </form>
                         </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-5" style="border-radius: 15px; background-color: #fff5f5; border-left: 5px solid #dc3545 !important;">
    <div class="card-body d-flex align-items-center justify-content-between p-4">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                <i class="fas fa-exclamation-triangle text-danger fa-lg"></i>
            </div>
            <div>
                <h6 class="text-muted small text-uppercase fw-bold mb-1">AI Early Warning System</h6>
                <h4 class="fw-bold mb-0 text-danger">⚠ {{ $atRiskCount ?? 0 }} Students At Risk</h4>
            </div>
        </div>
        
        <button class="btn btn-danger rounded-pill px-4 fw-bold" type="button" 
                data-bs-toggle="collapse" data-bs-target="#atRiskList" aria-expanded="false">
            View Flagged List <i class="fas fa-chevron-down ms-2"></i>
        </button>
    </div>
</div>

<div class="collapse mt-3" id="atRiskList">
    <div class="card border-0 shadow-sm p-4" style="border-radius: 15px;">
        <h5 class="fw-bold text-dark mb-3">High Risk Students (Requires Attention)</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Course</th>
                        <th>Weighted Score</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($highRiskStudents as $atRisk)
                        <tr>
                            <td class="fw-bold">{{ $atRisk->name }}</td>
                            <td>{{ $atRisk->course }}</td>
                            <td>
                                @php
                                    $score = ($atRisk->attendance_rate * 0.1) + ($atRisk->assignment_score * 0.4) + ($atRisk->midterm_score * 0.5);
                                @endphp
                                <span class="text-danger fw-bold">{{ number_format($score, 1) }}%</span>
                            </td>
                            <td>
                                <a href="{{ route('students.show', $atRisk->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View Profile</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No high-risk students identified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

            <p class="text-center text-muted mt-4 small">
                Student Management System &copy; 2026
            </p>

<script>
    // Listen for when a delete button is clicked
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-id');
            const studentName = this.getAttribute('data-name');
            
            // Update the name in the modal text
            document.getElementById('studentNameDisplay').innerText = studentName;
            
            // Update the form action URL to /students/{id}
            const form = document.getElementById('deleteForm');
            form.action = '/students/' + studentId;
        });
    });
</script>

<script>
    document.getElementById('selectAll').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>