$(document).ready(function(){

    function get_selected()
    {
        var chkArray = [];
        $(".check:checked").each(function() {
            chkArray.push($(this).val());
        });
        var selected;
        selected = chkArray.join('-');

        return selected;
    }

    $('#download_all').click(function () {
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'download.php',
            data:{
                download_all: ''
            },
            success:function(res){
                $("#responseDisplay").html(res);
                waitingDialog.hide();
                $("#downloadModal").modal({
                    show:true
                });
            }
        });
    });

    $('#download_selected').click(function () {
        var selected = get_selected();
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'download.php',
            data:{
                albums:selected,
                download_selected: ''
            },
            success:function(res){
                $("#responseDisplay").html(res);
                waitingDialog.hide();
                $("#downloadModal").modal({
                    show:true
                });
            }
        });

    });

    $('.download_album').click(function () {
        var album_id = $(this).data('id');
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'download.php',
            data:{
                album_id:album_id,
                download_single:''
            },
            success:function(res){
                $("#responseDisplay").html(res);
                waitingDialog.hide();
                $("#downloadModal").modal({
                    show:true
                });
            }
        });

    });

    $('#move_all').click(function () {
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'move.php',
            data:{
                move_all:''
            },
            success:function(res){
                $("#responseDisplay1").html(res);
                waitingDialog.hide();
                $("#moveModal").modal({
                    show:true
                });
            }
        });
    });

    $('#move_selected').click(function () {
        var selected = get_selected();
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'move.php',
            data:{
                albums:selected,
                move_selected:''
            },
            success:function(res){
                $("#responseDisplay1").html(res);
                waitingDialog.hide();
                $("#moveModal").modal({
                    show:true
                });
            }
        });

    });


    $('.move_album').click(function () {
        var album_id = $(this).data('id');
        waitingDialog.show('Please Wait...',{
            progressType: 'info'
        });

        $.ajax({
            type:'POST',
            url: 'move.php',
            data:{
                album_id:album_id,
                move_single:''
            },
            success:function(res){
                $("#responseDisplay1").html(res);
                waitingDialog.hide();
                $("#moveModal").modal({
                    show:true
                });
            }
        });

    });



});