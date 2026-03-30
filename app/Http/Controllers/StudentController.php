<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 

use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search');
    $gender = $request->input('gender');
    $year = $request->input('year');
    
    $perPage = $request->input('per_page', 5);
    $sort = $request->input('sort', 'id');
    $direction = $request->input('direction', 'asc');

    $query = Student::query();

    // Filtering logic
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('course', 'like', "%{$search}%");
        });
    }

    if ($request->filled('gender')) {
        $query->where('gender', $gender);
    }

    if ($request->filled('year')) {
        $query->where('year', $year);
    }

    // 1. Get the paginated students
    $students = $query->orderBy($sort, $direction)
                      ->paginate($perPage == 'all' ? Student::count() : $perPage)
                      ->withQueryString();

    // 2. Loop through each student to attach the "AI Prediction" data
    $students->getCollection()->transform(function ($student) {
    // A. Calculate what they have earned RIGHT NOW (Max 50%)
    $earnedAttendance = ($student->attendance / 100) * 10;
    $earnedTest = ($student->test_score / 100) * 15;
    $earnedAssignment = ($student->assignment_score / 100) * 25;
    
    // This is the "Current Mark" shown on the profile
    $student->current_progress = round($earnedAttendance + $earnedTest + $earnedAssignment, 2);

    // B. Calculate the AI Prediction (Max 100%)
    $performanceRatio = ($student->current_progress > 0) ? ($student->current_progress / 50) : 0;
    $student->forecasted_total = round($student->current_progress + ($performanceRatio * 50), 2);

    if ($student->attendance < 80) {
                $student->risk_status = 'Critical: Low Attendance';
                $student->risk_color = 'danger';
            } elseif ($student->forecasted_total < 50) {
                $student->risk_status = 'At Risk';
                $student->risk_color = 'warning';
            } else {
                $student->risk_status = 'Safe';
                $student->risk_color = 'success';
            }

    return $student;
});

        $highRiskStudents = \App\Models\Student::where('risk_level', 'High')->get(); 
        // 2. Update the count based on our AI calculation
        $atRiskCount = $highRiskStudents->count();

        // 3. IMPORTANT: Pass 'highRiskStudents' to the view so the @forelse finds it
        return view('students.index', compact('students', 'highRiskStudents', 'atRiskCount'));
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

        $students = Student::query()->paginate(10);
        $course = Student::distinct()->pluck('course');
        $heatmapData = Student::select('course', 'risk_level', DB::raw('count(*) as total'))
            ->groupBy('course', 'risk_level')
            ->get();

        $stats = [
            'high' => Student::where('risk_level', 'High')->count(),
            'medium' => Student::where('risk_level', 'Medium')->count(),
            'low' => Student::where('risk_level', 'Low')->count(),
        ];

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
            'total' => $highRisk + $mediumRisk + $lowRisk,
            'students' => $students,
            'courses' => $course, 
            'heatmapData' => $heatmapData,
            'stats' => $stats
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

        $topRiskStudents = Student::whereIn('risk_level', ['High', 'Medium'])
                            ->orderBy('attendance_rate', 'asc')
                            ->get();

        return view('students.admin', compact(
            'totalStudents', 
            'totalCourses', 
            'recentStudents', 
            'highRisk', 
            'mediumRisk', 
            'lowRisk',
            'topRiskStudents' 
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
            'test_score' => 'required|integer|min:0|max:100',
            'attendance_rate' => 'required|integer|min:0|max:100',
        ]);

        $assignment = $request->assignment_score * 0.25;
        $test    = $request->test_score * 0.15;
        $attendance = $request->attendance_rate * 0.10;
        $finalWeightedScore = $attendance + $test + $assignment;

        // Risk Logic
        $risk = 'Low';
        if ($finalWeightedScore < 40) { 
            $risk = 'High'; 
        } elseif ($finalWeightedScore < 65) { 
            $risk = 'Medium'; 
        }else{
            $risk = 'Low';
        }

        //display the % chance
        $risk_prediction = 100 - $finalWeightedScore;

        Student::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'gender'           => $request->gender,
            'course'           => $request->course,
            'year'             => $request->year,
            'assignment_score' => $assignment,
            'test_score'    => $test,
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
            'test_score' => 'required|integer|min:0|max:100',
            'attendance_rate' => 'required|integer|min:0|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $attendanceWeight = $request->attendance_rate * 0.10;
        $testWeight = $request->test_score * 0.15;
        $assignmentWeight = $request->assignment_score * 0.25;
        $currentTotal = $attendanceWeight + $testWeight + $assignmentWeight;

        // calc AI prediction
        $performanceRatio = $currentTotal / 50;
        $predictedFinal = $performanceRatio * 50;
        $totalPredictedScore = $currentTotal + $predictedFinal;

        $risk = "Low";
        if($totalPredictedScore < 40){
            $risk = "High";
        }
        elseif($totalPredictedScore < 60){
            $risk = "Medium";
        }

        if ($request->hasFile('photo')) {
            // Save the photo in public/uploads/students
            $fileName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('uploads/students'), $fileName);
            $validated['photo'] = $fileName;
        }

        $validated['risk_level'] = $risk;
        $validated['tets_score'] = $request->test_score;
        $validated['predicted_final_score'] = $predictedFinal;

        $student->update($validated); 

        return redirect()->back()->with('success', 'AI Predictio Updated.');
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

        $columns = ['ID', 'Name', 'Email','Gender', 'Course', 'Year','Attendance Rate', 'Test Score', 'Assignment Score', 'Risk Level', 'Predicted Final', 'Actual Final'];

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
                    $student->attendance_rate,
                    $student->test_score,
                    $student->assignment_score,            
                    $student->risk_level
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
            $testterm = $data[7];
            $attendance = $data[8];
    
            // ADDED: Risk Calculation for Import
            $currentRisk = 'Low'; 
            if ($attendance < 60 || $testterm < 40) {
                $currentRisk = 'High';
            } elseif ($attendance < 80 || $testterm < 60) {
                $currentRisk = 'Medium';
            }

            Student::create([
                'name'             => $data[1],
                'email'            => $data[2],
                'gender'           => $data[3],
                'course'           => $data[4],
                'year'             => $data[5],
                'assignment_score' => $data[6], // Column G
                'test_score'    => $testterm, // Column H
                'attendance_rate'  => $attendance, // Column I (Fixed index)
                'risk_level'       => $currentRisk  // Added so Admin dashboard syncs
            ]);
            $count++;
        }
        fclose($handle);
        return redirect('/students')->with('success', "Import successful! $count students added.");
    }

    public function show($id)
    {
        $student = Student::findOrFail($id);

        $attendance  = $student->attendance_rate ?? $student->attendance ?? 0;  // use whichever field is correct
        $test        = $student->test_score ?? 0;
        $assignment  = $student->assignment_score ?? 0;

        $earnedAttendance  = ($attendance  / 100) * 10;
        $earnedTest        = ($test        / 100) * 15;
        $earnedAssignment  = ($assignment  / 100) * 25;

        $student->current_progress = round($earnedAttendance + $earnedTest + $earnedAssignment, 2);

        $performanceRatio = $student->current_progress > 0 ? ($student->current_progress / 50) : 0;
        $student->forecasted_total = round($student->current_progress + ($performanceRatio * 50), 2);

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

    public function downloadPDF($id)
    {
        $student = Student::findOrFail($id);
        $pdf = Pdf::loadView('students.pdf_report', compact('student'));

        return $pdf->download("Academic_Report_{$student->student_id}.pdf");
    }

    public function predictFinalMark($testScore, $assignmentScore, $attendanceScore)
    {
        $earnedFromTest = ($testScore / 100) * 15;
        $earnedFromAssignment= ($assignmentScore / 100 * 25);
        $totalEarnedSoFar = $earnedFromTest + $earnedFromAssignment;

        //tell what mark they get/capture
        $performanceRatio = ($totalEarnedSoFar / 40);

        //predict out of 50
        $predictedFinalScore = $performanceRatio * 50;

        //internal + predict + attendance
        $earnedAttendance = ($attendanceScore / 100) * 10;
        $forecastedTotal = $totalEarnedSoFar + $predictedFinalScore + $earnedAttendance;

        return round($forecastedTotal, 2);
    }

}