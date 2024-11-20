jQuery.fn.changeContent = function( fn ) {
  return this.each(function(){

      var self = this;
      
      var oldVal = $(self).html();
      $(self).data(
          'watch_timer_content',
          setInterval(function(){
              if ($(self).html() !== oldVal) {
                  //alert(oldVal);
                  oldVal = $(self).html();
                  fn.call(self);
              }
          }, 300)
      );

  });

  return self;
};

jQuery.fn.changeHeight = function( fn ) {
  return this.each(function(){
      var self = this;
      
      var oldVal = $(self).height();
      $(self).data(
          'watch_timer_height',
          setInterval(function(){
              var tval=$(self).height();
              if (tval !== oldVal) {
                  oldVal = tval;
                  fn.call(self);
              }
          }, 300)
      );

  });

  return self;
};
 
jQuery.fn.unChangeContent = function() {
  return this.each(function(){
      clearInterval( $(this).data('watch_timer_content') );
  });
};

jQuery.fn.changeAttr = function( at, fn ) {
  return this.each(function(){

      var self = this;
      
      var oldVal = $(self).attr(at);
      $(self).data(
          'watch_timer_attr',
          setInterval(function(){
              if ($(self).attr(at) !== oldVal) {
                  //alert($(self).attr(at));
                  oldVal = $(self).attr(at);
                  fn.call(self);
              }
          }, 300)
      );

  });

  return self;
};
 
jQuery.fn.unChangeAttr = function() {
  return this.each(function(){
      clearInterval( $(this).data('watch_timer_attr') );
  });
};