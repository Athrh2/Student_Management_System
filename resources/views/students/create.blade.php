<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { border: none; border-radius: 15px; }
        .card-header { border-radius: 15px 15px 0 0 !important; }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-primary mb-0">Add New Student</h3>
            </div>

            <div class="d-flex flex-column align-items-end mb-4">
                    <form action="/students/import" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="file" name="csv_file" class="form-control form-control-sm" style="width: 250px;" required accept=".csv">
                        <button type="submit" class="btn btn-primary btn-sm fw-bold">Import CSV</button>
                    </form>
                </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">Student Information</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/students">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="e.g. Hana Bin Abdul" value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="example@gmail.com" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label fw-bold">Gender</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="" selected disabled>Select</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="course" class="form-label fw-bold">Course</label>
                                <input type="text" name="course" id="course" 
                                       class="form-control @error('course') is-invalid @enderror" 
                                       placeholder="e.g. Computer Science" value="{{ old('course') }}">
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label fw-bold">Year</label>
                                <select name="year" id="year" class="form-select @error('year') is-invalid @enderror">
                                    <option value="" selected disabled>Select</option>
                                    <option value="1" {{ old('year') == 1 ? 'selected' : '' }}>Year 1</option>
                                    <option value="2" {{ old('year') == 2 ? 'selected' : '' }}>Year 2</option>
                                    <option value="3" {{ old('year') == 3 ? 'selected' : '' }}>Year 3</option>
                                    <option value="4" {{ old('year') == 4 ? 'selected' : '' }}>Year 4</option>
                                </select>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Attendance %</label>
                                <input type="number" name="attendance_rate" class="form-control modern-input" min="0" max="100" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Test (0-100)</label>
                                <input type="number" name="test_score" class="form-control modern-input" min="0" max="100" required>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Assignment (0-100)</label>
                                <input type="number" name="assignment_score" class="form-control modern-input" min="0" max="100" required>
                            </div>

                            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Student Photo</label>
                                    <input type="file" name="photo" class="form-control">
                                </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary btn-lg shadow-sm" id="btnOpenCreateModal">Save Student Data</button>
                            <button type="reset" class="btn btn-light btn-lg shadow-sm">Clear Form</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="createConfirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Confirm New Student</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
            
                        <div class="modal-body p-4 text-center">
                            <p class="fs-5 mb-0 text-dark">Are you sure you want to add</p>
                            <h3 class="fw-bold text-danger my-2" id="displayStudentName">New Student</h3>
                            <p class="text-muted small">This action will update the student records.</p>
                        </div>
            
                        <div class="modal-footer bg-light border-top-0 justify-content-center">
                            <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger px-4 fw-bold" id="confirmFinalCreate">Confirm Now</button>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <a href="/students" class="btn btn-outline-secondary btn-sm">← Back to List</a>
            
            <p class="text-center text-muted mt-4 small">
                Student Management System &copy; 2026
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const createModal = new bootstrap.Modal(document.getElementById('createConfirmModal'));
    const openBtn = document.getElementById('btnOpenCreateModal');
    const finalConfirmBtn = document.getElementById('confirmFinalCreate');
    const form = document.querySelector('form'); // Or use your form's ID

    // 1. Open the modal when the blue button is clicked
    openBtn.addEventListener('click', function() {
        const nameInput = document.querySelector('input[name="name"]').value;
        document.getElementById('displayStudentName').innerText = nameInput || "New Student";
        createModal.show();
    });

    // 2. ONLY submit the form when the red "Confirm Now" button is clicked
    finalConfirmBtn.addEventListener('click', function() {
        form.submit(); 
    });
});
</script>
@include('partials.chatbot')
</body>
</html>