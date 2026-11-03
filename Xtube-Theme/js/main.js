jQuery( document ).ready( function() {

  // Video Previews Manager
  (function videosPreviewsManager() {
    var videosPreviewsXhrs = {};
    var videosPreviewsBuffer = {};    

    jQuery( '.video-with-trailer' ).each( function( index, value ) {
      var $videoPreview = jQuery( this ).find( '.video-preview' );
      var postId = jQuery( this ).data( 'post-id' );
      var $videoDebounceBar = jQuery( this ).find( '.video-debounce-bar' );
      var $videoImg = jQuery( this ).find( '.video-img' );
      var $videoName = jQuery( this ).find( '.video-name' );
      var $videoDuration = jQuery( this ).find( '.video-duration' );

      videosPreviewsBuffer[postId] = false;      

      // On mouseenter event.
      jQuery( value ).on( 'mouseenter', function( event ) {
        if ( ! $videoImg.hasClass( 'loaded' ) ) {
          return;
        }

        $videoDebounceBar.addClass( 'video-debounce-bar--wait' );
        // $videoName.addClass( 'video-name--hidden' );
        // $videoDuration.addClass( 'video-duration--hidden' );
        videosPreviewsBuffer[postId] = true;
        setTimeout( function() {
          if ( ! videosPreviewsBuffer[postId] ) {
            return;
          }
          jQuery.ajax({
            beforeSend: function( xhr ) {
              videosPreviewsXhrs[postId] = xhr;
            },
            method: 'POST',
            url: vtt_ajax_var.url,
            dataType: 'json',
            data: {
              action: 'vtt_load_video_preview',
              nonce: vtt_ajax_var.nonce,
              post_id: postId
            }
          })
          .done( function( response ) {
            var $canvas, canvasId;
            if ( ! ( videosPreviewsBuffer[postId] && response.success && '' !== response.data ) ) {
              return;
            }
            // Add the Model Preview in the DOM directly.
            // The Model Preview wrapper is z-indexed 50.
            $videoPreview.html( response.data ).show( function() {
              // Hide the Model Image to reveal the preview.
              // Model Image is z-indexed 100.
              if ( videosPreviewsBuffer[postId] ) {
                $videoImg.addClass( 'video-img--hidden' );
              }
            });
          }); // End of Ajax call.
        }, 250 ); // End of settimeout
      }); // End of mouseenter event.

      // On mouseleave event.
      jQuery( value ).on( 'mouseleave', function( event ) {
        videosPreviewsBuffer[postId] = false;
        $videoImg.removeClass( 'video-img--hidden' );
        // $videoName.removeClass( 'video-name--hidden' );
        // $videoDuration.removeClass( 'video-duration--hidden' );
        $videoDebounceBar.removeClass( 'video-debounce-bar--wait' );

        setTimeout( function() {
          $videoPreview.html( '' );
        }, 200 );

        // Abort current postId xhr if exists.
        if ( videosPreviewsXhrs[postId] ) {
          videosPreviewsXhrs[postId].abort();
          delete( videosPreviewsXhrs[postId] );
        }        
      }); // End of mouseleave event.

    }); // End of each.
  })(); // End of Models Previews Manager IFEE.

  //Multithumbs
  var changeThumb = null;
  var stopped = false;
  jQuery('body').on('mouseenter', '.thumbs-rotation', function(e){
      var $this = jQuery(this);
      stopped = false;
      if( $this.data('thumbs') != undefined ){
          var dataThumbs = $this.data('thumbs');
          var thumbs = dataThumbs.split(',');
          var nbThumbs = thumbs.length;
          var i = 1;
          changeThumb = null;
          clearTimeout(changeThumb);
          changeThumb = function() {
              if( stopped == false ){
                  $this.find('img').attr('srcset', thumbs[i - 1]);
                  if (i <= nbThumbs ) {
                      setTimeout(changeThumb, 700);
                      if( i == nbThumbs){
                          i = 1;
                      }else{
                        i++;
                      }
                  }
              }
          };
          changeThumb();
      }
  }).on('mouseleave', '.thumbs-rotation', function(e){
      stopped = true;
      changeThumb = null;
      var highestTimeoutId = setTimeout(";");
      for (var i = 0 ; i < highestTimeoutId ; i++) {
          clearTimeout(i);
      }
      var $blockImg = jQuery(this).find('img');
      var defaultThumb = $blockImg.attr('src');
      $blockImg.attr('srcset', defaultThumb);
  });

  // Open search form
  // jQuery( '.header-search-mobile' ).click( function() {
  //   if ( jQuery( window ).width() <= 767.98 ) {
  //     jQuery( '.header-search' ).slideToggle( 200 );
  //   }
  //   if ( jQuery( window ).width() >= 768 ) {
  //     jQuery( '.header-search' ).animate({ width: 'toggle' }, 200 );
  //     jQuery( '.search-field' ).focus();
  //   }
  // });
  // Move search form
  // if ( jQuery( window ).width() <= 767.98 ) {
  //   jQuery( '.header-search' ).insertAfter( '.header-nav' );
  // }
  // if ( jQuery( window ).width() >= 768 ) {
  //   jQuery( '.header-search' ).appendTo( '.logo-search' );
  // }
  // jQuery( window ).resize( function() {
  //   if ( jQuery( window ).width() <= 767.98 ) {
  //     jQuery( '.header-search' ).insertAfter( '.header-nav' );
  //   }
  //   if ( jQuery( window ).width() >= 768 ) {
  //     jQuery( '.header-search' ).appendTo( '.logo-search' );
  //   }
  // });

  // jQuery('.header-search').insertAfter('.logo-search');

  // if ( jQuery( window ).width() <= 767.98 ) {
  //   jQuery(window).on("load", function () {
  //     jQuery('.hamburger').removeClass('is-active');
  //     jQuery('.navbar-collapse').removeClass('show');
  //   });    
  // }

  if ( jQuery( window ).width() <= 767.98 ) {
      jQuery('.hamburger').removeClass('is-active');
      jQuery('.navbar-collapse').removeClass('show');
  }
  // if ( jQuery( window ).width() >= 768 ) {
  //     jQuery('.hamburger').addClass('is-active');
  //     jQuery('.navbar-collapse').addClass('show');
  // }

  jQuery( window ).resize( function() {
    if ( jQuery( window ).width() <= 767.98 ) {
      jQuery('.hamburger').removeClass('is-active');
      jQuery('.navbar-collapse').removeClass('show');
    }
    // if ( jQuery( window ).width() >= 768 ) {
    //   jQuery('.hamburger').addClass('is-active');
    //   jQuery('.navbar-collapse').addClass('show');
    // }
  });

  jQuery('.hamburger').on('click', function(e)  {
    jQuery('.navbar').removeClass('d-none');
  });

  jQuery('.video-cat-tag').readmore({
    speed: 75,
    collapsedHeight: 60,
    moreLink: '<span class="more-tags"><i class="fa fa-plus"></i></span>',
    lessLink: '<span class="more-tags"><i class="fa fa-minus"></i></span>',
  });

  // jQuery('ul.navbar-nav').readmore({
  //   speed: 75,
  //   collapsedHeight: 96,
  //   moreLink: '<span class="more-tags-menu btn btn-grey"><i class="fa fa-plus"></i></span>',
  //   lessLink: '<span class="more-tags-menu btn btn-grey"><i class="fa fa-minus"></i></span>',
  // });

  if ( jQuery( window ).width() <= 991.98 ) {
    jQuery('ul.navbar-nav li.tag-menu:gt(-45)').hide().last().show();
  }  
  if ( 992 > jQuery( window ).width() <= 1299.98 ) {
    jQuery('ul.navbar-nav li.tag-menu:gt(-30)').hide().last().show();
  }
  if ( 1299.98 > jQuery( window ).width() <= 1599.98 ) {
    jQuery('ul.navbar-nav li.tag-menu:gt(-15)').hide().last().show();
  }
  if ( jQuery( window ).width() >= 1600 ) {
    jQuery('ul.navbar-nav li.tag-menu').show();
  }
  jQuery( window ).resize( function() {
    if ( jQuery( window ).width() <= 991.98 ) {
      jQuery('ul.navbar-nav li.tag-menu:gt(-45)').hide().last().show();
    }
    if ( 992 > jQuery( window ).width() <= 1299.98 ) {
      jQuery('ul.navbar-nav li.tag-menu:gt(-30)').hide().last().show();
    }
    if ( 1299.98 > jQuery( window ).width() <= 1599.98 ) {
      jQuery('ul.navbar-nav li.tag-menu:gt(-15)').hide().last().show();
    }
    if ( jQuery( window ).width() >= 1600 ) {
      jQuery('ul.navbar-nav li.tag-menu').show();
    }
  });

  
  
  // console.log(lastLi);

  // jQuery('.more-tags-menu').insertAfter('.see-all-tags');

  // Load videojs
  if(jQuery('#vtt-video').length > 0 && !vtt_ajax_var.ctpl_installed){
    var playerOptions = {
      controlBar: {
        children: [
              'playToggle',           
              'progressControl', 
              'durationDisplay',
              'volumePanel',
              'qualitySelector',
              'fullscreenToggle',
        ],
      },
    };
    videojs('vtt-video', playerOptions);
  }

  // Close inplayer advertising
  jQuery('body').on('click', '.happy-inside-player .close-text', function(e) {
    jQuery(this).parent('.happy-inside-player').hide();
  });

  /** Tabs **/
  jQuery('.tab-link').on('click', function(e)  {
    var tabId = jQuery(this).data('tab-id');
    // Show/Hide Tabs
    jQuery('#' + tabId).show().siblings().hide();
    // Change/remove current tab to active
    jQuery(this).addClass('active').siblings().removeClass('active');
    e.preventDefault();
  });

  // Replace all SVG images with inline SVG
  jQuery( 'img[src$=".svg"]' ).each( function() {
    var $img = jQuery( this );
    var imgURL = $img.attr( 'src' );
    var attributes = $img.prop( 'attributes' );
    var id = $img.parent( 'a' ).attr( 'id' );

    jQuery.get( imgURL, function( data ) {

      // Get the SVG tag, ignore the rest
      var $svg = jQuery( data ).find( 'svg' );

      // Remove any invalid XML tags
      $svg = $svg.removeAttr( 'xmlns:a' );

      // Loop through IMG attributes and apply on SVG
      jQuery.each( attributes, function() {
        $svg.attr( this.name, this.value );
      });

      // Replace IMG with SVG
      $img.replaceWith( $svg );

      if ( 'wps-logo-link' === id ) {
        jQuery( '#' + id ).addClass( 'show-logo' );
      }
    }, 'xml' );
  });

  /** IIFE Set Post views with ajax request for cache compatibility */
  (function(){
    var is_post = jQuery('body.single-post').length > 0;
    if( !is_post ) return;
    var post_id = jQuery('article.post').attr('id').replace('post-', '');
    jQuery.ajax({
      type: 'post',
      url: vtt_ajax_var.url,
      dataType: 'json',
      data: {
        action: 'post-views',
        nonce: vtt_ajax_var.nonce,
        post_id: post_id
      }          
    })
    .done(function(doneData){
      // console.log(doneData);
    })
    .fail(function(errorData){
      console.error(errorData);
    })
    .always(function(alwaysData){
      //get post views & rating data
      jQuery.ajax({
        type: 'post',
        url: vtt_ajax_var.url,
        dataType: 'json',
        data: {
          action: 'get-post-data',
          nonce: vtt_ajax_var.nonce,
          post_id: post_id
        }
      })
      .done(function(doneData){        
        if(doneData.views) {
          jQuery("#video-views span.views-number").text(doneData.views);
        }
        if(doneData.likes) {
            jQuery(".likes_count").text(doneData.likes);
        }
        if(doneData.dislikes) {
            jQuery(".dislikes_count").text(doneData.dislikes);
        }
        if(doneData.rating) {
            jQuery(".percentage").text(doneData.rating);
            jQuery(".rating-bar-meter").css('width', doneData.rating);
        }
      })
      .fail(function(errorData){
        console.error(errorData);
      })
      .always(function(){
        // always stuff
      })
    });
  })();

  /** Post like **/
  jQuery(".post-like a").on('click', function(e){
      e.preventDefault();

      var heart = jQuery(this);
      var post_id = heart.data("post_id");
      var post_like = heart.data("post_like");

      jQuery.ajax({
          type: "post",
          url: vtt_ajax_var.url,
          dataType   : "json",
          data: "action=post-like&nonce=" + vtt_ajax_var.nonce + "&post_like=" + post_like + "&post_id=" + post_id,
          success    : function(data, textStatus, jqXHR){
              if(data.alreadyrate !== true) {
                  jQuery(".rating-bar-meter").removeClass("not-rated-yet");
                  /*jQuery(".rating").text(Math.floor(data.pourcentage) + "%");
                  jQuery(".rating").show();*/

                  jQuery(".rating-result .percentage").text(Math.floor(data.percentage) + "%");
                  jQuery(".rating-result .percentage").show();

                  jQuery(".likes_count").text(data.likes);
                  jQuery(".dislikes_count").text(data.dislikes);

                  jQuery(".post-like").text(data.button);

                  if( data.nbrates > 0 ){
                      jQuery(".rating-bar-meter").animate({
                          width: data.progressbar + "%",
                      }, "fast", function() {
                      // Animation complete.
                     });
                  }
              }
          }
      });
      return false;
  });  

  // Video share toggle
  jQuery('#show-sharing-buttons').click(function(e){
    e.preventDefault();
    jQuery('.video-share-box').slideToggle('fast');
    if (jQuery('.video-share-box').css('display') == 'block') {
      jQuery(this).addClass('active');
    }else{
      jQuery(this).removeClass('active');
    }
  });

  // Copy video share url to clipboard
  jQuery("#clickme").click(function() {
    var textToCopy = jQuery("#copyme").val();
    jQuery(this)
      .parent()
      .children("#temptext")
      .val(textToCopy);
    jQuery(this)
      .parent()
      .children("#temptext")
      .select();
    document.execCommand("copy");
    jQuery(this)       
      .replaceWith('<span id="clickme"><i class="fa fa-check"></i> Copied</span>');
  });
});

// Menu mobile
var forEach = function( t, o, r ) {
  if ( '[object Object]' === Object.prototype.toString.call( t ) ) {
    for ( var c in t ) {
      Object.prototype.hasOwnProperty.call( t, c ) && o.call( r, t[c], c, t );
    }
  } else {
    for ( var e = 0, l = t.length; l > e; e++ ) {
      o.call( r, t[e], e, t );
    }
  }
};
var hamburgers = document.querySelectorAll( '.hamburger' );
if ( hamburgers.length > 0 ) {
  forEach( hamburgers, function( hamburger ) {
    hamburger.addEventListener( 'click', function() {
      this.classList.toggle( 'is-active' );
    }, false );
  });
}

/** LOGIN / REGISTER POPUP */
function vtt_open_login_dialog(href){
    
  jQuery('#wpst-user-modal .modal-dialog').removeClass('registration-complete');

  var modal_dialog = jQuery('#wpst-user-modal .modal-dialog');
  modal_dialog.attr('data-active-tab', '');

  switch(href){

      case '#wpst-register':
          modal_dialog.attr('data-active-tab', '#wpst-register');
          break;

      case '#wpst-login':
      default:
          modal_dialog.attr('data-active-tab', '#wpst-login');
          break;
  }

  jQuery('#wpst-user-modal').modal('show');
}	

function vtt_close_login_dialog(){

  jQuery('#wpst-user-modal').modal('hide');
}	

jQuery(function($){

  "use strict";
  /***************************
  **  LOGIN / REGISTER DIALOG
  ***************************/

  // Open login/register modal
  $('[href="#wpst-login"], [href="#wpst-register"]').click(function(e){

      e.preventDefault();

      vtt_open_login_dialog( $(this).attr('href') );

  });

  // Switch forms login/register
  $('.modal-footer a, a[href="#wpst-reset-password"]').click(function(e){
      e.preventDefault();
      $('#wpst-user-modal .modal-dialog').attr('data-active-tab', $(this).attr('href'));
  });
  
  // Post login form
  $('#vtt_login_form').on('submit', function(e){

      e.preventDefault();

      var button = $(this).find('button');
          button.button('loading'); 

      $.post(vtt_ajax_var.url, $('#vtt_login_form').serialize(), function(data){

          var obj = $.parseJSON(data);

          $('.wpst-login .wpst-errors').html(obj.message);
          
          if(obj.error == false){
              $('#wpst-user-modal .modal-dialog').addClass('loading');
              window.location.reload(true);
              button.hide();
          }

          button.button('reset');
      });

  });


  // Post register form
  $('#vtt_registration_form').on('submit', function(e){

      e.preventDefault();

      var button = $(this).find('button');
          button.button('loading');

      $.post(vtt_ajax_var.url, $('#vtt_registration_form').serialize(), function(data){
          
          var obj = $.parseJSON(data);

          $('.wpst-register .wpst-errors').html(obj.message);
          
          if(obj.error == false){
              $('#wpst-user-modal .modal-dialog').addClass('registration-complete');
              // window.location.reload(true);
              button.hide();
          }

          button.button('reset');
          
      });

  });


  // Reset Password
  $('#vtt_reset_password_form').on('submit', function(e){

      e.preventDefault();

      var button = $(this).find('button');
          button.button('loading');

      $.post(vtt_ajax_var.url, $('#vtt_reset_password_form').serialize(), function(data){

          var obj = $.parseJSON(data);

          $('.wpst-reset-password .wpst-errors').html(obj.message);
          
          // if(obj.error == false){
              // $('#wpst-user-modal .modal-dialog').addClass('loading');
              // $('#wpst-user-modal').modal('hide');
          // }

          button.button('reset');
      });

  });

  if(window.location.hash == '#login'){
      vtt_open_login_dialog('#wpst-login');
  }		

});

/********************/
/** Bootstrap modal */
/********************/
/*!
 * Bootstrap v3.3.7 (http://getbootstrap.com)
 * Copyright 2011-2017 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

/*!
 * Generated using the Bootstrap Customizer (http://getbootstrap.com/docs/3.3/customize/?id=2df80e69d208c504c2a17d0146de73bb)
 * Config saved to config.json and https://gist.github.com/2df80e69d208c504c2a17d0146de73bb
 */
if (typeof jQuery === 'undefined') {
  throw new Error('Bootstrap\'s JavaScript requires jQuery')
}
+function ($) {
  'use strict';
  var version = $.fn.jquery.split(' ')[0].split('.')
  if ((version[0] < 2 && version[1] < 9) || (version[0] == 1 && version[1] == 9 && version[2] < 1) || (version[0] > 3)) {
    throw new Error('Bootstrap\'s JavaScript requires jQuery version 1.9.1 or higher, but lower than version 4')
  }
}(jQuery);

/* ========================================================================
 * Bootstrap: button.js v3.3.7
 * http://getbootstrap.com/javascript/#buttons
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // BUTTON PUBLIC CLASS DEFINITION
  // ==============================

  var Button = function (element, options) {
    this.$element  = $(element)
    this.options   = $.extend({}, Button.DEFAULTS, options)
    this.isLoading = false
  }

  Button.VERSION  = '3.3.7'

  Button.DEFAULTS = {
    loadingText: 'loading...'
  }

  Button.prototype.setState = function (state) {
    var d    = 'disabled'
    var $el  = this.$element
    var val  = $el.is('input') ? 'val' : 'html'
    var data = $el.data()

    state += 'Text'

    if (data.resetText == null) $el.data('resetText', $el[val]())

    // push to event loop to allow forms to submit
    setTimeout($.proxy(function () {
      $el[val](data[state] == null ? this.options[state] : data[state])

      if (state == 'loadingText') {
        this.isLoading = true
        $el.addClass(d).attr(d, d).prop(d, true)
      } else if (this.isLoading) {
        this.isLoading = false
        $el.removeClass(d).removeAttr(d).prop(d, false)
      }
    }, this), 0)
  }

  Button.prototype.toggle = function () {
    var changed = true
    var $parent = this.$element.closest('[data-toggle="buttons"]')

    if ($parent.length) {
      var $input = this.$element.find('input')
      if ($input.prop('type') == 'radio') {
        if ($input.prop('checked')) changed = false
        $parent.find('.active').removeClass('active')
        this.$element.addClass('active')
      } else if ($input.prop('type') == 'checkbox') {
        if (($input.prop('checked')) !== this.$element.hasClass('active')) changed = false
        this.$element.toggleClass('active')
      }
      $input.prop('checked', this.$element.hasClass('active'))
      if (changed) $input.trigger('change')
    } else {
      this.$element.attr('aria-pressed', !this.$element.hasClass('active'))
      this.$element.toggleClass('active')
    }
  }


  // BUTTON PLUGIN DEFINITION
  // ========================

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.button')
      var options = typeof option == 'object' && option

      if (!data) $this.data('bs.button', (data = new Button(this, options)))

      if (option == 'toggle') data.toggle()
      else if (option) data.setState(option)
    })
  }

  var old = $.fn.button

  $.fn.button             = Plugin
  $.fn.button.Constructor = Button


  // BUTTON NO CONFLICT
  // ==================

  $.fn.button.noConflict = function () {
    $.fn.button = old
    return this
  }


  // BUTTON DATA-API
  // ===============

  $(document)
    .on('click.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      var $btn = $(e.target).closest('.btn')
      Plugin.call($btn, 'toggle')
      if (!($(e.target).is('input[type="radio"], input[type="checkbox"]'))) {
        // Prevent double click on radios, and the double selections (so cancellation) on checkboxes
        e.preventDefault()
        // The target component still receive the focus
        if ($btn.is('input,button')) $btn.trigger('focus')
        else $btn.find('input:visible,button:visible').first().trigger('focus')
      }
    })
    .on('focus.bs.button.data-api blur.bs.button.data-api', '[data-toggle^="button"]', function (e) {
      $(e.target).closest('.btn').toggleClass('focus', /^focus(in)?$/.test(e.type))
    })

}(jQuery);

/* ========================================================================
 * Bootstrap: modal.js v3.3.7
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2016 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options             = options
    this.$body               = $(document.body)
    this.$element            = $(element)
    this.$dialog             = this.$element.find('.modal-dialog')
    this.$backdrop           = null
    this.isShown             = null
    this.originalBodyPad     = null
    this.scrollbarWidth      = 0
    this.ignoreBackdropClick = false

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.bs.modal')
        }, this))
    }
  }

  Modal.VERSION  = '3.3.7'

  Modal.TRANSITION_DURATION = 300
  Modal.BACKDROP_TRANSITION_DURATION = 150

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this.isShown ? this.hide() : this.show(_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.checkScrollbar()
    this.setScrollbar()
    this.$body.addClass('modal-open')

    this.escape()
    this.resize()

    this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

    this.$dialog.on('mousedown.dismiss.bs.modal', function () {
      that.$element.one('mouseup.dismiss.bs.modal', function (e) {
        if ($(e.target).is(that.$element)) that.ignoreBackdropClick = true
      })
    })

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(that.$body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      that.adjustDialog()

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element.addClass('in')

      that.enforceFocus()

      var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

      transition ?
        that.$dialog // wait for modal to slide in
          .one('bsTransitionEnd', function () {
            that.$element.trigger('focus').trigger(e)
          })
          .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
        that.$element.trigger('focus').trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.bs.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()
    this.resize()

    $(document).off('focusin.bs.modal')

    this.$element
      .removeClass('in')
      .off('click.dismiss.bs.modal')
      .off('mouseup.dismiss.bs.modal')

    this.$dialog.off('mousedown.dismiss.bs.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one('bsTransitionEnd', $.proxy(this.hideModal, this))
        .emulateTransitionEnd(Modal.TRANSITION_DURATION) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.bs.modal') // guard against infinite focus loop
      .on('focusin.bs.modal', $.proxy(function (e) {
        if (document !== e.target &&
            this.$element[0] !== e.target &&
            !this.$element.has(e.target).length) {
          this.$element.trigger('focus')
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keydown.dismiss.bs.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keydown.dismiss.bs.modal')
    }
  }

  Modal.prototype.resize = function () {
    if (this.isShown) {
      $(window).on('resize.bs.modal', $.proxy(this.handleUpdate, this))
    } else {
      $(window).off('resize.bs.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.$body.removeClass('modal-open')
      that.resetAdjustments()
      that.resetScrollbar()
      that.$element.trigger('hidden.bs.modal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var that = this
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $(document.createElement('div'))
        .addClass('modal-backdrop ' + animate)
        .appendTo(this.$body)

      this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
        if (this.ignoreBackdropClick) {
          this.ignoreBackdropClick = false
          return
        }
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus()
          : this.hide()
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one('bsTransitionEnd', callback)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      var callbackRemove = function () {
        that.removeBackdrop()
        callback && callback()
      }
      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one('bsTransitionEnd', callbackRemove)
          .emulateTransitionEnd(Modal.BACKDROP_TRANSITION_DURATION) :
        callbackRemove()

    } else if (callback) {
      callback()
    }
  }

  // these following methods are used to handle overflowing modals

  Modal.prototype.handleUpdate = function () {
    this.adjustDialog()
  }

  Modal.prototype.adjustDialog = function () {
    var modalIsOverflowing = this.$element[0].scrollHeight > document.documentElement.clientHeight

    this.$element.css({
      paddingLeft:  !this.bodyIsOverflowing && modalIsOverflowing ? this.scrollbarWidth : '',
      paddingRight: this.bodyIsOverflowing && !modalIsOverflowing ? this.scrollbarWidth : ''
    })
  }

  Modal.prototype.resetAdjustments = function () {
    this.$element.css({
      paddingLeft: '',
      paddingRight: ''
    })
  }

  Modal.prototype.checkScrollbar = function () {
    var fullWindowWidth = window.innerWidth
    if (!fullWindowWidth) { // workaround for missing window.innerWidth in IE8
      var documentElementRect = document.documentElement.getBoundingClientRect()
      fullWindowWidth = documentElementRect.right - Math.abs(documentElementRect.left)
    }
    this.bodyIsOverflowing = document.body.clientWidth < fullWindowWidth
    this.scrollbarWidth = this.measureScrollbar()
  }

  Modal.prototype.setScrollbar = function () {
    var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
    this.originalBodyPad = document.body.style.paddingRight || ''
    if (this.bodyIsOverflowing) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
  }

  Modal.prototype.resetScrollbar = function () {
    this.$body.css('padding-right', this.originalBodyPad)
  }

  Modal.prototype.measureScrollbar = function () { // thx walsh
    var scrollDiv = document.createElement('div')
    scrollDiv.className = 'modal-scrollbar-measure'
    this.$body.append(scrollDiv)
    var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
    this.$body[0].removeChild(scrollDiv)
    return scrollbarWidth
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  function Plugin(option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('bs.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  var old = $.fn.modal

  $.fn.modal             = Plugin
  $.fn.modal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


  // MODAL DATA-API
  // ==============

  $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
    var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target.one('show.bs.modal', function (showEvent) {
      if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
      $target.one('hidden.bs.modal', function () {
        $this.is(':visible') && $this.trigger('focus')
      })
    })
    Plugin.call($target, option, this)
  })

}(jQuery);