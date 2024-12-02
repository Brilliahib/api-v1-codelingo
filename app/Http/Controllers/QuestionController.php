<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index($quizId)
    {
        $questions = Question::where('quiz_id', $quizId)->get();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Questions retrieved successfully',
            'data' => $questions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'answers' => 'required|array', // Jawaban dalam format array
            'answers.*.text' => 'required|string', // Teks jawaban
            'answers.*.is_correct' => 'required|boolean', // Jawaban benar
        ]);

        $question = Question::create($validated);
        return response()->json([
            'statusCode' => 201,
            'message' => 'Question created successfully',
            'data' => $question
        ]);
    }

    public function show($id)
    {
        $question = Question::find($id);
        if (!$question) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Question not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Question retrieved successfully',
            'data' => $question
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Question not found',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'answers' => 'required|array',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        $question->update($validated);
        return response()->json([
            'statusCode' => 200,
            'message' => 'Question updated successfully',
            'data' => $question
        ]);
    }

    public function destroy($id)
    {
        $question = Question::find($id);
        if (!$question) {
            return response()->json([
                'statusCode' => 404,
                'message' => 'Question not found',
                'data' => null
            ], 404);
        }

        $question->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Question deleted successfully',
            'data' => null
        ]);
    }
}
