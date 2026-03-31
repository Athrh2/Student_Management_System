<!DOCTYPE html>
<html>
<head>
    <title>Academic Performance Report</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.4; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #1a237e; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* Fit to Word Info Box */
        .info-table-fit { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: auto; }
        .info-table-fit td { border: 1px solid #ddd; padding: 6px 10px; }
        
        /* The Secret Sauce: width 1% makes the cell shrink to fit the word exactly */
        .label-cell { 
            background-color: #1a237e; 
            color: white; 
            font-weight: bold; 
            width: 1%; 
            white-space: nowrap; 
        }
        .value-cell { width: 49%; } /* Balances the two columns */

        /* Component Table */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table td { border: 1px solid #ddd; padding: 10px; }

        /* Note Boxes */
        .insight-box { background: #e3f2fd; padding: 15px; border-left: 5px solid #0d47a1; margin-top: 20px; }
        .plan-box { background: #fff9c4; padding: 15px; border-left: 5px solid #f57f17; margin-top: 15px; }
        
        /* Overall Risk Box - Properly structured inside a table to prevent error */
        .overall-container { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .overall-box { background-color: #f8f9fa; padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 5px; }
        
        .risk-badge { 
            display: inline-block; padding: 8px 20px; border-radius: 15px; 
            font-weight: bold; color: white; margin-top: 10px;
        }
        .bg-high { background-color: #dc3545; }
        .bg-medium { background-color: #ffc107; color: #333; }
        .bg-low { background-color: #198754; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="color: #1a237e;">STUDENT ACADEMIC PERFORMANCE REPORT</h2>
    </div>

    <table class="info-table-fit">
        <tr>
            <td class="label-cell">Name:</td>
            <td class="value-cell">{{ $student->name }}</td>
            <td class="label-cell">Student ID:</td>
            <td class="value-cell">{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td class="label-cell">Course:</td>
            <td class="value-cell">{{ $student->course }}</td>
            <td class="label-cell">Year:</td>
            <td class="value-cell">Year {{ $student->year ?? '3' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Assessment Component</th>
                <th>Raw Score</th>
                <th>Weighted</th>
                <th>Max</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Attendance (10%)</td>
                <td>{{ $student->attendance_rate }}%</td>
                <td>{{ round($student->attendance_rate * 0.10, 2) }}</td>
                <td>10</td>
            </tr>
            <tr>
                <td>Test (15%)</td>
                <td>{{ $student->test_score }}%</td>
                <td>{{ round($student->test_score * 0.15, 2) }}</td>
                <td>15</td>
            </tr>
            <tr>
                <td>Assignments (25%)</td>
                <td>{{ $student->assignment_score }}%</td>
                <td>{{ round($student->assignment_score * 0.25, 2) }}</td>
                <td>25</td>
            </tr>
            <tr style="background:#f8f9fa;font-weight:bold;">
                <td>Current Progress</td>
                <td>—</td>
                <td>{{ $student->current_progress }}</td>
                <td>50</td>
            </tr>
            <tr style="background:#e8f4fd;font-weight:bold;">
                <td>Forecasted Final</td>
                <td>—</td>
                <td>{{ round($student->current_progress * 2, 2) }}</td>
                <td>100</td>
            </tr>
        </tbody>
    </table>

    <div class="insight-box">
        <strong style="color: #0d47a1;">AI Performance Note:</strong><br>
        <p style="margin: 8px 0; font-size: 13px;">{{ $student->risk_explanation }}</p>
        <small><em>Trend: {{ $student->performance_trend['insight'] }}</em></small>
    </div>

    <div class="plan-box">
        <strong style="color: #f57f17;">Actionable Suggestion Plan:</strong>
        <div style="font-size: 13px; margin-top: 10px;">
            @if($student->risk_level == 'High')
                <p>• <strong>Schedule Academic Counseling:</strong> Urgent meeting required.</p>
                <p>• <strong>Extra Assignments:</strong> Assign remedial modules.</p>
                <p>• <strong>Attendance Monitoring:</strong> Daily check-ins required.</p>
            @elseif($student->risk_level == 'Medium')
                <p>• <strong>Peer Mentoring:</strong> Connect with a student mentor.</p>
                <p>• <strong>Optional Workshop:</strong> Recommend "Study Skills" seminar.</p>
            @else
                <p>• <strong>Advanced Track:</strong> Suggest participation in the Dean's List project group.</p>
                <p>• <strong>Consistency:</strong> Maintain current study patterns.</p>
            @endif
        </div>
    </div>

    <table class="overall-container">
        <tr>
            <td style="width: 60%;"></td> <td class="overall-box">
                <span style="font-size: 14px; font-weight: bold; color: #1a237e;">OVERALL RISK</span><br>
                <div class="risk-badge bg-{{ strtolower($student->risk_level) }}">
                    {{ strtoupper($student->risk_level) }}
                </div>
                <p style="font-size: 10px; margin-top: 10px; color: #777;">
                    Based on weighted average of assessment components.
                </p> 
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #888;">
        <p>This is a system-generated report for academic monitoring purposes &copy; 2026</p>
    </div>
</body>
</html>