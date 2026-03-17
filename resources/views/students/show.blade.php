<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Detail - {{ $student->name }}</title>
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
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-primary fw-bold display-5 mb-0" style="letter-spacing: -1.5px;">Student Profile</h1>
            </div>
            <div class="d-flex align-items-center modern-nav-container">
                <ul class="nav modern-menu me-4">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">Student List</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.visualize') ? 'active' : '' }}" href="{{ route('students.visualize') }}">Analytics</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.admin') ? 'active' : '' }}" href="{{ route('students.admin') }}">Admin</a></li>
                </ul>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-modern-action px-4 py-2 fw-bold">Logout</button>
                </form>
            </div>
        </div>

        <hr class="mt-0 mb-4 opacity-25">

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 text-center p-4">
                    <div class="mb-3">
                        @if($student->photo && file_exists(public_path('uploads/students/' . $student->photo)))
                            <img src="{{ asset('uploads/students/' . $student->photo) }}" class="rounded-circle shadow-sm border" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=150&background=0D6EFD&color=fff" class="rounded-circle shadow-sm border">
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1">{{ $student->name }}</h3>
                    <p class="text-muted mb-0">{{ $student->email }}</p>
                    <div class="mt-3">
                        @if($student->risk_level == 'High')
                            <span class="badge bg-danger px-3">High Risk</span>
                        @elseif($student->risk_level == 'Medium')
                            <span class="badge bg-warning text-dark px-3">Medium Risk</span>
                        @else
                            <span class="badge bg-success px-3">Low Risk</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold border-0 pt-3 text-muted small text-uppercase">Basic Information</div>
                    <div class="card-body pt-0">
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Student ID</label>
                                <span class="h5 fw-bold">{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Course</label>
                                <span class="h5 fw-bold">{{ $student->course }}</span>
                            </div>
                            <div class="col-4">
                                <label class="text-muted small d-block">Academic Year</label>
                                <span class="h5 fw-bold">Year {{ $student->year }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold border-0 pt-3 text-muted small text-uppercase">Academic Performance</div>
                    <div class="card-body pt-0">
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Assignment</label>
                                <span class="h5 fw-bold">{{ $student->assignment_score }}%</span>
                            </div>
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Midterm</label>
                                <span class="h5 fw-bold">{{ $student->midterm_score }}%</span>
                            </div>
                            <div class="col-4">
                                <label class="text-muted small d-block">Attendance</label>
                                <span class="h5 fw-bold text-primary">{{ $student->attendance_rate }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mt-4 overflow-hidden">
                    <div class="card-header {{ $student->risk_level == 'High' ? 'bg-danger' : ($student->risk_level == 'Medium' ? 'bg-warning' : 'bg-success') }} text-white fw-bold py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Insights & Recommendations</span>
                            <span class="badge bg-white {{ $student->risk_level == 'High' ? 'text-danger' : ($student->risk_level == 'Medium' ? 'text-warning' : 'text-success') }}">
                                Risk Score: 
                                @if($student->risk_level == 'High') 82% @elseif($student->risk_level == 'Medium') 45% @else 12% @endif
                            </span>
                        </div>
                    </div>
                    <div class="card-body bg-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="fw-bold text-dark mb-3">Action Plan for {{ $student->name }}:</h6>
                                <ul class="mb-0">
                                    @if($student->risk_level == 'High')
                                        <li class="mb-2"><strong>Schedule Academic Counseling:</strong> Urgent meeting required with the course coordinator.</li>
                                        <li class="mb-2"><strong>Extra Assignments:</strong> Assign remedial modules to boost the Midterm gap.</li>
                                        <li><strong>Attendance Monitoring:</strong> Daily check-ins required for the next 2 weeks.</li>
                                   @elseif($student->risk_level == 'Medium')
                                        <li class="mb-2"><strong>Peer Mentoring:</strong> Connect student with a Year {{ $student->year }} mentor.</li>
                                        <li><strong>Optional Workshop:</strong> Recommend the upcoming "Study Skills" seminar.</li>
                                    @else
                                        <li class="mb-2"><strong>Advanced Track:</strong> Suggest participation in the Dean's List project group.</li>
                                        <li><strong>Maintain Consistency:</strong> Continue current study pattern.</li>
                                    @endif
                                </ul>
                            </div>

                            <div class="col-md-4 text-center border-start">
                                <p class="text-muted small mb-1">Prediction Status</p>
                                <h4 class="fw-bold {{ $student->risk_level == 'High' ? 'text-danger' : 'text-success' }}">
                                    {{ $student->risk_level == 'High' ? 'At Risk' : 'On Track' }}
                                </h4>
                            <div class="progress mt-2" style="height: 10px;">
                                @php 
                                    $val = $student->risk_level == 'High' ? 82 : ($student->risk_level == 'Medium' ? 45 : 12);
                                @endphp
                            <div class="progress-bar {{ $student->risk_level == 'High' ? 'bg-danger' : ($student->risk_level == 'Medium' ? 'bg-warning' : 'bg-success') }}" 
                                role="progressbar" style="width: {{ $val }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning px-4 fw-bold shadow-sm">
                            <i class="fas fa-edit me-1"></i> Edit Profile
                        </a>
                        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">
                            Back to List
                        </a>
                    </div>

                    <p class="text-center text-muted mt-5 small">
                        Student Management System &copy; 2026
                    </p>
</body>
</html>