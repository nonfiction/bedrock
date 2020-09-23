import './style.css';


import $ from 'jquery';

$(document).ready(function(){

  $('.nf-news-release-list__button').each(function(e){
    let $button = $(this);
    $button.find('a').click(function(e) {
      e.preventDefault();
      $('.nf-news-release-tease.is-hidden').removeClass('is-hidden');
      $button.hide();
    });
  });

});
