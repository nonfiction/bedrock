// This is contains JavaScript for admin pages
// WP hook: admin_enqueue_scripts (in footer)

import './css/base.css';
import "../src/tweaks/admin-tweaks.js"; 


if (typeof window.disable_comments == "undefined") {
  window.disable_comments = { disabled_blocks: '' }
}
