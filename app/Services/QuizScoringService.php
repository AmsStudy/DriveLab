<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Support\Facades\DB;

class QuizScoringService
{
    /**
     * Finalize and calculate score for a quiz attempt.
     */
    public static function finalize(QuizAttempt $attempt, array $userAnswers = [], bool $isForceSubmitted = false): void
    {
        $quiz = $attempt->quiz;
        $questions = $quiz->questions()->with('options')->get();

        $totalPointsEarned = 0;
        $correctCount = 0;
        $wrongCount = 0;

        // Clean userAnswers (remove metadata like __flagged)
        unset($userAnswers['__flagged']);

        DB::transaction(function () use ($attempt, $questions, $userAnswers, $isForceSubmitted, &$totalPointsEarned, &$correctCount, &$wrongCount) {
            // Delete existing answers if any re-calculation
            $attempt->answers()->delete();

            foreach ($questions as $q) {
                $rawAns = $userAnswers[$q->id] ?? null;
                $isCorrect = false;
                $points = 0;
                $selectedOptionId = null;
                $selectedOptionIds = null;

                if ($q->isMultiSelect()) {
                    $correctOptionIds = $q->options->where('is_correct', true)->pluck('id')->values()->all();
                    $userAnsIds = [];
                    if (is_array($rawAns)) {
                        $userAnsIds = array_values(array_map('intval', $rawAns));
                    } elseif ($rawAns) {
                        $userAnsIds = [(int)$rawAns];
                    }

                    $selectedOptionIds = $userAnsIds;
                    $selectedOptionId = $userAnsIds[0] ?? null;

                    if (!empty($userAnsIds)) {
                        $correctSelected = array_intersect($userAnsIds, $correctOptionIds);
                        $wrongSelected = array_diff($userAnsIds, $correctOptionIds);

                        // If user selected exactly all correct options and no wrong options
                        if (count($wrongSelected) === 0 && count($correctSelected) === count($correctOptionIds)) {
                            $isCorrect = true;
                            $points = $q->points;
                            $correctCount++;
                        } elseif (count($wrongSelected) === 0 && count($correctSelected) > 0) {
                            // Partial credit: proportional to correctly selected items
                            $ratio = count($correctSelected) / max(1, count($correctOptionIds));
                            $points = (int) round($q->points * $ratio);
                            $wrongCount++;
                        } else {
                            $wrongCount++;
                        }
                    } else {
                        $wrongCount++;
                    }
                } else {
                    $selectedOptionId = $rawAns ? (int)(is_array($rawAns) ? ($rawAns[0] ?? 0) : $rawAns) : null;
                    if ($selectedOptionId) {
                        $selectedOptionIds = [$selectedOptionId];
                        $selectedOption = $q->options->firstWhere('id', (int)$selectedOptionId);
                        if ($selectedOption && $selectedOption->is_correct) {
                            $isCorrect = true;
                            $points = $q->points;
                            $correctCount++;
                        } else {
                            $wrongCount++;
                        }
                    } else {
                        $wrongCount++;
                    }
                }

                if ($points > 0) {
                    $totalPointsEarned += $points;
                }

                QuizAttemptAnswer::create([
                    'quiz_attempt_id'     => $attempt->id,
                    'quiz_question_id'    => $q->id,
                    'selected_option_id'  => $selectedOptionId,
                    'selected_option_ids' => $selectedOptionIds,
                    'is_correct'          => $isCorrect,
                    'points_earned'       => $points,
                ]);
            }

            $completedAt = now();
            $durationSeconds = max(1, abs((int) $completedAt->diffInSeconds($attempt->started_at)));
            $possiblePoints = $attempt->total_points_possible > 0 ? $attempt->total_points_possible : $questions->sum('points');
            $score = $possiblePoints > 0 ? round(($totalPointsEarned / $possiblePoints) * 100, 1) : 0;

            $attempt->update([
                'completed_at'          => $completedAt,
                'duration_seconds'      => $durationSeconds,
                'total_points_earned'   => $totalPointsEarned,
                'total_points_possible' => $possiblePoints,
                'correct_answers_count' => $correctCount,
                'wrong_answers_count'   => $wrongCount,
                'score'                 => $score,
                'is_force_submitted'    => $isForceSubmitted || (bool) $attempt->is_force_submitted,
                'is_locked'             => false,
                'penalty_end_time'      => null,
            ]);
        });
    }
}
