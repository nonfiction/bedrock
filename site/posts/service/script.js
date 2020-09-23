import './style.css';
import $ from 'jquery';

$(document).ready(function(){

  $('.nf-services--filter li a').click(function(e){
    e.preventDefault();

    $('ul.nf-services--filter li a').removeClass('is-active');
    $(this).addClass('is-active');

    let service = $(this).data('service');
    
    $('.nf-case-study-tease').each(function(e) {

      let services = $(this).data('services').split(' ');

      if ( services.includes(service) ) {
        $(this).show();
      } else {
        $(this).hide();
      }

    });
  });

});
