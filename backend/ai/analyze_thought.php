<?php
// backend/ai/analyze_thought.php

function analyzeThought(string $text, string $category): array
{
    $clean = strtolower(trim($text));

    // Sentiment logic
    $positive = ['good','happy','progress','success','excited','productive','achieved'];
    $negative = ['stress','sad','angry','tired','anxious','problem','failed','confused'];

    $pos = 0;
    $neg = 0;

    foreach ($positive as $p) {
        if (strpos($clean, $p) !== false) $pos++;
    }
    foreach ($negative as $n) {
        if (strpos($clean, $n) !== false) $neg++;
    }

    if ($pos > $neg) $sentiment = 'Positive';
    elseif ($neg > $pos) $sentiment = 'Negative';
    elseif ($pos === 0 && $neg === 0) $sentiment = 'Neutral';
    else $sentiment = 'Mixed';

    // Keywords
    $stop = ['i','am','the','a','and','to','of','in','on','is','it','my','for'];
    $words = preg_split('/\W+/', $clean);
    $keywords = [];

    foreach ($words as $w) {
        if (strlen($w) > 3 && !in_array($w, $stop)) {
            $keywords[] = $w;
        }
    }

    $keywords = implode(', ', array_unique($keywords));

    // Summary
    $summary = implode(' ', array_slice(explode(' ', $text), 0, 12));
    if (strlen($summary) < strlen($text)) $summary .= '...';

    // Insight (simple + useful)
    $map = [
        'reflection' => 'Reflect on this again after 24 hours to see if your perspective changes.',
        'career'     => 'Track this decision. Career clarity improves with consistent reflection.',
        'stress'     => 'Stress indicates overload. Identify one task you can reduce today.',
        'focus'      => 'Distraction patterns reveal priority issues. Try time-blocking.',
        'learning'   => 'Learning improves when you revisit concepts within 48 hours.',
        'goals'      => 'Every goal needs a deadline and weekly checkpoint.',
        'ideas'      => 'Review this idea after one week before acting on it.',
        'personal'   => 'Personal thoughts help identify emotional patterns over time.',
        'gratitude'  => 'Gratitude improves emotional stability when practiced daily.'
    ];

    $insight = $map[strtolower($category)] ?? 
               'This thought contributes to your long-term self-analysis.';

    return [
        'sentiment' => $sentiment,
        'keywords'  => $keywords ?: null,
        'summary'   => $summary,
        'insight'   => $insight
    ];
}
