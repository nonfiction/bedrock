import './style.css';

import $ from 'jquery';

$('.nf-team-member__link').on('click', function(e) {
  e.preventDefault();
  $(this).closest('.nf-team-member').addClass('nf-team-member--active');
});

$('.nf-team-member__overlay').on('click', function(e) {
  $(this).closest('.nf-team-member').removeClass('nf-team-member--active');
}); 
