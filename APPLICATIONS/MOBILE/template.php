<section class="mobile">
    <div class="flex gap-20">
        <select oninput="set_select(this)" id="com">
            <option>test</option>
            <option>JSONstring</option>
            <option>off-line</option>
        </select>
        <textarea id="field_t" placeholder="param1=argum1~|param2=argum2">text=bla-bla</textarea>
        <button onclick="sended()" class="btn-just">SEND</button>

        <button onclick="send_sms_test()">SEND SMS test</button>
    </div>
</section>

<script>
    function sended() {
        switch($('#com').val()) {
            case 'test':
                let link_str = 'com='+$('#com').val();
                send_com(link_str+'~|'+$('.mobile textarea').val());
                say('sended');
                break;
            case 'JSONstring':
                send_com($('.mobile textarea').val(), true);
                say('Sended JSON');
                break;
        }
    }

    function set_select(obj) {
        switch($(obj).val()) {
            case 'test':
                $('#field_t').val('text=bla-bla');
                break;
            case 'JSONstring':
                $('#field_t').val('{"list_places":"~~Красноярск, микрорайон Зелёная Роща, улица Тельмана, 43","count_uploading_files":{"DATA":"2024-03-27","COUNT":"2","ALL":"2"},"test":{"param1":"value 1","param2":"value 2","insert":{"NEW_KEY":"2342432"}}}');
                break;

        }
    }

    function send_sms_test() {
        buffer_app = 'MOBILE';
        SENDER_APP('send_sms_test', {}, function(mess) {
            mess_executer(mess, function(mess) {
                console.dir(mess);
            })
        })
    }
</script>