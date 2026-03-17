<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary fw-bold mb-0 display-5">Admin Control</h1>
            
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


        <div class="row g-4 mb-4">
            <div class="col-md-2">
                <div class="card shadow-sm border-0 bg-primary h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-dark">
                        <h6 class="text-uppercase small fw-bold">Total Students</h6>
                        <h2 class="display-5 fw-bold mb-0">{{ $totalStudents }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow-sm border-0 bg-info h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-dark">
                        <h6 class="text-uppercase small fw-bold">Courses</h6>
                        <h2 class="display-5 fw-bold mb-0">{{ $totalCourses }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow-sm border-0 bg-danger h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-dark">
                        <h6 class="text-uppercase small fw-bold">High Risk</h6>
                        <h2 class="display-5 fw-bold mb-0">{{ $highRisk ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow-sm border-0 bg-warning h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-dark">
                        <h6 class="text-uppercase small fw-bold">Medium Risk</h6>
                        <h2 class="display-5 fw-bold mb-0">{{ $mediumRisk ?? 0 }}</h2>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="card shadow-sm border-0 bg-success h-100">
                    <div class="card-body d-flex flex-column justify-content-center text-center text-dark">
                        <h6 class="text-uppercase small fw-bold">Low Risk</h6>
                        <h2 class="display-5 fw-bold mb-0">{{ $lowRisk ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div> <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Recently Added Students</div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Course</th>
                                    <th>Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $recent)
                                <tr>
                                    <td>{{ $recent->name }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $recent->course }}</span></td>
                                    <td class="small text-muted">{{ $recent->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>