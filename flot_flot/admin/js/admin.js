// start a periodic post to keep users session alive
(function worker() {
  $.ajax({
    url: '/flot_flot/admin/', 
    type: "POST",
    success: function(data) {
    },
    complete: function() {
      // Schedule the next request when the current one's complete
      setTimeout(worker, 30000);
    }
  });
})();