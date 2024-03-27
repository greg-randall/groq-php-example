<?php
  
  function groq_call( $prompt, $model, $temperature, $groq_key ) {
      // Define the data to be sent in the request
      $groq_key_data = array(
           "messages" => array(
               array(
                   "role" => "user",
                  "content" => $prompt 
              ) 
          ),
          "model" => $model,
          "temperature" => $temperature,
          "max_tokens" => 1024,
          "top_p" => 1,
          "stream" => false,
          "stop" => null 
      );
      
      // Initialize a new cURL session
      $ch = curl_init();
      
      // Set the URL for the request
      curl_setopt( $ch, CURLOPT_URL, 'https://api.groq.com/openai/v1/chat/completions' );
      // Set the option to return the result as a string
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      // Set the option to make a POST request
      curl_setopt( $ch, CURLOPT_POST, 1 );
      // Set the POST fields for the request
      curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $groq_key_data ) );
      
      // Define the headers for the request
      $headers    = array( );
      $headers[ ] = 'Content-Type: application/json';
      $headers[ ] = 'Authorization: Bearer ' . $groq_key;
      // Set the headers for the request
      curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
      
      // Execute the cURL session
      $result = curl_exec( $ch );
      // Decode the JSON response
      $result = json_decode( $result, true );
      // If there was an error with the cURL session, print the error
      if ( curl_errno( $ch ) ) {
          echo 'Error:' . curl_error( $ch );
      }
      // Close the cURL session
      curl_close( $ch );
      // Return the result of the request
      return ( $result );
  }
  
  function groq_cost( $response ) {
      // Define the cost per token for each model. 
      $groq_cost = array(
           'Llama' => array(
               'input' => 0.70 / 1000000,
              'output' => 0.80 / 1000000 
          ), // Llama 2 70B
          'Mixtral' => array(
               'input' => 0.27 / 1000000,
              'output' => 0.27 / 1000000 
          ), // Mixtral, 8x7B SMoE
          'Gemma' => array(
               'input' => 0.10 / 1000000,
              'output' => 0.10 / 1000000 
          ) // Gemma 7B
      );
      
      // Get the model from the response
      $model = $response[ 'model' ];
      
      $found = false;
      // Loop through each model in groq_cost
      // The prices on their site don't match up exactly with the model names they have (ie mixtral-8x7b-32768 and Mixtral, 8x7B SMoE)
      // So we're going to try to match the model name to the key
      foreach ( $groq_cost as $key => $value ) {
          // If the model is found in the key
          if ( stripos( $model, $key ) !== false ) {
              // Get the input and output costs for the model
              $input_cost  = $value[ 'input' ];
              $output_cost = $value[ 'output' ];
              $found       = true;
              break;
          }
      }
    
      // If the model was not found, use the most expensive model
      if ( !$found ) {
          $input_cost  = $groq_cost[ 'Llama' ][ 'input' ];
          $output_cost = $groq_cost[ 'Llama' ][ 'output' ];
      }
      
      // Get the number of prompt and completion tokens from the response
      $prompt_tokens     = $response[ 'usage' ][ 'prompt_tokens' ];
      $completion_tokens = $response[ 'usage' ][ 'completion_tokens' ];
      
      // Calculate the cost
      $cost = ( $prompt_tokens * $input_cost ) + ( $completion_tokens * $output_cost );
      
      // Return the cost in dollars
      return ( $cost );
  }
?>