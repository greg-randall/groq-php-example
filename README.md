Super basic example of an API call to Groq in PHP.

Setup: Duplicate the file 'blank_keys.php', name it 'keys.php', and add your Groq API key in.

funcations.php has the functions:
*  groq_call($prompt, $model, $temperature, $groq_key)
*  groq_cost($response)

As of 3/27/2024, Groq seems to have three models available:
*  mixtral-8x7b-32768
*  llama2-70b-4096
*  gemma-7b-it
