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


                {{-- Final Exam Estimator --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white fw-bold border-0 pt-3 text-muted small text-uppercase d-flex justify-content-between align-items-center">
        <span>🎯 Final Exam Estimator</span>
        <small class="text-primary">Remaining 50% of total marks</small>
    </div>
    <div class="card-body">

        {{-- What they need to pass --}}
        <div class="row text-center mb-3">
            @php
                $currentPts = $student->current_progress ?? 0;
                $targets    = [
                    ['label' => 'Pass (50%)',        'target' => 50,  'color' => 'success'],
                    ['label' => 'Credit (60%)',       'target' => 60,  'color' => 'primary'],
                    ['label' => 'Distinction (75%)',  'target' => 75,  'color' => 'warning'],
                    ['label' => 'High Dist. (85%)',   'target' => 85,  'color' => 'danger'],
                ];
            @endphp

            @foreach($targets as $t)
                @php
                    $needed     = $t['target'] - $currentPts;   // points needed from final exam
                    $neededPct  = round(($needed / 50) * 100);  // as % of final exam (out of 50)
                    $feasible   = $neededPct <= 100;
                @endphp
                <div class="col-3">
                    <div class="border rounded-3 p-2 {{ $feasible ? '' : 'opacity-50' }}">
                        <small class="text-muted d-block" style="font-size:.72rem;">{{ $t['label'] }}</small>
                        <span class="fw-bold text-{{ $feasible ? $t['color'] : 'secondary' }}" style="font-size:1.1rem;">
                            {{ $feasible ? $neededPct . '%' : 'N/A' }}
                        </span>
                        <small class="text-muted d-block" style="font-size:.7rem;">
                            {{ $feasible ? 'needed in exam' : 'not achievable' }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>

        <hr class="my-3">

        {{-- Simulator --}}
        <div class="row align-items-center">
            <div class="col-md-5">
                <label class="text-muted small fw-bold d-block mb-1">
                    Simulate: If I score <span id="simPct" class="text-primary fw-bold">50</span>% in final exam
                </label>
                <input type="range" class="form-range" id="examSlider"
                       min="0" max="100" value="50"
                       oninput="simulateExam(this.value,
                           {{ $currentPts }})">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">0%</small>
                    <small class="text-muted">100%</small>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <small class="text-muted d-block text-uppercase" style="font-size:.72rem;">Predicted Final Score</small>
                <h2 class="fw-bold mb-0" id="simTotal">—</h2>
                <small class="text-muted">out of 100</small>
            </div>
            <div class="col-md-3 text-center">
                <div id="simGrade" class="badge px-3 py-2 fs-6">—</div>
                <small class="text-muted d-block mt-1" id="simMessage"></small>
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

    {{-- AI Report Button --}}
    <button id="generateReportBtn"
            onclick="generateReport({{ $student->id }})"
            class="btn btn-primary px-4 fw-bold shadow-sm">
        <span id="reportBtnText">🤖 AI Report</span>
        <span id="reportBtnSpinner" class="d-none">
            <span class="spinner-border spinner-border-sm me-1"></span> Generating...
        </span>
    </button>

    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning px-4 fw-bold shadow-sm">Edit Info</a>
    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">Back to List</a>
</div>

{{-- AI Report Modal --}}
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white"
                 style="background:linear-gradient(135deg,#0d6efd,#6610f2)">
                <h5 class="modal-title fw-bold">
                    🤖 AI Academic Report — {{ $student->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="reportContent" style="line-height:1.8;white-space:pre-wrap;font-size:.92rem;"></div>
            </div>
            <div class="modal-footer bg-light">
                <small class="text-muted me-auto">Generated by Claude AI — for advisory purposes only</small>
                <button onclick="printReport()" class="btn btn-outline-primary btn-sm">🖨️ Print</button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

// ── Final Exam Simulator ──────────────────────────────
function simulateExam(examPct, currentPts) {
    document.getElementById('simPct').textContent = examPct;

    // Final exam is worth 50 points total
    const examPoints = (examPct / 100) * 50;
    const total      = currentPts + examPoints;
    const rounded    = Math.round(total * 10) / 10;

    document.getElementById('simTotal').textContent = rounded + '%';

    // Grade + message
    let grade, color, gpa;
    if      (total >= 85) { grade = 'A';  color = 'bg-success';   gpa = '3.67 - 4.00'; }
    else if (total >= 80) { grade = 'A-'; color = 'bg-success';   gpa = '3.34 - 3.66'; }
    else if (total >= 75) { grade = 'B+'; color = 'bg-primary';   gpa = '3.01 - 3.33'; }
    else if (total >= 71) { grade = 'B';  color = 'bg-primary';   gpa = '2.67 - 3.00'; }
    else if (total >= 68) { grade = 'B-'; color = 'bg-primary';   gpa = '2.34 - 2.66'; }
    else if (total >= 64) { grade = 'C+'; color = 'bg-info';      gpa = '2.01 - 2.33'; }
    else if (total >= 61) { grade = 'C';  color = 'bg-info';      gpa = '1.67 - 2.00'; }
    else if (total >= 58) { grade = 'C-'; color = 'bg-warning';   gpa = '1.31 - 1.66'; }
    else if (total >= 54) { grade = 'D+'; color = 'bg-warning';   gpa = '1.01 - 1.30'; }
    else if (total >= 50) { grade = 'D';  color = 'bg-warning';   gpa = '0.10 - 1.00'; }
    else                  { grade = 'F';  color = 'bg-danger';    gpa = '0.00'; }

    const gradeEl = document.getElementById('simGrade');
    gradeEl.textContent  = grade;
    gradeEl.className    = `badge px-3 py-2 fs-6 ${color}`;
    document.getElementById('simMessage').textContent = msg;
}

// Run once on page load so the slider shows a result immediately
simulateExam(50, {{ $student->current_progress ?? 0 }});

// ── AI Report Generator ───────────────────────────────
async function generateReport(studentId) {
    const btn     = document.getElementById('generateReportBtn');
    const btnText = document.getElementById('reportBtnText');
    const spinner = document.getElementById('reportBtnSpinner');
    const content = document.getElementById('reportContent');

    btn.disabled = true;
    btnText.classList.add('d-none');
    spinner.classList.remove('d-none');

    try {
        const res = await fetch(`/students/${studentId}/generate-report`, {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });

        if (!res.ok) {
            const err = await res.text();
            alert('Server error ' + res.status + ': ' + err.substring(0, 200));
            return;
        }

        const data = await res.json();

        if (data.success) {
            content.textContent = data.report;
            new bootstrap.Modal(document.getElementById('reportModal')).show();
        } else {
            alert('Error: ' + (data.error ?? 'Unknown error'));
        }

    } catch (err) {
        alert('Failed: ' + err.message);
    } finally {
        btn.disabled = false;
        btnText.classList.remove('d-none');
        spinner.classList.add('d-none');
    }
}

// ── Print Report ──────────────────────────────────────
function printReport() {
    const content = document.getElementById('reportContent').textContent;
    const win = window.open('', '_blank');
    win.document.write(`
        <html><head><title>Academic Report</title>
        <style>
            body { font-family: Georgia, serif; padding: 40px; line-height: 1.8; max-width: 800px; margin: 0 auto; }
            h2   { color: #1a237e; border-bottom: 2px solid #1a237e; padding-bottom: 8px; }
            p    { margin-bottom: 16px; text-align: justify; }
        </style></head>
        <body>
            <h2>Academic Report — {{ $student->name }}</h2>
            <p>
                <strong>Course:</strong> {{ $student->course }} &nbsp;|&nbsp;
                <strong>Year:</strong> {{ $student->year }} &nbsp;|&nbsp;
                <strong>Risk:</strong> {{ $student->risk_level }}
            </p>
            <hr>
            ${content.replace(/\n\n/g, '</p><p>').replace(/\n/g, '<br>')}
        </body></html>
    `);
    win.document.close();
    win.print();
}
</script>


    @include('partials.chatbot')
</body>
</html>