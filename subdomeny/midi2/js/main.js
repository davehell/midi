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
