<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChatController extends Controller
{
    public function showMetaTemplates()
    {
        try {
            $response = Http::withToken(config('services.whatsapp.token'))
                ->timeout(30)
                ->get('https://graph.facebook.com/v18.0/' . config('services.whatsapp.business_account_id') . '/message_templates');

            $templates = [];

            if ($response->successful()) {
                $data = $response->json('data', []);

                $templates = collect($data)->map(function ($template) {
                    $bodyComponent = collect($template['components'] ?? [])->firstWhere('type', 'BODY');
                    $body = $bodyComponent['text'] ?? '';

                    $variables = [];
                    if ($body) {
                        preg_match_all('/\{\{(\d+)\}\}/', $body, $matches);
                        $variables = array_unique($matches[1]);
                    }

                    return [
                        'id' => $template['name'],
                        'template_name' => $template['name'],
                        'body' => $body,
                        'variables' => $variables,
                        'status' => $template['status'] ?? 'unknown',
                        'language' => $template['language'] ?? 'en',
                        'category' => $template['category'] ?? 'UTILITY'
                    ];
                })->toArray();
            }

            return view('filament.pages.whatsapp-templates', compact('templates'));
        } catch (\Exception $e) {
            Log::error('WhatsApp Templates Error: ' . $e->getMessage());
            return view('filament.pages.whatsapp-templates', ['templates' => []]);
        }
    }

    public function selectTemplate(Request $request)
    {
        return response()->json(['status' => 'Template selected', 'template' => $request->input('template_name')]);
    }
}
