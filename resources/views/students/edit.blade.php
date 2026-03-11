<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Edit Student</h2>

<form action="/students/{{ $student->id }}" method="POST">
    @csrf
    @method('PUT')

    <label>Name</label><br>
    <input type="text" name="name" value="{{ $student->name }}"><br><br>

    <label>Email</label><br>
    <input type="email" name="email" value="{{ $student->email }}"><br><br>

    <label>Course</label><br>
    <input type="text" name="course" value="{{ $student->course }}"><br><br>

    <label>Year</label><br>
    <input type="number" name="year" value="{{ $student->year }}"><br><br>

    <button type="submit">Update Student</button>
</form>

<br>

<a href="/students">Back</a>
</body>
</html>

