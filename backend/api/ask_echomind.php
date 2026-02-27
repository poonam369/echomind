<?php
// AI analysis logic
function analyzeThought($thought){
    $lower = strtolower($thought);
    if(strpos($lower, 'happy') !== false || strpos($lower, 'good') !== false){
        $sentiment = 'Positive';
    } elseif(strpos($lower, 'sad') !== false || strpos($lower, 'bad') !== false){
        $sentiment = 'Negative';
    } else {
        $sentiment = 'Neutral';
    }

    $insight = "Category inferred automatically.";

    return [
        'sentiment' => $sentiment,
        'insight' => $insight
    ];
}
