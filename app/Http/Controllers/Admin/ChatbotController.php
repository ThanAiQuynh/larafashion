<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConfig;
use App\Models\ChatbotLead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatbotController extends Controller
{
    /**
     * Display chatbot configuration page
     */
    public function config(): View
    {
        $config = ChatbotConfig::getConfig() ?? new ChatbotConfig();
        
        return view('admin.chatbot.config', compact('config'));
    }

    /**
     * Update chatbot configuration
     */
    public function updateConfig(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'script_code' => 'nullable|string',
            'is_active' => 'boolean',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        $config = ChatbotConfig::getConfig();
        
        if (!$config) {
            $config = new ChatbotConfig();
        }

        $config->fill($validated);
        $config->save();

        return redirect()
            ->route('admin.chatbot.config')
            ->with('success', 'Cấu hình Chatbot đã được cập nhật!');
    }

    /**
     * Generate new webhook secret
     */
    public function generateSecret(): RedirectResponse
    {
        $config = ChatbotConfig::getConfig();
        
        if (!$config) {
            $config = new ChatbotConfig();
        }

        $config->webhook_secret = Str::random(32);
        $config->save();

        return redirect()
            ->route('admin.chatbot.config')
            ->with('success', 'Webhook secret mới đã được tạo!');
    }

    /**
     * Display leads list
     */
    public function leads(Request $request): View
    {
        $query = ChatbotLead::query()->orderBy('created_at', 'desc');

        // Filter by processed status
        if ($request->has('status')) {
            if ($request->status === 'processed') {
                $query->processed();
            } elseif ($request->status === 'unprocessed') {
                $query->unprocessed();
            }
        }

        // Search by phone or name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $leads = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => ChatbotLead::count(),
            'unprocessed' => ChatbotLead::unprocessed()->count(),
            'today' => ChatbotLead::whereDate('created_at', today())->count(),
        ];

        return view('admin.chatbot.leads', compact('leads', 'stats'));
    }

    /**
     * View single lead details
     */
    public function showLead(ChatbotLead $lead): View
    {
        return view('admin.chatbot.lead-detail', compact('lead'));
    }

    /**
     * Mark lead as processed
     */
    public function markProcessed(ChatbotLead $lead): RedirectResponse
    {
        $lead->markAsProcessed();

        return redirect()
            ->back()
            ->with('success', 'Lead đã được đánh dấu xử lý!');
    }

    /**
     * Delete a lead
     */
    public function deleteLead(ChatbotLead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()
            ->route('admin.chatbot.leads')
            ->with('success', 'Lead đã được xóa!');
    }
}
