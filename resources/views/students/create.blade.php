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
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-primary mb-0">Add New Student</h3>
                <a href="/students" class="btn btn-outline-secondary btn-sm">← Back to List</a>
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
                        </div>

                        <hr class="my-4">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">Save Student Data</button>
                            <button type="reset" class="btn btn-light btn-sm">Clear Form</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center text-muted mt-4 small">
                Student Management System &copy; 2026
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>