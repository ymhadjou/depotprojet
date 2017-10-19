$(document).ready(function () {

    $("#cours").keyup(function(){

        //alert(this.value);
        $('#imgloader').css('display','inline');
        $('#msgerr').css('display','none');
       $.ajax({
            url: "ajaxCours",
            method: "GET",
            data: {"param": this.value},
            dataType: "json",
            success: function(data)
            {
                $('#imgloader').css('display','none');
                if(data.length == 0)
                    $('#msgerr').css('display','inline');
                else
                    $("#cours").autocomplete({
                        source: data
                    });
            }
        });
    });

    $("#enseignant").keyup(function(){

        //alert(this.value);
        $('#imgloader').css('display','inline');
        $('#msgerr').css('display','none');
       $.ajax({
            url: "ajaxEnseignant",
            method: "GET",
            data: {"param": this.value},
            dataType: "json",
            success: function(data)
            {
                $('#imgloader').css('display','none');
                if(data.length == 0)
                    $('#msgerr').css('display','inline');
                else
                $("#enseignant").autocomplete({
                    source: data
                });
            }
        });
    });

    $("#statut").keyup(function(){

        //alert(this.value);
        $('#imgloader').css('display','inline');
        $('#msgerr').css('display','none');
       $.ajax({
            url: "ajaxStatut",
            method: "GET",
            data: {"param": this.value},
            dataType: "json",
            success: function(data)
            {
                $('#imgloader').css('display','none');
                if(data.length == 0)
                    $('#msgerr').css('display','inline');
                else
                $("#statut").autocomplete({
                    source: data
                });
            }
        });
    });


});