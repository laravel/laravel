<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Test;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use App\Models\TestAttempt;

class QuestionController extends Controller
{
    // Update a question
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        $question->update($request->only(['question', 'options', 'correct_answer']));
        return response()->json($question);
    }

    // Delete a question
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();
        return response()->json(['message' => 'Question deleted.']);
    }

    public function upload(Request $request)
    {
        // 1. Validate file upload
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'test_id' => 'required|exists:tests,id',
        ]);
    
        // 2. Store uploaded file
        $file = $request->file('file');
        $folderPath = 'question_uploads';
        if (!Storage::disk('public')->exists($folderPath)) {
            Storage::disk('public')->makeDirectory($folderPath, 0755, true, true);
        }
        $filePath = $file->store($folderPath, 'public'); // Store in public disk, accessible via URL
        // 3. Parse PDF content using the uploaded file directly
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getRealPath()); // Use the temporary file path
        $extractedText = $pdf->getText();
    
        // 4. Extract questions (you need to implement this)
        $questions = $this->extractQuestionsFromText($extractedText);
    
        // 5. Save questions to database
        $savedQuestions = [];
        foreach ($questions as $questionData) {
            $question = Question::create([
                'test_id' => $request->test_id,
                'question' => $questionData['question'],
                'options' => $questionData['options'], // assuming `Question` has `casts` in model
                'correct_answer' => $questionData['correct_answer'],
            ]);
            $savedQuestions[] = $question;
        }
    
        // 6. Update test question count
        $test = Test::find($request->test_id);
        $test->update(['total_questions' => $test->questions()->count()]);
    
        return response()->json([
            'message' => 'Questions uploaded successfully',
            'count' => count($savedQuestions),
            'questions' => $savedQuestions
        ], 201);
    }

    private function extractQuestionsFromText(string $text): array
    {
        // Separate questions and answers
        [$questionsPart, $answersPart] = explode("Set 1: Number Series Answers", $text);
    
        // Extract answers
        preg_match_all('/(\d+)\.\s*([a-d])/', $answersPart, $answerMatches, PREG_SET_ORDER);
        $answers = [];
        foreach ($answerMatches as $match) {
            $answers[$match[1]] = strtoupper($match[2]); // e.g., 1 => B
        }
    
        // Extract questions
        preg_match_all('/(\d+)\.\s*(.*?)\s+a\)\s*(.*?)\s+b\)\s*(.*?)\s+c\)\s*(.*?)\s+d\)\s*(.*?)(?=\n\d+\.|\z)/is', $questionsPart, $questionMatches, PREG_SET_ORDER);
    
        $parsed = [];
        foreach ($questionMatches as $match) {
            $num = $match[1];
            $questionText = trim(preg_replace('/\s+/', ' ', $match[2]));
            $options = [
                'A' => trim($match[3]),
                'B' => trim($match[4]),
                'C' => trim($match[5]),
                'D' => trim($match[6]),
            ];
            $correct = $answers[$num] ?? null;
    
            if ($correct) {
                $parsed[] = [
                    'question' => $questionText,
                    'options' => $options,
                    'correct_answer' => $correct,
                ];
            }
        }
    
        return $parsed;
    }
}
