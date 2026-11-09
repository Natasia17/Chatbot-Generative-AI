<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Faq;
use App\Models\FaqVariant;
use App\Models\Product;
use App\Models\StockSnapshot;
use App\Models\PriceList;
use App\Models\Ticket;
use App\Models\BotEvent;
use App\Services\Ai\SemanticFaqMatcher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_customers' => Customer::count(),
            'total_conversations' => Conversation::count(),
            'total_messages' => Message::count(),
            'total_faqs' => Faq::count(),
            'total_products' => Product::count(),
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::whereIn('status', ['baru', 'diproses', 'eskalasi_manager'])->count(),
        ];

        $faqs = Faq::with('variants')->where('is_active', true)->get();
        $products = Product::with(['latestStock', 'latestPrice'])->where('is_active', true)->get();
        $customers = Customer::with('defaultSales')->get();
        $recentMessages = Message::with('conversation.customer')->latest()->take(10)->get();
        $tickets = Ticket::with(['customer', 'assignedSales'])->latest()->take(10)->get();

        return view('dashboard.index', compact('stats', 'faqs', 'products', 'customers', 'recentMessages', 'tickets'));
    }

    public function simulateChat(Request $request, SemanticFaqMatcher $matcher): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        $query = $validated['message'];
        $startTime = microtime(true);
        $match = $matcher->match($query);
        $responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);

        $customer = null;
        if (!empty($validated['customer_id'])) {
            $customer = Customer::find($validated['customer_id']);
        } else {
            $customer = Customer::first();
        }

        $isMatched = ($match['status'] === 'matched');
        $confidenceScore = $match['confidence'] ?? 0.0;
        $matchedFaq = $match['faq'] ?? null;

        if ($isMatched && $matchedFaq) {
            $reply = $match['answer'] ?? $matchedFaq->answer;
        } elseif ($match['status'] === 'clarification') {
            $suggestTitle = $matchedFaq?->canonical_question;
            $reply = "Apakah maksud Anda mengenai:\n\"{$suggestTitle}\"?\n\nJika benar, berikut informasinya:\n" . ($matchedFaq?->answer ?? '');
        } else {
            $reply = "Mohon maaf, pesan Anda belum dapat dijawab otomatis oleh asisten bot. Pertanyaan Anda telah diteruskan ke Sales Pendamping apotek Anda.";
        }

        // Catat ke pesan & bot event untuk keperluan audit pengujian
        $conversation = null;
        if ($customer) {
            $conversation = Conversation::firstOrCreate(
                ['customer_id' => $customer->id],
                ['current_state' => 'IDLE', 'last_inbound_at' => now()]
            );
        }

        $intentDetected = 'UNKNOWN';
        $matchedFaqId = null;

        if ($matchedFaq) {
            $intentDetected = !empty($matchedFaq->id) ? 'FAQ_' . $matchedFaq->id : ($matchedFaq->kategori ?? 'GENERAL');
            $matchedFaqId = !empty($matchedFaq->id) ? $matchedFaq->id : null;
        }

        BotEvent::create([
            'conversation_id' => $conversation?->id,
            'raw_query' => $query,
            'intent_detected' => $intentDetected,
            'confidence_score' => $confidenceScore,
            'matched_faq_id' => $matchedFaqId,
            'outcome' => $isMatched ? 'answered_faq' : ($match['status'] === 'clarification' ? 'clarification_offered' : 'escalated_to_sales'),
            'response_time_ms' => $responseTimeMs,
        ]);

        return response()->json([
            'query' => $query,
            'reply' => $reply,
            'matched' => $isMatched || ($match['status'] === 'clarification'),
            'confidence_score' => $confidenceScore,
            'matched_faq' => $matchedFaq ? [
                'id' => $matchedFaq->id ?? 0,
                'canonical_question' => $matchedFaq->canonical_question,
                'kategori' => $matchedFaq->kategori,
            ] : null,
            'response_time_ms' => $responseTimeMs,
            'customer' => $customer ? $customer->nama_apotek : 'Umum',
        ]);
    }
}
