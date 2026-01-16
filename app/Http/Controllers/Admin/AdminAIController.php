<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminAIService;
use Illuminate\Http\Request;

class AdminAIController extends Controller
{
    protected AdminAIService $aiService;

    public function __construct(AdminAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display the AI Assistant page
     */
    public function index()
    {
        return view('admin.ai-assistant.index');
    }

    /**
     * Process a question from admin
     */
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
        ]);

        $response = $this->aiService->ask($request->question);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($response);
        }

        return back()->with('ai_response', $response);
    }
}
