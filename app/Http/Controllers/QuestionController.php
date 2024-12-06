<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index($quizId)
    {
        $questions = Question::with('answers')->where('quiz_id', $quizId)->get();

        return response()->json([
            'statusCode' => 200,
            'message' => 'Questions retrieved successfully',
            'data' => $questions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'answers' => 'required|array|min:2', // Minimal 2 jawaban
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        if ($request->hasFile('question_image')) {
            $imageName = time() . '_' . $request->file('question_image')->getClientOriginalName();
            $imagePath = $request->file('question_image')->storeAs('question_images', $imageName, 'public');
            $validated['question_image'] = 'public/' . $imagePath;
        }

        // Buat pertanyaan
        $question = Question::create([
            'quiz_id' => $validated['quiz_id'],
            'question_text' => $validated['question_text'],
            'question_image' => $validated['question_image'] ?? null,
        ]);

        // Tambahkan jawaban
        foreach ($validated['answers'] as $answer) {
            $question->answers()->create($answer);
        }

        return response()->json([
            'statusCode' => 201,
            'message' => 'Question and answers created successfully',
            'data' => $question->load('answers'),
        ]);
    }

    public function show($id)
    {
        $question = Question::with('answers')->find($id);

        if (!$question) {
            return response()->json(
                [
                    'statusCode' => 404,
                    'message' => 'Question not found',
                    'data' => null,
                ],
                404,
            );
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Question retrieved successfully',
            'data' => $question,
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(
                [
                    'statusCode' => 404,
                    'message' => 'Question not found',
                    'data' => null,
                ],
                404,
            );
        }

        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'answers' => 'required|array|min:2',
            'answers.*.answer_text' => 'required|string',
            'answers.*.is_correct' => 'required|boolean',
        ]);

        if ($request->hasFile('question_image')) {
            if ($question->question_image) {
                $oldImagePath = public_path('storage/' . $question->question_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $imageName = time() . '_' . $request->file('question_image')->getClientOriginalName();
            $imagePath = $request->file('question_image')->storeAs('question_images', $imageName, 'public');
            $validated['question_image'] = 'public/' . $imagePath;
        }

        // Update pertanyaan
        $question->update([
            'question_text' => $validated['question_text'],
            'question_image' => $validated['question_image'] ?? $question->question_image,
        ]);

        // Update jawaban (hapus semua dulu, lalu tambahkan kembali)
        $question->answers()->delete();
        foreach ($validated['answers'] as $answer) {
            $question->answers()->create($answer);
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Question and answers updated successfully',
            'data' => $question->load('answers'),
        ]);
    }

    public function destroy($id)
    {
        $question = Question::find($id);
        if (!$question) {
            return response()->json(
                [
                    'statusCode' => 404,
                    'message' => 'Question not found',
                    'data' => null,
                ],
                404,
            );
        }

        $question->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Question deleted successfully',
            'data' => null,
        ]);
    }

    public function submitSingleQuestion(Request $request, $questionId)
    {
        // Validasi input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'answer_id' => 'required|exists:answers,id',
        ]);

        // Cari soal berdasarkan ID
        $question = Question::find($questionId);

        if (!$question) {
            return response()->json(
                [
                    'statusCode' => 404,
                    'message' => 'Question not found',
                ],
                404,
            );
        }

        // Cek jawaban yang benar
        $correctAnswer = $question->answers()->where('is_correct', true)->first();
        $isCorrect = $correctAnswer && $correctAnswer->id == $validated['answer_id'];

        // Hitung EXP yang didapat (hanya jika jawaban benar)
        $earnedExp = $isCorrect ? 200 : 0; // 10 EXP per jawaban benar

        // Update EXP, Level, dan League pengguna
        $user = User::findOrFail($validated['user_id']);
        $user->exp += $earnedExp;

        // Update level dan league berdasarkan EXP
        $user->level = User::determineLevel($user->exp);
        $user->league = User::determineLeague($user->exp);

        $user->save();

        return response()->json([
            'statusCode' => 200,
            'message' => $isCorrect ? 'Correct answer!' : 'Incorrect answer!',
            'data' => [
                'is_correct' => $isCorrect,
                'earned_exp' => $earnedExp,
                'user' => [
                    'id' => $user->id,
                    'exp' => $user->exp,
                    'level' => $user->level,
                    'league' => $user->league,
                ],
            ],
        ]);
    }
}
