// const router = require('./router.js');

$(document).ready(() => {
    $('#table_sortie thead tr').clone(true).appendTo('#table_sortie thead'); 
    $('#table_sortie thead tr:eq(1) th').each(function(i) {
        var title = $(this).text();

        if (['Nom', 'Etat', 'Organisateur'].includes(title)) {
            $(this).html('<input type="text" placeholder="rechercher" class="form-control">');
        } else {
            $(this).html('');
        }

        $('input', this).on('keyup change', function() {
            if (table.column(i).search() !== this.value) {
                table.column(i)
                    .search(this.value)
                    .draw();
            }
        });
    });

    // Datatable
    var table = $('#table_sortie').DataTable({
        "dom": 'rtp',
        "pageLength": 15,
        "orderCellsTop": true,
        "fixedHeader": true,
        "oLanguage": {
            "oPaginate": {
                "sNext": "Suivant",
                "sPrevious": "Précédent"
            }
        },
    });

});

function clearDateDebut() {
    $('#filtre_home_dateDebutSearch').val(null);
}

function clearDateFin() {
    $('#filtre_home_dateFinSearch').val(null);
}

function actionSortie(url, idSortie, method) {
    $.ajax({
        type: method,
        url: url,
        data: {
            'id': idSortie
        },
        success: function(data) {
            // console.log('success');
            window.location.reload();
        },
        error: function(err) {
            console.log('erreur', err);
        } 
    });
}

function annulerSortie(url, idSortie) {
    var conf = confirm('Voulez-vous vraiment annuler cette sortie')
    if (conf) {
        $.ajax({
            type: 'PUT',
            url: url,
            data: {
                'id': idSortie
            },
            success: function(data) {
                window.location.reload();
            },
            error: function(err) {
                console.log('erreur', err);
            } 
        });   
    }
}