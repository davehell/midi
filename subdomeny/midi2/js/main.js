//stazeni skladby po kliknuti na tlacitko #stahnout
$('form#stazeniSkladby button#stahnout').click(function() {
  var url = $('form#stazeniSkladby #format').val();
  if(url) window.location = url;
});
