import './style.css';

import $ from 'jquery';

let total = 0;
$('.nf-case-studies__list a').each(function() {
  total += $(this).width();
});
$('.nf-case-studies__list').width(total);
