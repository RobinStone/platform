<h1 id="h1">labuton</h1>
<button onclick="send_com('com=test~|text=ROBIN BOBIN~|status=ofigitelno')">send</button>
<script>
    ws_ss = new WebSocket('wss://kokonk.com:2349')

    ws_ss.onopen = function (e) {
        console.log("Connection established SOC_APP");
        let arr = {
            text: 'hello world',
        };
        ws_ss.send(JSON.stringify(arr));
    };

    ws_ss.onmessage = function (event) {
        let mess = JSON.parse(event.data);
        let arr = [];
        for(let i in mess) {
            arr.push(i+'='+mess[i]);
        }
        if(event.data.includes('"com":"json"')) {
            come_send(event.data);
        } else {
            come_send(arr.join('~|'));
        }
    };

    ws_ss.onclose = function (event) {
        if (event.wasClean) {
            console.log(`Connection closed cleanly, code=${event.code} reason=${event.reason}`);
        } else {
            console.log('Connection unexpectedly closed');
        }
    };

    ws_ss.onerror = function (error) {
        console.log(`Error: ${error.message}`);
    };

    function system_send(arr = {}) {
        ws_ss.send(JSON.stringify(arr));
    }

    function come_send(mess) {
        let customUrl = "jsbridge://" + encodeURIComponent(mess);
        window.location.href = customUrl;
    }

    // txt в этом случае это com=comand~|param1=argum1~|param2=argum2
    function send_com(txt) {
        let arr = {};
        let list = txt.split('~|');
        for(let i in list) {
            let first = list[i].indexOf('=');
            if(first !== -1) {
                arr[list[i].substring(0, first)] = list[i].substring(first+1);
            }
        }
        if('com' in arr) {
            system_send(arr);
        } else {
            alert('Not finded operator [ COM ] in array...');
        }
    }
</script>