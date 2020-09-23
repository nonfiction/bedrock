import './style.css';
import $ from 'jquery';

import 'slick-carousel';

$(document).ready(function(){

  $('.nf-testimonials').slick({
    dots: true,
    arrows: false,
    infinite: true,
    speed: 300,
    slidesToShow: 1,
    adaptiveHeight: true
  });

});
