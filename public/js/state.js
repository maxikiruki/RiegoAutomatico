$(document).ready(function() {
    // $(document).on('click', 'switchMan', function() {
    //     $sector = this.id;
    //     //window.location.replace('/update/'$sector'/manual');
    //     console.log($sector);
    //     console.log(this);
    // });
    $("[name*='switchMan']").on('click', function() {
        $state = this.id;
        // alert('/update/' + $sector);
        window.location.replace('/update_manual/' + $state);
    })

    $("[name*='switchPro']").on('click', function() {
        $state = this.id;
        window.location.replace('/update_programmed/' + $state);
    })

});