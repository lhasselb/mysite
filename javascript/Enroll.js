//jQuery.noConflict();
(function($){
    $(document).ready(function() {
        var bindGroups = function() {
            // First copy values
            $("#Form_EnrollForm_AccountHolderFirstName").val($("#Form_EnrollForm_FirstName").val());
            $("#Form_EnrollForm_AccountHolderLastName").val($("#Form_EnrollForm_LastName").val());
            $("#Form_EnrollForm_AccountHolderStreet").val($("#Form_EnrollForm_Street").val());
            $("#Form_EnrollForm_AccountHolderStreetNumber").val($("#Form_EnrollForm_StreetNumber").val());
            $("#Form_EnrollForm_AccountHolderZip").val($("#Form_EnrollForm_Zip").val());
            $("#Form_EnrollForm_AccountHolderCity").val($("#Form_EnrollForm_City").val());
            // Then bind fields
            $("#Form_EnrollForm_FirstName").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderFirstName").val($(this).val());
            });
            $("#Form_EnrollForm_LastName").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderLastName").val($(this).val());
            });
            $("#Form_EnrollForm_Street").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderStreet").val($(this).val());
            });
            $("#Form_EnrollForm_StreetNumber").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderStreetNumber").val($(this).val());
            });
            $("#Form_EnrollForm_Zip").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderZip").val($(this).val());
            });
            $("#Form_EnrollForm_City").bind("change keyup", function() {
                $("#Form_EnrollForm_AccountHolderCity").val($(this).val());
            });
        };
        var unbindGroups = function() {
            $("#Form_EnrollForm_FirstName").unbind("change keyup");
            $("#Form_EnrollForm_LastName").unbind("change keyup");
            $("#Form_EnrollForm_Street").unbind("change keyup");
            $("#Form_EnrollForm_StreetNumber").unbind("change keyup");
            $("#Form_EnrollForm_Zip").unbind("change keyup");
            $("#Form_EnrollForm_City").unbind("change keyup");
        };
        if ($("input[name='EqualAddress']:checked").length > 0) {
            bindGroups();
            // Hide fields on init
            $("div[id^='AccountHolder']").hide();
        }
        $("input[name='EqualAddress']").change(function() {
            if ($("input[name='EqualAddress']:checked").length > 0) {
                bindGroups();
                $('div[id^="AccountHolder"]').hide();
            } else {
                unbindGroups();
                $('div[id^="AccountHolder"]').show();
            }
        });

        /*Front-end validation using jquery-validate */
        $("#Form_EnrollForm").validate({
            ignore: ".date",
            rules: {
                Iban: {required: true, iban: true},
                Bic: {required: true, bic: true},
            }
        });

    });

}(jQuery));


(function($) {
    $(document).ready(function() {

        $('.date input').datetimepicker({
             //format: "DD.MM.YYYY",
            //debug: true,
            locale: "de",
            tooltips: {
                today: 'Heute auswählen',
                clear: 'Auswahl löschen',
                close: 'Kalender schließen',
                selectMonth: 'Monat auswählen',
                prevMonth: 'Vorheriger Monat',
                nextMonth: 'Nächster Monat',
                selectYear: 'Jahr auswählen',
                prevYear: 'Vorheriges Jahr',
                nextYear: 'Nächstes Jahr',
                selectDecade: 'Jahrzent auswählen',
                prevDecade: 'Vorheriges Jahrzent',
                nextDecade: 'Nächstes Jahrzent',
                prevCentury: 'Vorheriges Jahrhundert',
                nextCentury: 'Nächstes Jahrhundert'
            }
        });
    });
}(jQuery));




