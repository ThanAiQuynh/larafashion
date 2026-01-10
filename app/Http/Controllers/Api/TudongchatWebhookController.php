<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatbotConfig;
use App\Models\ChatbotLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TudongchatWebhookController extends Controller
{
    /**
     * Handle webhook from Tudongchat
     * 
     * Receives lead data when chatbot collects customer info
     */
    public function handle(Request $request): JsonResponse
    {
        // Get chatbot config
        $config = ChatbotConfig::getConfig();
        
        // Validate webhook secret if configured
        if ($config && $config->webhook_secret) {
            $providedSecret = $request->header('X-Webhook-Secret') 
                ?? $request->input('secret');
            
            if (!$config->validateSecret($providedSecret ?? '')) {
                Log::warning('Tudongchat webhook: Invalid secret', [
                    'ip' => $request->ip(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid webhook secret',
                ], 401);
            }
        }

        // Log incoming webhook for debugging
        Log::info('Tudongchat webhook received', [
            'payload' => $request->all(),
            'ip' => $request->ip(),
        ]);

        // Extract and validate required data
        $payload = $request->all();
        
        // Check if we have any useful customer data
        $hasCustomerData = !empty($payload['phone']) 
            || !empty($payload['customer_phone'])
            || !empty($payload['email'])
            || !empty($payload['customer_email'])
            || !empty($payload['name'])
            || !empty($payload['customer_name']);

        if (!$hasCustomerData) {
            return response()->json([
                'success' => false,
                'message' => 'No customer data provided',
            ], 400);
        }

        // Create lead from webhook payload
        try {
            $lead = ChatbotLead::createFromWebhook($payload);

            Log::info('Tudongchat lead created', [
                'lead_id' => $lead->id,
                'phone' => $lead->customer_phone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead received successfully',
                'lead_id' => $lead->id,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Tudongchat webhook error', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process lead',
            ], 500);
        }
    }

    /**
     * Health check endpoint for webhook
     */
    public function ping(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Webhook endpoint is active',
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
