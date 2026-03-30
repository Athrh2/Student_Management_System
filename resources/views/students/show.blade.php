@php
    $attendanceWeight = ($student->attendance_rate ?? 0) * 0.10;
    $testWeight       = ($student->test_score ?? 0) * 0.15;
    $assignmentWeight = ($student->assignment_score ?? 0) * 0.25;

    $currentTotal =  $attendanceWeight + $testWeight + $assignmentWeight;
    
    // 2. Risk Prediction is the probability of failing (100 minus the earned points)
    $riskPercent = 100 - ( $currentTotal * 2);
    $riskPercent = max(0, min(100, $riskPercent));

    // 3. Determine Risk Color for the UI
    $riskColor = $student->risk_level == 'High' ? 'danger' : ($student->risk_level == 'Medium' ? 'warning' : 'success');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .modern-nav-container { gap: 20px; }
        .modern-menu .nav-link { color: #4b5563 !important; font-weight: 500; position: relative; transition: color 0.2s ease; }
        .modern-menu .nav-link.active { color: #0d6efd !important; font-weight: 600; }
        .modern-menu .nav-link.active::after { content: ''; position: absolute; bottom: 0; left: 15px; right: 15px; height: 3px; background-color: #0d6efd; border-radius: 2px; }
        .btn-modern-action { background-color: #f1f5f9; color: #dc3545 !important; border: 2px solid #e2e8f0; border-radius: 50px !important; transition: all 0.2s ease; }
        .btn-modern-action:hover { background-color: #dc3545; color: #ffffff !important; }
    </style>
</head>
<body class="p-4 bg-light">
    <div class="container-fluid mt-4">
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <h1 class="text-primary fw-bold display-5 mb-0" style="letter-spacing: -1.5px;">Student Profile</h1>
            <div class="d-flex align-items-center modern-nav-container">
                <ul class="nav modern-menu me-4">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.index') ? 'active' : '' }}" href="{{ route('students.index') }}">Student List</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.visualize') ? 'active' : '' }}" href="{{ route('students.visualize') }}">Analytics</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('students.admin') ? 'active' : '' }}" href="{{ route('students.admin') }}">Admin</a></li>
                </ul>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button type="submit" class="btn btn-modern-action px-4 py-2 fw-bold">Logout</button>
                </form>
            </div>
        </div>

        <hr class="mt-0 mb-4 opacity-25">

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-70 text-center p-4">
                    <div class="mb-3">
                        @if($student->photo && file_exists(public_path('uploads/students/' . $student->photo)))
                            <img src="{{ asset('uploads/students/' . $student->photo) }}" class="rounded-circle shadow-sm border" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=150&background=0D6EFD&color=fff" class="rounded-circle shadow-sm border">
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1">{{ $student->name }}</h3>
                    <p class="text-muted mb-2 small">{{ $student->email }}</p>
                    <span class="badge bg-{{ $riskColor }} px-3 py-2 rounded-pill">{{ $student->risk_level }} Risk</span>
                    <a href="{{ route('students.pdf', $student->id) }}" class="btn btn-outline-danger btn-sm mt-3">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF Report
                    </a>
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

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold border-0 pt-3 text-muted small text-uppercase">Academic Performance (Weighted)</div>
                    <div class="card-body pt-0">
                        <div class="row text-center">
                            <div class="col-4">
                                <label class="text-muted small d-block">Attendance (10%)</label>
                                <span class="h5 fw-bold">{{ $student->attendance_rate }}%</span>
                                <p class="text-primary small mb-0">+{{ $student->attendance_rate * 0.1 }} pts</p>
                            </div>
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Test (15%)</label>
                                <span class="h5 fw-bold">{{ $student->test_score }}%</span>
                                <p class="text-primary small mb-0">+{{ $student->test_score * 0.15 }} pts</p>
                            </div>
                            <div class="col-4 border-end">
                                <label class="text-muted small d-block">Assignment (25%)</label>
                                <span class="h5 fw-bold">{{ $student->assignment_score }}%</span>
                                <p class="text-primary small mb-0">+{{ $student->assignment_score * 0.25 }} pts</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold border-0 pt-3 text-muted small text-uppercase">Academic Forecast</div>
                    <div class="card-body pt-0">
                        <div class="row text-center">
                            <div class="col-6 border-end">
                                <small class="text-uppercase text-muted d-block">Current Marks (50%)</small>
                                <h4 class="fw-bold">{{ $student->current_progress ?? '0' }}%</h4>
                            </div>
                            <div class="col-6">
                                <small class="text-uppercase text-muted d-block">Final Prediction (100%)</small>
                                <h4 class="text-primary fw-bold">{{ $student->forecasted_total ?? '0' }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>    



                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="card-header bg-{{ $riskColor }} text-white fw-bold py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Academic Risk Analysis</span>
                            <span class="badge bg-white text-{{ $riskColor }}">
                                Risk Prediction: {{ number_format($riskPercent, 1) }}%
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <p class="text-muted small text-uppercase fw-bold mb-1 text-dark mt-3">Risk Level</p>
                                <h3 class="fw-bold">
                                    @if($student->risk_level == 'High')
                                        <span class="text-danger">High</span>
                                    @elseif($student->risk_level == 'Medium')
                                        <span class="text-warning">Medium</span>
                                    @else
                                       <span class="text-success">Low</span>
                                    @endif
                                </h3>

                                <p class="text-muted small mb-1">Failure Probability</p>
                                <h4 class="fw-bold text-{{ $riskColor }}">{{ number_format($riskPercent, 1) }}%</h4>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar bg-{{ $riskColor }}" style="width: {{ $riskPercent }}%"></div>
                                </div>

                            </div>
                            <div class="col-4 border-end">
                                <h6 class="fw-bold text-dark mb-3 mt-3">Suggestion Plan</h6>
                                <ul class="mb-0 small">
                                    @if($student->risk_level == 'High')
                                        <li class="mb-2"><strong>Schedule Academic Counseling:</strong> Urgent meeting required.</li>
                                        <li class="mb-2"><strong>Extra Assignments:</strong> Assign remedial modules.</li>
                                        <li><strong>Attendance Monitoring:</strong> Daily check-ins required.</li>
                                    @elseif($student->risk_level == 'Medium')
                                        <li class="mb-2"><strong>Peer Mentoring:</strong> Connect with a student mentor.</li>
                                        <li><strong>Optional Workshop:</strong> Recommend "Study Skills" seminar.</li>
                                    @else
                                        <li class="mb-2"><strong>Advanced Track:</strong> Suggest participation in the Dean's List project group.</li>
                                        <li><strong>Consistency:</strong> Maintain current study patterns.</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-4">
                                <h6 class="fw-bold text-dark mb-3 mt-3">Explanation</h6>
                                <div class="px-2">
                                    <p class="mb-0 small" style="line-height: 1.6; text-align: justify;">
                                        {{ $student->risk_explanation }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning px-4 fw-bold shadow-sm">Edit Info</a>
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">Back to List</a>
                </div>
            </div>
        </div>
    </div>
    @include('partials.chatbot')
</body>
</html>