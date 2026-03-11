<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB; 

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $perPage = $request->input('per_page', 5);
        if($perPage == 'all'){
            $perPage = 999;
        }
        
        $students = Student::when($search, function($query, $search){
            return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('course', 'like', "%{$search}%")
                         ->orWhere('year', 'like', "%{$search}%");
        })->paginate($perPage)->withQueryString();

        //pie chart
        $courseData = Student::select('course',DB::raw('count(*) as total'))
            ->groupBy('course')
            ->get();
        $chartLabels = $courseData->pluck('course');
        $chartValues = $courseData->pluck('total');
        
        return view('students.index', compact('students', 'chartLabels', 'chartValues'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'course' => 'required',
            'year' => 'required|numeric',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')->with('success', 'Student added successfully!');
    }

    public function edit($id)
    {
        $student = \App\Models\Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'course' => $request->course,
            'year' => $request->year
        ]);

        return redirect('/students');
    }

    public function destroy($id)
    {
        $student = Student::find($id);
        $student->delete();

        return redirect('/students');
    }

}