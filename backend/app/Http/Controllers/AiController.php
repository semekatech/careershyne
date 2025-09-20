<?php

namespace App\Http\Controllers;

use App\Services\AIReviewService;
use Exception;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected $aiReview;

    public function __construct(AIReviewService $aiReview)
    {
        $this->aiReview = $aiReview;
    }

    public function uploadCV(Request $request)
    {
        try {
            $count = \DB::table('ai_reviews')
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($count >= 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Daily limit reached for Careershyne AI. Please try again tomorrow.',
                ], 422);
            }
            // 1. Validate
            $request->validate([
                'file' => 'required|mimetypes:application/pdf|max:5120',
            ]);
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'rb');
            $magic = fread($handle, 4);
            fclose($handle);

            if ($magic !== '%PDF') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file format, only genuine PDFs allowed.'
                ], 422);
            }

            // 2. Extract text
            [$path, $text] = $this->aiReview->extractText($request->file('file'));

            if (empty(trim($text))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to extract text from the PDF. Please upload a text-based CV.',
                ], 422);
            }

            // 3. Analyze with AI
            $json = $this->aiReview->analyzeCV($text);
            if (isset($json['is_cv']) && $json['is_cv'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => $json['message'] ?? 'This file does not look like a CV.',
                ], 422);
            }

            // 4. Normalize
            $result = $this->aiReview->normalizeResult($json);

            // 5. Save
            $this->aiReview->saveReview($json, $path, $result);

            return response()->json([
                'success'   => true,
                'message'   => 'CV uploaded and reviewed successfully.',
                'file_path' => asset('storage/' . $path),
                'file_name' => basename($path),
                'review'    => $result,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function coveletterGenerator(Request $request)
    {
        try {
            info('All request input:', $request->all());

            // 1. Validate the CV file
            $request->validate([
                'cv_file' => 'required|mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
            ]);

            // Validate the CV file's PDF magic number to prevent fake PDFs
            $cvFile = $request->file('cv_file');
            if ($cvFile->getMimeType() === 'application/pdf') {
                $handle = fopen($cvFile->getPathname(), 'rb');
                $magic = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid CV file format. Please upload a genuine PDF.'
                    ], 422);
                }
            }

            // Extract and validate text from the CV
            // The method should be on the `aiReview` service, but we use a placeholder here.
            [$cvFilePath, $cvText] = $this->aiReview->extractText($cvFile);
            if (empty(trim($cvText))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to extract text from the CV. Please upload a text-based file.',
                ], 422);
            }

            // Log CV information
            info('CV Text: ' . $cvText);
            info('CV File Path: ' . $cvFilePath);

            // 2. Capture and log job details
            if ($request->filled('job_text')) {
                $jobDescription = $request->input('job_text');
                info('Job Description (Pasted Text): ' . $jobDescription);
            } elseif ($request->has('job_url') && !empty(trim($request->input('job_url')))) {
                // Case: Job URL is provided
                $jobUrl = $request->input('job_url');
                // You would typically use a web scraping library here to get the content from the URL.
                info('Job URL: ' . $jobUrl);
            } elseif ($request->hasFile('job_pdf')) {
                // Case: Job description is a PDF file
                $request->validate([
                    'job_pdf' => 'required|mimetypes:application/pdf|max:5120',
                ]);
                $jobPdfFile = $request->file('job_pdf');

                // Validate the PDF magic number for the job file
                $handle = fopen($jobPdfFile->getPathname(), 'rb');
                $magic = fread($handle, 4);
                fclose($handle);
                if ($magic !== '%PDF') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid job PDF file format. Please upload a genuine PDF.'
                    ], 422);
                }

                [$jobFilePath, $jobText] = $this->aiReview->extractText($jobPdfFile);
                if (empty(trim($jobText))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to extract text from the job PDF. Please upload a text-based PDF.',
                    ], 422);
                }
                info('Job PDF Text: ' . $jobText);
                info('Job PDF File Path: ' . $jobFilePath);
            } else {
                // No job details provided
                return response()->json([
                    'success' => false,
                    'message' => 'No job details provided. Please paste a job description, provide a link, or upload a PDF.',
                ], 422);
            }

            // Your AI logic for generating the cover letter would go here
            $coverLetter = 'This is a sample cover letter based on the provided CV and job details.';

            // 3. Return a successful JSON response
            return response()->json([
                'success' => true,
                'message' => 'Cover letter generated successfully.',
                'cover_letter' => $coverLetter,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
