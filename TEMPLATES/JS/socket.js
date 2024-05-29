let ws = new WebSocket('ws://80.87.203.249:8080');

ws.onopen = function() {
    console.log('Соединение установлено');
};

ws.onmessage = function(event) {
    let data = JSON.parse(event.data);
    console.dir(data);
    let messageContainer = document.createElement('div');
    messageContainer.textContent = data.message;
    document.getElementById('messages').appendChild(messageContainer);
};

function send_server(txt='') {
    let message = txt;
    if(message === '') {
        document.getElementById('message').value;
    }
    ws.send(JSON.stringify({ 'message': message }));
    document.getElementById('message').value = '';
}

setTimeout(function() {
    send_server('hello mirr!!!');
}, 1000);