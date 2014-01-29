//stazeni skladby po kliknuti na tlacitko #stahnout
$('form#stazeniSkladby button#stahnout').click(function() {
  var url = $('form#stazeniSkladby #format').val();
  if(url) window.location = url;
});

//stazeni ukazky skladby po kliknuti na tlacitko #stahnoutDemo
$('button#stahnoutDemo').click(function() {
  var url = $('#formatDema').val();
  if(url) window.location = url;
});


var obdobiOd = $('#frm-obdobiForm-zacatek').datepicker({format: 'dd.mm.yyyy'}).on('changeDate', function(ev) {
  obdobiOd.hide();
}).data('datepicker');

var obdobiDo = $('#frm-obdobiForm-konec').datepicker({format: 'dd.mm.yyyy'}).on('changeDate', function(ev) {
  obdobiDo.hide();
}).data('datepicker');

