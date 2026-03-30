<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IntentQueryService;

class ChatbotController extends Controller
{
    protected IntentQueryService $intentService;

    public function __construct(IntentQueryService $intentService)
    {
        $this->intentService = $intentService;
    }

    /**
     * Show the chatbot page.
     */
    public function index()
    {
        return view('chatbot.chat');
    }

    /**
     * Handle incoming chat message (AJAX POST).
     */
    public function message(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $userMessage = trim($request->input('message'));

        // 1. Parse intent from message
        $parsed = $this->intentService->parseIntent($userMessage);

        // 2. Handle unknown intent
        if ($parsed['intent'] === 'unknown') {
            return response()->json([
                'success'  => true,
                'intent'   => 'unknown',
                'label'    => null,
                'students' => [],
                'count'    => null,
                'reply'    => $this->unknownReply($userMessage),
            ]);
        }

        // 3. Run query
        $result = $this->intentService->query($parsed);

        // 4. Build reply message
        $reply = $this->buildReply($result, $parsed);

        return response()->json([
            'success'  => true,
            'intent'   => $result['intent'],
            'label'    => $result['label'],
            'students' => $result['students'],
            'count'    => $result['count'],
            'reply'    => $reply,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────

    private function buildReply(array $result, array $parsed): string
    {
        $label = $result['label'];

        // Count intent
        if ($result['intent'] === 'count') {
            return "There are **{$result['count']}** students" .
                   ($parsed['course'] ? " in **" . ucfirst($parsed['course']) . "**" : '') . ".";
        }

        $count = count($result['students']);

        if ($count === 0) {
            return "No students found for: **{$label}**." .
                   ($parsed['course'] ? " Make sure the course name matches exactly." : '');
        }

        return "Found **{$count}** student(s) — **{$label}**.";
    }

    private function unknownReply(string $msg): string
    {
        return "I didn't quite understand that. Try asking things like:\n" .
               "• \"Who is likely to fail in Biology?\"\n" .
               "• \"Show top students in Chemistry\"\n" .
               "• \"Who has the lowest attendance?\"\n" .
               "• \"How many students are in Physics?\"\n" .
               "• \"List all female students in year 2\"";
    }
}