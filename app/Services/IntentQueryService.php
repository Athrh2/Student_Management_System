<?php

namespace App\Services;

use App\Models\Student;

class IntentQueryService
{
    // ─── Your actual courses (Malay) ─────────────────────────────────────
    private array $courses = [
        'computer sains',   // must be before 'sains' so it matches first
        'sains sukan',      // must be before 'sains' so it matches first
        'ilmu pendidikan',
        'biologi',
        'ekonomi',
        'fizik',
        'kimia',
        'management',
        'matematik',
        'sains',        
    ];

    /**
     * Parse user message and return structured intent.
     */
    public function parseIntent(string $message): array
    {
        $msg    = strtolower(trim($message));
        $intent = $this->detectIntent($msg);
        $course = $this->extractCourse($msg);
        $gender = $this->extractGender($msg);
        $year   = $this->extractYear($msg);
        $limit  = $this->extractLimit($msg);

        return compact('intent', 'course', 'gender', 'year', 'limit');
    }

    /**
     * Run Eloquent query based on parsed intent and return results.
     */
    public function query(array $parsed): array
    {
        ['intent' => $intent, 'course' => $course, 'gender' => $gender, 'year' => $year, 'limit' => $limit] = $parsed;

        $q = Student::query();

        // ── Apply filters ──────────────────────────────────────
        if ($course) {
            $q->whereRaw('LOWER(course) LIKE ?', ["%{$course}%"]);
        }
        if ($gender) {
            $q->whereRaw('LOWER(gender) = ?', [$gender]);
        }
        if ($year) {
            $q->where('year', $year);
        }

        // ── Apply intent logic ─────────────────────────────────
        switch ($intent) {

            case 'likely_fail':
            case 'at_risk':
                $q->where('risk_level', 'high')
                  ->orderBy('attendance_rate', 'asc');
                $label = 'Students likely to fail / at risk';
                break;

            case 'top_students':
                $q->orderByRaw('(test_score * 0.15 + assignment_score * 0.25 + attendance_rate * 0.10) DESC');
                $label = 'Top performing students';
                break;

            case 'low_students':
                $q->orderByRaw('(test_score * 0.15 + assignment_score * 0.25 + attendance_rate * 0.10) ASC');
                $label = 'Lowest performing students';
                break;

            case 'low_attendance':
                $q->orderBy('attendance_rate', 'asc');
                $label = 'Students with lowest attendance';
                break;

            case 'high_attendance':
                $q->orderBy('attendance_rate', 'desc');
                $label = 'Students with highest attendance';
                break;

            case 'top_assignment':
                $q->orderBy('assignment_score', 'desc');
                $label = 'Students with highest assignment score';
                break;

            case 'low_assignment':
                $q->orderBy('assignment_score', 'asc');
                $label = 'Students with lowest assignment score';
                break;

            case 'top_test':
                $q->orderBy('test_score', 'desc');
                $label = 'Students with highest test score';
                break;

            case 'low_test':
                $q->orderBy('test_score', 'asc');
                $label = 'Students with lowest test score';
                break;

            case 'count':
                $count = $q->count();
                return [
                    'intent'   => $intent,
                    'label'    => 'Student count' . ($course ? ' in ' . ucwords($course) : ''),
                    'count'    => $count,
                    'students' => [],
                ];

            case 'course_list':
            default:
                $q->orderBy('name', 'asc');
                $label = $course ? 'All students in ' . ucwords($course) : 'All students';
                break;

            case 'medium_risk':
                $q->where('risk_level', 'medium')->orderBy('attendance_rate', 'asc');
                $label = 'Medium risk students';
                break;

            case 'low_risk':
                $q->where('risk_level', 'low')->orderByRaw('(test_score * 0.15 + assignment_score * 0.25 + attendance_rate * 0.10) DESC');
                $label = 'Low risk / safe students';
                break;

            case 'zero_attendance':
    $q->where('attendance_rate', '<=', 10)->orderBy('attendance_rate', 'asc');
    $label = 'Students with near-zero attendance';
    break;

case 'consistent':
    // All three components above 60%
    $q->whereRaw('attendance_rate >= 60')
      ->whereRaw('test_score >= 60')
      ->whereRaw('assignment_score >= 60')
      ->orderByRaw('(test_score * 0.15 + assignment_score * 0.25 + attendance_rate * 0.10) DESC');
    $label = 'Consistent students (all components ≥ 60%)';
    break;
        }

        $students = $q->limit($limit)->get([
            'name', 'course', 'year', 'gender',
            'assignment_score', 'test_score', 'attendance_rate', 'risk_level',
        ])->map(function ($s) {
            $s->current_progress = round(
                ($s->attendance_rate  / 100) * 10 +
                ($s->test_score       / 100) * 15 +
                ($s->assignment_score / 100) * 25,
                2
            );
            return $s;
        });

        return [
            'intent'   => $intent,
            'label'    => $label,
            'students' => $students,
            'count'    => null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────

    private function detectIntent(string $msg): string
    {
        // Fail / at-risk
        if ($this->matches($msg, [
            'fail', 'failing', 'at risk', 'at-risk', 'danger',
            'low grade', 'poor performance', 'struggle', 'struggling',
            'likely to fail', 'akan gagal', 'gagal', 'berisiko',
        ])) {
            return 'likely_fail';
        }

        // Top / highest grade
        if ($this->matches($msg, [
            'highest grade', 'top student', 'best student', 'highest mark',
            'best performance', 'highest score', 'top performer',
            'gred tertinggi', 'pelajar terbaik', 'markah tertinggi',
        ])) {
            return 'top_students';
        }

        // Lowest grade
        if ($this->matches($msg, [
            'lowest grade', 'worst student', 'lowest mark', 'lowest score',
            'worst performance', 'bottom student',
            'gred terendah', 'markah terendah', 'pelajar terburuk',
        ])) {
            return 'low_students';
        }

        // Attendance — low
        if ($this->matches($msg, [
            'lowest attendance', 'worst attendance', 'poor attendance',
            'least attendance', 'missing class', 'absent',
            'kehadiran rendah', 'kurang hadir', 'tidak hadir',
        ])) {
            return 'low_attendance';
        }

        // Attendance — high
        if ($this->matches($msg, [
            'highest attendance', 'best attendance', 'most attendance',
            'perfect attendance', 'kehadiran tinggi', 'kehadiran terbaik',
        ])) {
            return 'high_attendance';
        }

        // Assignment — top
        if ($this->matches($msg, [
            'highest assignment', 'best assignment', 'top assignment',
            'tugasan terbaik', 'markah tugasan tertinggi',
        ])) {
            return 'top_assignment';
        }

        // Assignment — low
        if ($this->matches($msg, [
            'lowest assignment', 'worst assignment', 'poor assignment',
            'tugasan terendah',
        ])) {
            return 'low_assignment';
        }

        // Test — top
        if ($this->matches($msg, [
            'highest test', 'best test', 'top test',
            'highest exam', 'best exam', 'ujian terbaik', 'markah ujian tertinggi',
        ])) {
            return 'top_test';
        }

        // Test — low
        if ($this->matches($msg, [
            'lowest test', 'worst test', 'poor test',
            'lowest exam', 'failed test', 'ujian terendah',
        ])) {
            return 'low_test';
        }

        // Count
        if ($this->matches($msg, [
            'how many', 'count', 'total student', 'number of student',
            'berapa', 'jumlah pelajar', 'bilangan pelajar',
        ])) {
            return 'count';
        }

        // General list — intentionally broad
        if ($this->matches($msg, [
            'list', 'show', 'who is', 'who are', 'who has',
            'students in', 'all student', 'senarai',
            'tunjuk', 'semua pelajar', 'pelajar dalam',
            'all female', 'all male', 'all girl', 'all boy',          
            'year student', 'tahun', '1st year', '2nd year',         
            '3rd year', '4th year','third year', 'second year',     
            'first year', 'fourth year',
        ])) {
            return 'course_list';
        }

        // If a course was mentioned but no other intent matched → show list
        if ($this->extractCourse($msg)) {
            return 'course_list';
        }

        // High risk (already have 'at_risk' but add more triggers)
        if ($this->matches($msg, [
            'high risk', 'high priority', 'attention', 'need attention',
            'need help', 'intervention', 'critical', 'danger',
            'urgent', 'perlu perhatian', 'kritikal', 'bahaya',
        ])) {
            return 'at_risk';
        }

        // Medium risk
        if ($this->matches($msg, [
            'medium risk', 'moderate risk', 'average risk', 'risiko sederhana',
        ])) {
            return 'medium_risk';  // new intent
        }

        // Low risk / safe students
        if ($this->matches($msg, [
            'low risk', 'safe student', 'doing well', 'good student',
            'passing', 'no risk', 'risiko rendah', 'selamat',
        ])) {
            return 'low_risk';  // new intent
        }

        // Perfect / high assignment
if ($this->matches($msg, [
    'perfect assignment', 'full marks assignment', 'full assignment',
    'markah penuh tugasan',
])) {
    return 'top_assignment';
}

// Never attended / zero attendance
if ($this->matches($msg, [
    'never attend', 'zero attendance', 'no attendance',
    'tidak pernah hadir', 'tiada kehadiran',
])) {
    return 'zero_attendance';  // new intent
}

// Improving / consistent
if ($this->matches($msg, [
    'consistent', 'reliable', 'consistent student',
    'konsisten',
])) {
    return 'consistent';  // new intent
}

        return 'unknown';
    }

    private function extractCourse(string $msg): ?string
    {
        // Already sorted longest-first in $this->courses array above
        foreach ($this->courses as $course) {
            if (str_contains($msg, strtolower($course))) {
                return $course;
            }
        }
        return null;
    }

    private function extractGender(string $msg): ?string
{
    if ($this->matches($msg, ['female', 'girl', 'women', 'woman', 'perempuan', 'wanita'])) {
        return 'female';
    }
    if ($this->matches($msg, ['male', 'boy', 'men', 'man', 'lelaki', 'laki-laki'])) {
        return 'male';
    }
    return null;
}

    private function extractYear(string $msg): ?int
    {
        // Matches: "year 2", "tahun 3"
        if (preg_match('/\b(year|tahun)\s*([1-9])\b/', $msg, $m)) return (int) $m[2];

        // Matches: "3rd year", "2nd year", "1st year", "4th year"
        if (preg_match('/\b([1-9])\s*(?:st|nd|rd|th)\s*year\b/', $msg, $m)) return (int) $m[1];

        // Matches: "first year", "second year", "third year"
        $words = [
            'first' => 1, 'second' => 2, 'third' => 3,
            'fourth' => 4, 'fifth' => 5,
            'pertama' => 1, 'kedua' => 2, 'ketiga' => 3,
        ];
        foreach ($words as $word => $num) {
            if (str_contains($msg, $word . ' year') || str_contains($msg, 'year ' . $word)) {
                return $num;
            }
        }

        return null;
    }

    private function extractLimit(string $msg): int
    {
        if (preg_match('/\btop\s*(\d+)\b/', $msg, $m))   return (int) $m[1];
        if (preg_match('/\bfirst\s*(\d+)\b/', $msg, $m)) return (int) $m[1];
        return 20; // default
    }

    private function matches(string $msg, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($msg, strtolower($kw))) return true;
        }
        return false;
    }
}