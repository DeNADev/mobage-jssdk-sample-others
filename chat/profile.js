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
    var clientData = {};

    document.addEventListener('DOMContentLoaded', function() {

    });

    document.addEventListener('mobageReady', function() {
        var html = document.getElementsByTagName('html')[0];

        clientData = {
            clientId     : html.dataset.clientId,
            redirectUri  : html.dataset.redirectUri,
            logoutUri    : html.dataset.logoutUri,
            state        : html.dataset.state,
            sessionState : html.dataset.sessionState,
            user_id      : html.dataset.userId
        };

        mobage.init({ clientId: clientData.clientId, redirectUri: clientData.redirectUri });

        if (clientData.sessionState) {
            mobage.event.subscribe('oauth.sessionStateChange', clientData.sessionState, function(result) {
                if (result === 'changed') {
                    location.href = clientData.logoutUri;
                }   
            });
            console.log('sessionState is OK');
            getPeople(clientData.user_id);

        } else if (clientData.state) {
            var params = { state: clientData.state};
            mobage.oauth.getConnectedStatus(params, function(err, result) {
                if (result) {
                    console.log('getConnectedStatus is OK');
                    getPeople(clientData.user_id);
                } else {
                    console.log('getConnectedStatus failed');
                }
            });
        }
        console.log('mobageReady is OK');
    });

    function getPeople (user_id) {
        mobage.api.people.get({
            userId: user_id, groupId: '@self',
            fields: ['id', 'nickname', 'profileUrl', 'thumbnailUrl'
            ]},
            function(err, people) {
                console.log(people);
                var mobageNickname = document.getElementById('mobage-nickname');
                if ("innerText" in mobageNickname) {
                    mobageNickname.innerText = people.nickname;
                } else {
                    mobageNickname.textContent = people.nickname;
                }
                document.getElementById('mobage-profile').href = people.profileUrl;
                document.getElementById('mobage-thumbnail-url').src = people.thumbnailUrl;
            }
        );
    }

})(this.self || global);
