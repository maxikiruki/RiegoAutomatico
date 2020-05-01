$(document).ready(function() {

    var count = $(".itemRow").length;

    showproduct("#sectorName_1");

    $(document).on('click', '#checkAll', function() {
        $(".itemRow").prop("checked", this.checked);
    });
    $(document).on('click', '.itemRow', function() {
        if ($('.itemRow:checked').length == $('.itemRow').length) {
            $('#checkAll').prop('checked', true);
        } else {
            $('#checkAll').prop('checked', false);
        }
    });

    // JQUERY PARA AÑADIR UNA FILA MAS
    $(document).on('click', '#addRows', function() {
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input class="itemRow" type="checkbox"></td>';
        htmlRows += '<td><input type="text" name="sectorName[]" id="productName_' + count + '" class="form-control" autocomplete="off"><div class="suggestions" id="suggestions_' + count + '"></div></td>';
        htmlRows += '<td><input type="number" name="valve[]" min="0" pattern="^[0-9]+" id="valve' + count + '" class="form-control valve" autocomplete="off"></td>';
        htmlRows += '<td><input type="number" name="humedity[]" min="0" pattern="^[0-9]+" id="humedity_' + count + '" class="form-control humedity" autocomplete="off"></td>';
        htmlRows += '<td><input type="number" name="flowmeter[]" min="0" pattern="^[0-9]+" id="flowmeter_' + count + '" class="form-control flowmeter" autocomplete="off"></td>';
        htmlRows += '<td><input type="number" readonly min="0" pattern="^[0-9]+" name="total[]" id="total_' + count + '" class="form-control total" autocomplete="off"></td>';
        htmlRows += '</tr>';
        htmlRows += '<div id="suggestions_' + count + '</div>';
        $('#invoiceItem').append(htmlRows);
        showproduct("#productName_" + count);
    });

    // JQUERY PARA ELIMINAR UNA FILA
    $(document).on('click', '#removeRows', function() {
        $(".itemRow:checked").each(function() {
            $(this).closest('tr').remove();
        });
        $('#checkAll').prop('checked', false);
        calculateTotal();
    });
    $(document).on('change', "[id^=valve_]", function() {
        calculateTotal();
    });
    $(document).on('change', "[id^=humedity_]", function() {
        calculateTotal();
    });
    $(document).on('change', "[id^=flowmeter_]", function() {
        calculateTotal();
    });

});