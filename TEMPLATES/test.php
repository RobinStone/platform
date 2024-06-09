<div class="wrapper">
    <button onclick="tester()">GET</button>
    <br>
    <button onclick="tester2()">SET</button>
    <br>
    <button onclick="tester3()">DELETE</button>
</div>

<script>
    function tester() {
        // PROFIL.get_list({cash: 0, about: '---', city: 'not set', blaster: 'oleg', phone: ''}, function(mess) {
        //     console.dir(mess);
        // }, true);
        //
        // PROFIL.get('cash', function(mess) {
        //     say(mess, 2);
        // })
    }

    function tester2() {
        // PROFIL.set_list({cash: 0, about: '---', city: 'Вилейка', blaster: 'oleg', phone: ''}, function(mess) {
        //     console.dir(mess);
        // }, true);

        // PROFIL.set('cash', '5000', function(mess) {
        //     console.log('------------');
        //     console.dir(mess);
        // });
    }

    function tester3() {
        PROFIL.delete(['ooo', 'cash', 'SEO', 'blaster']);
    }
</script>
