<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2014-2015 DeNA Co., Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
 
require_once('../config.php');

function getClientAccessToken () {

    global $CLIENT_SECRETS;

    $token_endpoint_request_params = [ 
        'grant_type'    => 'client_credentials',
        'client_id'     => CLIENT_ID,
        'client_secret' => $CLIENT_SECRETS[CLIENT_ID]
    ];

    $headers = [ 
        'Content-Type: application/x-www-form-urlencoded',
        'Connection: close',
    ]; 

    $options = [ 
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query($token_endpoint_request_params),
            'header'  => implode("\r\n", $headers),
        ]   
    ];

    $raw_token_response = file_get_contents(TOKEN_ENDPOINT_URL, false, stream_context_create($options));

    $token_response    = json_decode($raw_token_response);
    error_log('token_response ' . print_r($token_response,true)  . "\n", 3, ERROR_LOG_PATH);
    
    $access_token      = $token_response->access_token;
    $expires_time      = time() + $token_response->expires_in;

    $result = [
        'access_token' => $access_token,
        'expires_time' => $expires_time
    ];
    return $result;
}
?>
