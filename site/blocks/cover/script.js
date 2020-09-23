import './style.css';
import $ from 'jquery';

$(document).ready(function(){

  $('a[href$=".mp4"]').closest('.wp-block-cover').click(function(e){
    e.preventDefault();
    let v = $(this).find('a[href$=".mp4"]').attr('href');
    $('body').append(`<div class="nf-video-overlay"><div></div><video autoplay controls src="${v}"></video></div>`);
    $('.nf-video-overlay').each(function() {
      let $overlay = $(this);
      $overlay.find('> div').click(function(){
        $overlay.remove();
      });
    });
  });

});
