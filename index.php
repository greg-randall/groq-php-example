<?php  
    include_once 'functions.php';
    include_once 'keys.php';

    // available models (3/27/2024): mixtral-8x7b-32768, llama2-70b-4096, gemma-7b-it

    $prompt         = "What's the best way to cook a hotdog?";
    $groq_result    = groq_call($prompt, 'mixtral-8x7b-32768', 2, $groq_key); //returns an array that we converted from a json
    $groq_text      = $groq_result["choices"][0]["message"]["content"];

    $cost = groq_cost($groq_result); // cost is in dollars 

    echo   "<h2>Original Prompt</h2>
            <pre>$prompt</pre>
            <hr>
            <h2>Groq Response</h2>
            <pre>$groq_text</pre>
            <hr>
            <h2>Cost</h2>
            ".round($cost *100 ,3)."&cent;"; // convert to cents and round to 3 decimal places

?>