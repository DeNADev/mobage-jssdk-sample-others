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
require_once('../common.php');
require_once('../JWT/JWT.php');

define('SENT_INVITATION_LOG_PATH', '../log/sent_invitation.log');

global $CLIENT_SECRETS;
error_log('Sent Invitation Notification from Platfrom has come !!' . "\n", 3, ERROR_LOG_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    // load json from request body
    $jwt_body = file_get_contents('php://input');
    if (empty($jwt_body)) {
        echo json_encode([ 'result' => false ]);
        return;
    }

    // decode JSON Web Token
    try {
        checkSignatureAlgorithm($jwt_body);
        $jwt_claims = JWT::decode($jwt_body, PUBLIC_KEY);
    } catch (Exception $e) {
        error_log('jwt_decode failed because ' . $e . "\n", 3, ERROR_LOG_PATH);
        render_error_json();
    }


    //----------------------
    // Validate JSON Web Token

    $unixtime = time();
    // validate 'iss'(Issuer Claim)
    if (!($jwt_claims->iss === ISSUER_FOR_ASYNC_CONFIRMATION)) {
        error_log('Issuer Claim is invalid. ' .
            'iss=' . $jwt_claims->iss . ', ISSUER=' . ISSUER_FOR_ASYNC_CONFIRMATION . "\n", 3, ERROR_LOG_PATH);
        render_error_json();
    }
    // validate 'aud'(Audience Claim)
    if (!(isset($CLIENT_SECRETS[$jwt_claims->aud]))) {
        error_log('Audience Claim is invalid. ' .
            'aud=' . $jwt_claims->aud . "\n", 3, ERROR_LOG_PATH);
        render_error_json();
    }
    // validate 'iat'(Issued At)
    if (!($jwt_claims->iat <= $unixtime)) {
        error_log('Issed At is newer than now. ' .
            'iat=' . $jwt_claims->iat . ', now=' . $unixtime . "\n", 3, ERROR_LOG_PATH);
        render_error_json();
    }


    $result = [ 
        'From'           => 'Platform',
        'inviterUserId'  => $jwt_claims->event->inviterUserId,
        'inviteeUserIds' => $jwt_claims->event->inviteeUserIds
    ];  

    error_log(json_encode($result) . "\n", 3, SENT_INVITATION_LOG_PATH);

    render_json([ 'success' => true ]);
}

function render_json($params) {
    echo json_encode($params);
    exit;
}

function render_error_json() {
    http_response_code(400);
    return render_json([ 'success' => false ]);
}
