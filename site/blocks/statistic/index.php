<?php
use nf\BlockType;
use function nf\import;
use function nf\css;

BlockType::register_block_type( __DIR__ . '/block.json', [

  // Server-side block rendering
  'render' => function( &$context ) { 

    // Return a twig template for Timber to render
    return (<<<END

      <a class="nf-statistic {{ className }}" style="{{ style }}"{%if link %}href="{{ link }}"{%endif%}> 
        <div class="nf-statistic__number">
          <span class="number">{{ number }}</span>
          <span class="extra">{{ extra }}</span>
        </div>
        <div class="nf-statistic__label">{{ label }}</div>
      </a>

    END);
  },

]);




register_block_pattern( 'nf/results-statistics', [
  'title'       => 'Results',
  'description' => 'Two statistics under a heading',
  'content'     => (<<<END

    <!-- wp:heading {"className":"is-style-decorative"} -->
    <h2 class="is-style-decorative">Results</h2>
    <!-- /wp:heading -->

    <!-- wp:columns -->
    <div class="wp-block-columns"><!-- wp:column -->
    <div class="wp-block-column"><!-- wp:nf/statistic {"number":"123,456,789","extra":"+","label":"Media impressions"} /--></div>
    <!-- /wp:column -->

    <!-- wp:column -->
    <div class="wp-block-column"><!-- wp:nf/statistic {"number":"50","label":"Markets"} /--></div>
    <!-- /wp:column --></div>
    <!-- /wp:columns -->

  END),

]);
