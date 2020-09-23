<?php
namespace nf;

class Menu extends \Timber\Menu {

  public function __construct( ...$args ) {
    parent::__construct( ...$args );
    static::clean_menu_items( $this->items );
  }

  // Recursive function for tweaking menu classnames
  private static function clean_menu_items( $items ) {
    foreach( $items as $item ) {

      $classes = [ 'menu-' . $item->slug ];
      if ( $item->current ) $classes[] = 'current';
      if ( $item->current_item_parent ) $classes[] = 'parent';
      if ( $item->current_item_ancestor ) $classes[] = 'ancestor';
      if ( $item->current or $item->current_item_parent or $item->current_item_ancestor ) {
        $classes[] = 'open';
      }
      $item->classes = $classes;
      $item->class = implode( ' ', $classes );

      if ( $item->children ) {
        static::clean_menu_items( $item->children );
      }
    }
  }

  public function foo() {
    return "foobar!";
  }

}
