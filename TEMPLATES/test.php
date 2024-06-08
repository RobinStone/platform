<div class="wrapper">
    <button onclick="tester()">CLICK</button>
</div>

<script>
    function tester() {
        PROFIL.get_list({cash: 0, about: '---', city: 'not set', blaster: 'oleg', phone: ''}, function(mess) {
            console.dir(mess);
        }, true);

        PROFIL.get('cash', function(mess) {
            say(mess, 2);
        })
    }
</script>
