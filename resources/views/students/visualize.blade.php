<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .modern-nav-container { gap: 20px; }
        .modern-menu { border: none !important; background: none !important; padding: 0 !important; box-shadow: none !important; }
        .modern-menu .nav-link {
            color: #4b5563 !important;
            font-weight: 500;
            padding: 10px 15px !important;
            position: relative;
            transition: color 0.2s ease;
        }
        .modern-menu .nav-link.active { color: #0d6efd !important; font-weight: 600; }
        .modern-menu .nav-link.active::after {
            content: ''; position: absolute; bottom: 0; left: 15px; right: 15px; height: 3px;
            background-color: #0d6efd; border-radius: 2px;
        }
        .btn-modern-action {
            background-color: #f1f5f9; color: #dc3545 !important;
            border: 2px solid #e2e8f0; border-radius: 50px !important; transition: all 0.2s ease;
        }
        .btn-modern-action:hover { background-color: #dc3545; color: #ffffff !important; border-color: #dc3545; }
        .card { border-radius: 15px; transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</head>
<body class="p-4 bg-light">
    <div class="container-fluid mt-2">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary fw-bold display-5 mb-0">Analytics Insights</h1>
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

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold border-0 pt-3">Course Distribution</div>
                    <div class="card-body" style="height: 280px;"><canvas id="courseChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold border-0 pt-3">Students per Year</div>
                    <div class="card-body" style="height: 280px;"><canvas id="yearChart"></canvas></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold border-0 pt-3">Gender Ratio</div>
                    <div class="card-body" style="height: 280px;"><canvas id="genderChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4">
                    <h5 class="fw-bold text-center mb-4">Risk Distribution Overview</h5>
                    <div style="height: 350px;">
                        <canvas id="riskChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Data Parsing
        const courseLabels = JSON.parse(`{!! json_encode($chartLabels) !!}`);
        const courseValues = JSON.parse(`{!! json_encode($chartValues) !!}`);
        const yearLabels   = JSON.parse(`{!! json_encode($yearLabels) !!}`);
        const yearValues   = JSON.parse(`{!! json_encode($yearValues) !!}`);
        const genderLabels = JSON.parse(`{!! json_encode($genderLabels) !!}`);
        const genderValues = JSON.parse(`{!! json_encode($genderValues) !!}`);

        // 1. Base Options for Circle/Doughnut (No X/Y lines)
        const baseCircleOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom' } 
            },
            scales: {
                x: { display: false }, 
                y: { display: false }  
            }
        };

        // 2. Bar Chart Options (With X/Y lines)
        const barOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, display: true },
                x: { display: true }
            },
            plugins: { legend: { position: 'bottom' } }
        };

        // Course Distribution - Solid Circle (Pie)
        new Chart(document.getElementById('courseChart'), {
            type: 'pie',
            data: { 
                labels: courseLabels, 
                datasets: [{ 
                    data: courseValues, 
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'] 
                }] 
            },
            options: {
                ...baseCircleOptions,
                cutout: 0 // This makes it a full circle instead of a doughnut
            }
        });

        // Students per Year - Bar Chart (Needs Axes)
        new Chart(document.getElementById('yearChart'), {
            type: 'bar',
            data: { 
                labels: yearLabels, 
                datasets: [{ 
                    label: 'Students', 
                    data: yearValues, 
                    backgroundColor: '#36b9cc', 
                    borderRadius: 5 
                }] 
            },
            options: barOptions
        });

        // Gender Ratio - Uniform Doughnut
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: { 
                labels: genderLabels, 
                datasets: [{ 
                    data: genderValues, 
                    backgroundColor: ['#f49bb0', '#7496fb'] 
                }] 
            },
            options: {
                ...baseCircleOptions,
                cutout: '70%' // Uniform size for Gender
            }
        });

        // Risk Overview - Uniform Doughnut
        new Chart(document.getElementById('riskChart'), {
            type: 'doughnut',
            data: {
                labels: ['High Risk', 'Medium Risk', 'Low Risk'],
                datasets: [{
                    data: [
                        Number(`{{ $highRisk ?? 0 }}`), 
                        Number(`{{ $mediumRisk ?? 0 }}`), 
                        Number(`{{ $lowRisk ?? 0 }}`)
                    ],
                    backgroundColor: ['#dc3545', '#ffc107', '#198754'],
                    borderWidth: 0
                }]
            },
            options: {
                ...baseCircleOptions,
                cutout: '70%' // Same uniform size as Gender
            }
        });
    </script>
</body>
</html>