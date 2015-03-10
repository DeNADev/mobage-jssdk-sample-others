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
 
(function() {
    console.log('invite.js was called.');

    var inviteButton, clientData;

    var html = document.getElementsByTagName('html')[0];
    clientData = {
        clientId     : html.dataset.clientId,
        redirectUri  : html.dataset.redirectUri,
        logoutUri    : html.dataset.logoutUri,
        state        : html.dataset.state,
        sessionState : html.dataset.sessionState
    };

    document.addEventListener("mobageReady", function() {
        mobage.init({
            clientId:    clientData.clientId,
            redirectUri: clientData.redirectUri
        });

        inviteButton = document.getElementById('invite');

        if (clientData.sessionState) {
            mobage.event.subscribe('oauth.sessionStateChange', clientData.sessionState, function(result) {
                if (result === 'changed') {
                    location.href = clientData.logoutUri;
                }   
            }); 
            console.log('sessionState is OK');

            callFriendInvitation();

        } else if (clientData.state) {
            var params = { state : clientData.state };
            mobage.oauth.getConnectedStatus(params, function(err, result) {
                if (result) {
                    sendToRedirectURI(result);
                    callFriendInvitation();
                } else {
                    console.log('getConnected Status Error');
                    inviteButton.style.cssText = "visibility:hidden";
                }
            });
        }
    });

    function callFriendInvitation() {
        inviteButton.style.cssText = "visibility:visible";
        inviteButton.addEventListener('click', function() {
            mobage.ui.open(
                "invitation",
                {},
                function (error, result) {
                    if (error) {
                        if (error.error == "user_canceled") {
                            window.alert('招待がキャンセルされました');
                        } else {
                            window.alert('招待が失敗しました');
                        }
                    } else {
                        if (result.channel == 'mobage') {
                            sendInvitationResult(result);
                        }
                    }
                }
            );
        }, false);
    }

    // 友達招待リクエスト to GameServer
    function sendInvitationResult(result) {
        console.log('sendInvitationResult was called.');
        var req = new XMLHttpRequest();
        req.open('POST', 'sent_invitation.php');
        req.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        req.addEventListener('load', function() {
            // ログイン処理後のハンドリング
            showInvitationSuccessfulDialog(req, result);
        } , false);
        req.send(result.signedResponse);
    }

    // 友達招待の送信成功ダイアログ
    function showInvitationSuccessfulDialog(req, result) {
        if (req.status != 200) {
            window.alert("招待が送信されました。アイテムの付与にはしばらく時間が掛かります");
            return;
        }
        var res = JSON.parse(req.response);
        window.alert(req.response);
    }


    function sendToRedirectURI(result) {

        var response = result.response;
        var payload  = {
            code          : response.code,
            state         : response.state,
            session_state : response.session_state
        };

        var req = new XMLHttpRequest();
        req.open('POST', clientData.redirectUri);
        req.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        req.addEventListener('load', function() {
            // ログイン処理後のハンドリング
        } , false);
        req.send(JSON.stringify(payload));
    }

})(this.self || global);
