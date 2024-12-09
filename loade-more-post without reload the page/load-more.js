jQuery(document).ready(function($) {
  // Define the initial page number
  let page = 1;
console.log($('.loadmore'))
  // Attach click event to the load more button
  $('body').on('click', '.loadmore', function() {
      let data = {
          'action': 'load_posts_by_ajax',
          'page': page,
          'security': ajax_object.security, // Correct nonce key
      };

      // Perform the AJAX request
      $.post(ajax_object.ajax_url, data, function(response) {
          if ($.trim(response) !== '') {
              $('.blog-posts').append(response); // Append new posts
              page++; // Increment page number
          } else {
              $('.loadmore').hide(); // Hide button if no more posts
          }
      });
  });
});
