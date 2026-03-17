<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

use App\Models\Student;

class StudentController extends Controller
{
    //display all student with search, sorting, pagination
    public function index(Request $request)
   {
    $search = $request->input('search');
    $gender = $request->input('gender');
    $year = $request->input('year');
    
    // Other settings
    $perPage = $request->input('per_page', 5);
    $sort = $request->input('sort', 'id');
    $direction = $request->input('direction', 'asc');

    $query = Student::query();

    // 1. Broad Search: Only search Name, Email, and Course
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('course', 'like', "%{$search}%");
        });
    }

    // 2. Dedicated Gender Filter
    if ($request->filled('gender')) {
        $query->where('gender', $gender);
    }

    // 3. Dedicated Year Filter (The "Year 1" fix)
    if ($request->filled('year')) {
        $query->where('year', $year);
    }

    // 4. Final Execution (Ensure this is the ONLY $students = ...)
    $students = $query->orderBy($sort, $direction)
                      ->paginate($perPage == 'all' ? Student::count() : $perPage)
                      ->withQueryString();

    return view('students.index', compact('students'));
}

    public function visualize()
    {
        // 1. Course Data (for Pie Chart)
        $courses = Student::select('course', DB::raw('count(*) as total'))
            ->groupBy('course')
            ->get();

        // 2. Year Data (for Bar Chart)
        $years = Student::select('year', DB::raw('count(*) as total'))
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // 3. Gender Data (for Doughnut Chart)
        $genders = Student::select('gender', DB::raw('count(*) as total'))
            ->groupBy('gender')
            ->get();

        $highRisk = Student::where('risk_level', 'High')->count();
        $mediumRisk = Student::where('risk_level', 'Medium')->count();
        $lowRisk = Student::where('risk_level', 'Low')->count();

        return view('students.visualize', [
            // Ensure these variable names match exactly what you use in the Blade script
            // pluck = label for graph
            'chartLabels'  => $courses->pluck('course'),
            'chartValues'  => $courses->pluck('total'),
            'yearLabels'   => $years->pluck('year')->map(fn($y) => "Year $y"),
            'yearValues'   => $years->pluck('total'),
            'genderLabels' => $genders->pluck('gender'),
            'genderValues' => $genders->pluck('total'),
            'highRisk' => $highRisk,
            'mediumRisk' => $mediumRisk,
            'lowRisk' => $lowRisk,
            'total' => $highRisk + $mediumRisk + $lowRisk
        ]);
    }
    
    public function admin()
    {
        $totalStudents = Student::count();
        $totalCourses = Student::distinct('course')->count('course'); //count unique course
        $recentStudents = Student::latest()->take(10)->get();

        $highRisk = Student::where('risk_level', 'High')->count(); 
        $mediumRisk = Student::where('risk_level', 'Medium')->count();
        $lowRisk = Student::where('risk_level', 'Low')->count();

        return view('students.admin', compact(
            'totalStudents', 
            'totalCourses', 
            'recentStudents', 
            'highRisk', 
            'mediumRisk', 
            'lowRisk'
    ));
    }

    // load create.blade.php file
    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request) 
    {
        // Fix: Added actual validation rules so it doesn't throw a syntax error
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'gender' => 'required',
            'course' => 'required',
            'year' => 'required|integer',
            'assignment_score' => 'required|integer|min:0|max:100',
            'midterm_score' => 'required|integer|min:0|max:100',
            'attendance_rate' => 'required|integer|min:0|max:100',
        ]);

        $assignment = $request->assignment_score;
        $midterm    = $request->midterm_score;
        $attendance = $request->attendance_rate;

        // Risk Logic
        $risk = 'Low';
        if ($attendance < 60 || $midterm < 40) { 
            $risk = 'High'; 
        } elseif ($attendance < 80 || $midterm < 60) { 
            $risk = 'Medium'; 
        }

        Student::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'gender'           => $request->gender,
            'course'           => $request->course,
            'year'             => $request->year,
            'assignment_score' => $assignment,
            'midterm_score'    => $midterm,
            'attendance_rate'  => $attendance,
            'risk_level'       => $risk,
        ]);

        return redirect()->route('students.index')->with('success', 'New student added successfully.');
    }

    public function edit($id)
    {
        $student = \App\Models\Student::findOrFail($id); //use to find student 
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students,email,'.$student->id,
            'gender' => 'required', 
            'course' => 'required',
            'year' => 'required|integer',
            'assignment_score' => 'required|integer|min:0|max:100',
            'midterm_score' => 'required|integer|min:0|max:100',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $risk = 'Low'; // Default
        if ($request->attendance_rate < 60 || $request->midterm_score < 40) {
            $risk = 'High';
        } elseif ($request->attendance_rate < 80 || $request->midterm_score < 60) {
            $risk = 'Medium';
        }

        if ($request->hasFile('photo')) {
            // Save the photo in public/uploads/students
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/students'), $fileName);
            $validated['photo'] = $fileName;
        }

        $validated['risk_level'] = $risk;

        $student->update($validated); 

        return redirect()->route('students.index')->with('success', 'Student updated!');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect('/students'); //redirect to main list
    }

    public function exportCsv() 
    {
        $fileName = 'students_list_' . date('Y-m-d') . '.csv'; 
        $students = Student::orderBy('id', 'asc')->get();

        $headers = [ 
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName", 
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $columns = ['ID', 'Name', 'Email','Gender', 'Course', 'Year'];

        $callback = function() use ($students, $columns) {
            $file = fopen('php://output', 'w'); //open temporary stream to write data directly to user browser
            fputcsv($file, $columns); //write top header

            $rowNumber = 1;

            foreach ($students as $student) {
                fputcsv($file, [
                    // id: 001,002,003
                    str_pad($student->id, 3, '0', STR_PAD_LEFT), 
                    $student->name,
                    $student->email,
                    $student->gender,
                    $student->course,
                    $student->year,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ], [
            'csv_file.mimes' => 'The file must be a .csv file.'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        fgetcsv($handle); // skip header row

        $count = 0;
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
            // Skip empty rows
            if (empty($data[1])) continue;
    
            // Correctly assign separate indices
            $midterm = $data[7];
            $attendance = $data[8];
    
            // ADDED: Risk Calculation for Import
            $currentRisk = 'Low'; 
            if ($attendance < 60 || $midterm < 40) {
                $currentRisk = 'High';
            } elseif ($attendance < 80 || $midterm < 60) {
                $currentRisk = 'Medium';
            }

            Student::create([
                'name'             => $data[1],
                'email'            => $data[2],
                'gender'           => $data[3],
                'course'           => $data[4],
                'year'             => $data[5],
                'assignment_score' => $data[6], // Column G
                'midterm_score'    => $midterm, // Column H
                'attendance_rate'  => $attendance, // Column I (Fixed index)
                'risk_level'       => $currentRisk  // Added so Admin dashboard syncs
            ]);
            $count++;
        }
        fclose($handle);
        return redirect('/students')->with('success', "Import successful! $count students added.");
    }

    public function show(Student $student)
    {
        //dd($student);
        return view('students.show', compact('student'));
    }

    public function bulkManage()
    {
        $students = Student::all();
        return view('students.bulk-manage', compact('students'));
    }

    public function bulkDelete(Request $request)
    {
        $ids = (array)$request->input('ids', []);

        if(empty($ids)){
            return redirect()->back()->with('error', 'Pleasae select at least one student.');
        }

        //find student in array to delete
        Student::whereIn('id', $ids)->delete();

        return redirect()->route('students.index')->with('success', 'Selected students deleted successfully.');
    }

}