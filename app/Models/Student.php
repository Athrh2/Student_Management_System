<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{    
    protected $fillable = [
        'name',
        'email',
        'course',
        'year',
        'gender',
        'assignment_score',
        'test_score',
        'attendance_rate',
        'risk_level', 
        'photo',
        'predicted_final_score',
        'actual_final_score',
    ];

    protected $appends = ['risk_explanation', 'current_progress'];

    public function getCurrentProgressAttribute()
    {
        return round(
            ($this->attendance_rate / 100) * 10 +
            ($this->test_score / 100) * 15 +
            ($this->assignment_score / 100) * 25,
            2
        );
    }

// WITH this (works on all PHP versions):
public static function calculateRisk(float $score)
{
    if ($score < 20)   return 'High';
    if ($score < 32.5) return 'Medium';
    return 'Low';
}

    public function getRiskExplanationAttribute()
    {
        $reasons = [];

        if($this->attendance_rate < 70){
            $reasons[] = "low attendance (" . $this->attendance_rate . "%)";
        }

        if($this->test_score < 60){
            $reasons[] = "weak test performance (" . $this->test_score . "%)";
        }

        if($this->assignment_score < 60){
            $reasons[] = "poor assignment submissions (" . $this->assignment_score . "%)";
        }

        if (empty($reasons)) {
            return "Consistent performance across all metrics indicates a stable academic standing.";
        }

        // Join with commas and "and" for the last item (e.g., "A, B and C")
        $lastItem = array_pop($reasons);
        $text = count($reasons) ? implode(', ', $reasons) . " and " . $lastItem : $lastItem;

        // Return the full sentence
        return ucfirst($text) . " indicate a " . strtolower($this->risk_level ?? 'high') . " probability of course failure.";
    }

    public function getPerformanceTrendAttribute()
    {
        $test = $this->test_score ?? 0;
        $assignment = $this->assignment_score ?? 0;
        $diff = $assignment - $test;

        if($diff > 10){
            return[
                'status' => 'Improving',
                'icon' => 'trending-up',
                'color' => 'success',
                'insight' => 'Showing strong recovery in recent assignments compared to test results.'
            ];
        }elseif($diff < -10){
            return[
                'status' => 'Declining',
                'icon' => 'trending-down',
                'color' => 'danger',
                'insight' => 'Recent assignment scores are significantly lower than test performance.'
            ];
        }else{
            return[
                'status' => 'Stable',
                'icon' => 'minus',
                'color' => 'info',
                'insight' => 'Maintaining consistent performance across all evaluated components.'
            ];
        }
    }
}
