<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - {{ $student->name }}</title>
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
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-warning mb-0">Edit Student</h3>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0 fw-bold">Update Information for ID: {{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Full Name</label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $student->name) }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $student->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label fw-bold">Gender</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="" selected disabled>Select</option>
                                    <option value="Female" {{ old('gender') ?? $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Male" {{ old('gender') ?? $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
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
                                       value="{{ old('course', $student->course) }}">
                                @error('course')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="year" class="form-label fw-bold">Year</label>
                                <select name="year" id="year" class="form-select @error('year') is-invalid @enderror">
                                    @php $currentYear = old('year', $student->year); @endphp
                                    <option value="1" {{ $currentYear == 1 ? 'selected' : '' }}>Year 1</option>
                                    <option value="2" {{ $currentYear == 2 ? 'selected' : '' }}>Year 2</option>
                                    <option value="3" {{ $currentYear == 3 ? 'selected' : '' }}>Year 3</option>
                                    <option value="4" {{ $currentYear == 4 ? 'selected' : '' }}>Year 4</option>
                                </select>
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Assignment Score</label>
                                <input type="number" name="assignment_score" class="form-control modern-input" value="{{ old('assignment_score', $student->assignment_score) }}" min="0" max="100">
                            </div>
    
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Midterm Score</label>
                                <input type="number" name="midterm_score" class="form-control modern-input" value="{{ old('midterm_score', $student->midterm_score) }}" min="0" max="100">
                            </div>
    
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold text-secondary small">Attendance Rate (%)</label>
                                <input type="number" name="attendance_rate" class="form-control modern-input" value="{{ old('attendance_rate', $student->attendance_rate) }}" min="0" max="100">
                            </div>

                            <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Student Photo</label>
                                    <input type="file" name="photo" class="form-control">
                                </div>

                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold shadow-sm">Update Student Record</button>
                            <a href="/students" class="btn btn-light btn-sm text-muted">Cancel Changes</a>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                      <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <small class="text-muted">
                    Last updated: {{ $student->updated_at ? $student->updated_at->diffForHumans() : 'Never' }}
                </small>
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
</body>
</html>