<?php

namespace App\Http\Controllers;

use App\Models\MatchingPairItem;
use App\Models\MatchingPairs;
use Illuminate\Http\Request;

class MatchingPairController extends Controller
{
    /**
     * Get all MatchingPairs with their items
     */
    public function index()
    {
        $matchingPairs = MatchingPairs::with('items')->get();

        return response()->json([
            'success' => true,
            'data' => $matchingPairs
        ], 200);
    }

    /**
     * Store a new MatchingPair with its items
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'learning_path_id' => 'required|uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.question' => 'required|string',
            'items.*.answer' => 'required|string',
        ]);

        // Create MatchingPair
        $matchingPair = MatchingPairs::create([
            'learning_path_id' => $validatedData['learning_path_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
        ]);

        // Create MatchingPairItems
        foreach ($validatedData['items'] as $item) {
            MatchingPairItem::create([
                'matching_pair_id' => $matchingPair->id,
                'question' => $item['question'],
                'answer' => $item['answer'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Matching Pair created successfully',
            'data' => $matchingPair->load('items')
        ], 201);
    }

    public function submit(Request $request, $id)
{
    $validatedData = $request->validate([
        'answers' => 'required|array',
        'answers.*.question_id' => 'required|uuid',
        'answers.*.answer' => 'required|string',
    ]);

    // Ambil semua item yang terkait dengan MatchingPair tertentu
    $matchingPairItems = MatchingPairItem::where('matching_pair_id', $id)->get();
    
    $results = [];
    $correctCount = 0;

    foreach ($validatedData['answers'] as $answer) {
        $item = $matchingPairItems->where('id', $answer['question_id'])->first();

        if ($item) {
            $isCorrect = $item->answer === $answer['answer'];
            $results[] = [
                'question_id' => $item->id,
                'question' => $item->question,
                'user_answer' => $answer['answer'],
                'correct_answer' => $item->answer,
                'is_correct' => $isCorrect
            ];
            if ($isCorrect) {
                $correctCount++;
            }
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Matching Pair submission result',
        'data' => $results,
        'total_correct' => $correctCount,
        'total_questions' => $matchingPairItems->count(),
    ], 200);
}
}
