<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Manage Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4 bg-light">
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-danger fw-bold mb-0 display-4">Bulk Delete</h1>
        </div>

        <hr class="mt-2 mb-4">

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
                <span class="me-2">✅</span>
                <div class="fw-bold">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
                <span class="me-2">⚠️</span>
                <div class="fw-bold">{{ session('error') }}</div>
            </div>
        @endif

        <form id="bulkDeleteForm" action="{{ route('students.bulkDelete') }}" method="POST">
            @csrf
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <span class="fw-bold text-muted">Select students to remove from system</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">Select</th>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $student->id }}" class="student-checkbox form-check-input">
                                </td>
                                <td class="fw-bold text-dark">{{ $student->name }}</td>
                                <td>{{ $student->course }}</td>
                                <td><span class="badge bg-light text-dark border">Year {{ $student->year }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div class="d-flex justify-content-end mb-5">
            <button type="button" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" id="btnOpenBulkModal">
                Confirm Bulk Deletion
            </button>
        </div>
    </div>

    <div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Bulk Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5 mb-0">Are you sure you want to delete</p>
                    <h3 class="fw-bold text-danger my-2" id="selectedCountDisplay">0 Students</h3>
                    <p class="text-muted small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer bg-light border-top-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" id="confirmFinalDelete">Delete Now</button>
                </div>
            </div>
        </div>
    </div>

    <a href="/students" class="btn btn-outline-secondary btn-sm">← Back to List</a>

    <p class="text-center text-muted mt-4 small">
        Student Management System &copy; 2026
    </p>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // 1. Modal & Selection Logic
    const bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
    const btnOpenModal = document.getElementById('btnOpenBulkModal');
    const btnFinalDelete = document.getElementById('confirmFinalDelete');
    const bulkForm = document.getElementById('bulkDeleteForm');

    btnOpenModal.addEventListener('click', function() {
        // Find all checked boxes
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;

        if (checkedCount === 0) {
            alert('Please select at least one student to delete.');
            return;
        }

        // Update modal text and show it
        document.getElementById('selectedCountDisplay').textContent = checkedCount + " Students";
        bulkDeleteModal.show();
    });

    // 2. Final Submit
    btnFinalDelete.addEventListener('click', function() {
        bulkForm.submit();
    });

    // 3. Auto-hide alerts
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = "opacity 0.5s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 500);
        });
    }, 3000);
</script>
@include('partials.chatbot')
</body>
</html>