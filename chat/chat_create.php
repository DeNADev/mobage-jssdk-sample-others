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
 
require_once("../config.php");
require_once("./client_credentials.php");
require_once("./create_channel.php");
require_once("./get_friend_list.php");
require_once("./add_member_to_channel.php");
require_once("./post_chat_message.php");


// prepare access_token
$authorization_code_access_token = $_SESSION['access_token'];

$client_credentials_result = getClientAccessToken();
$client_access_token       = $client_credentials_result['access_token'];


// create Channel
$channel_name      = 'MyFirstChannel';
$channel_shortname = 'MFC';

$created_channel = createChatChannel($channel_name, $channel_shortname, $client_access_token);
error_log('created_channel is ' . print_r($created_channel,true) . "\n", 3, ERROR_LOG_PATH);


// get Friends List
$channel_id = $created_channel['id'];

$friends = getFriendList($authorization_code_access_token);
$entry   = $friends['entry'];
error_log('entry is ' . print_r($entry,true) . "\n", 3, ERROR_LOG_PATH);


// create Chat Member Format
$chat_members = array();
for($i = 0; $i < count($entry); ++$i) {
    $global_id = $entry[$i]['id'];
    error_log('global_id is ' . $global_id . "\n", 3, ERROR_LOG_PATH);
    $local_id = substr($global_id, strpos($global_id, ':') + 1); // remove prefix
    $member = array(
            'objectType' => 'person',
            'localId' => $local_id);
    array_push($chat_members, $member);
}
$viewer = array(
        'objectType' => 'person',
        'localId' => $_SESSION['user_id']
);
array_push($chat_members, $viewer);
error_log('chat_members is ' . print_r($chat_members,true) . "\n", 3, ERROR_LOG_PATH);

addMemberToChannel($chat_members, $channel_id, $client_access_token);


// post Message from Game
$message = 'This is the first message from application!';

postChatMessage($message, $channel_id, $client_access_token);

$redirect_url = BASE_URI . '/mobage-jssdk-sample-others/chat/chat.php?channel_id=' . $channel_id;
header("Location: $redirect_url");

?>
