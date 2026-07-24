/* js */
!function e(i,t,o){function a(r,s){if(!t[r]){if(!i[r]){var c="function"==typeof require&&require;if(!s&&c)return c(r,!0);if(n)return n(r,!0);var l=new Error("Cannot find module '"+r+"'");throw l.code="MODULE_NOT_FOUND",l}var m=t[r]={exports:{}};i[r][0].call(m.exports,function(e){var t=i[r][1][e];return a(t?t:e)},m,m.exports,e,i,t,o)}return t[r].exports}for(var n="function"==typeof require&&require,r=0;r<o.length;r++)a(o[r]);return a}({1:[function(e,i,t){"use strict";!function(e){e.fn.turntable=function(i){function t(i){var t,o=i.length;t="scroll"===n.axis?e(window).height():"y"===n.axis?r.height():r.width();for(var a=t/o,c=0;c<i.length;c++)s[c]={min:a*c,max:a+a*c,index:c};n.reverse===!0&&(s.reverse(),e.each(s,function(e,i){i.index=e}))}var o=function(){var e=!1;return function(i){(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(i)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(i.substr(0,4)))&&(e=!0)}(navigator.userAgent||navigator.vendor||window.opera),e},a=e("ul",this).children(),n=e.extend({},e.fn.turntable.defaults,i),r=e(this),s=[];!function(i){a.each(function(){e(this).html('<img src="'+e(this).data("imgSrc")+'">')})}(),e("li:first-child>img",r).load(function(){e(this).parent().addClass("active"),t(a)}),e(window).resize(function(){t(a)});var c=function(i,t){e.each(i,function(){t>=this.min&&t<=this.max&&(a.removeClass("active"),a.eq(this.index).addClass("active"))})};return"scroll"===n.axis?e(window).scroll(function(){var i;i="bottom"===n.scrollStart?r.height():"top"===n.scrollStart?0:r.height()/2;var t=r.offset(),o=t.top-(e(window).scrollTop()-i);c(s,o)}):o()?r.on("touchmove",function(i){i.preventDefault();var t,o=e(this).offset(),a=i.originalEvent.touches[0]||i.originalEvent.changedTouches[0];t="y"===n.axis?a.pageY-o.top:a.pageX-o.left,c(s,t)}):r.on("mousemove",function(i){var t,o=e(this).offset();t="y"===n.axis?i.pageY-o.top:i.pageX-o.left,c(s,t)})},e.fn.turntable.defaults={axis:"x",reverse:!1,scrollStart:"middle"}}(jQuery)},{}]},{},[1]);


jQuery(document).ready(function($) {

	//hide carrousell controls if there is only one
	$('#slider .carousel-inner').each(function() {
		if ($(this).children('div').length === 1) $(this).siblings('.carousel-indicators, .carousel-control-prev, .carousel-control-next, .num').hide();
	});


    $('.image-lightbox').magnificPopup({type:'image'});

        $('#hotspotImg').hotSpot({
            bindselector: 'click'
        });


    $('.videos-gallery .sections-list ul li a').on('click', function (e) {
        e.preventDefault();
        var partnerResources = $(this).attr('class');
        $(this).parent().parent().find('.active').removeClass('active');
        $(this).parent().addClass('active');

        if (partnerResources == 'all') {
            $('#videos-area').children('div.col-sm-4').fadeIn(500);
        } else {
            $('#videos-area').children('div.col-sm-4:not(.' + partnerResources + ')').fadeOut(500);
            $('#videos-area').children('div.col-sm-4.' + partnerResources).fadeIn(500);
        }
        return false;
    });

    $('.images-gallery .sections-list ul li a').on('click', function (e) {
        e.preventDefault();
        var partnerResources = $(this).attr('class');
        $('.filter li').removeClass('active');
        $(this).parent().addClass('active');

        if (partnerResources == 'all') {
            $('#images-area').children('div.col-sm-4').fadeIn(500);
        } else {
            $('#images-area').children('div.col-sm-4:not(.' + partnerResources + ')').fadeOut(500);
            $('#images-area').children('div.col-sm-4.' + partnerResources).fadeIn(500);
        }
        return false;
    });

    if($('#myTurntable').length > 0) {
        $('#myTurntable').turntable({
            axis: 'scroll',
            reverse: true,
            scrollStart: 'middle'
        });
    }

    $('.open-youtube-modal').on('click', function(e) {
        e.preventDefault();
        var title = $(this).data('title');
        var youtube_id = $(this).data('youtube-id');
        $('#video-modal .modal-title').html(title);
        $('#video-modal .embed-responsive').html(`<iframe width="560" height="315" src="https://www.youtube.com/embed/${youtube_id}" allowfullscreen></iframe>`);
        $('#video-modal').modal('show')

    });


/* ===========================================================
   * Dynamic header offset Ã¢â‚¬â€ position mobile menu overlay below
   * the fixed header. Recalculates on resize.
   * =========================================================== */
  function adjustHeaderOffset() {
    $('#content').css('padding-top', '');
    if ($(window).width() >= 1024) {
      $('#all-header-menu').css({'top': '', 'max-height': ''});
      return;
    }
    var headerHeight = $('header#masthead').outerHeight() || 0;
    $('#all-header-menu').css({
      'top': headerHeight + 'px',
      'max-height': 'calc(100vh - ' + headerHeight + 'px)'
    });
  }
  adjustHeaderOffset();
  $(window).on('resize', adjustHeaderOffset);

  $('#mobile-menu-btn').click(function(e){
    e.preventDefault();
    $(this).toggleClass('menu-open').blur();
    $('#all-header-menu').toggleClass('d-none');
  });

  /* ===========================================================
   * Mobile menu Ã¢â‚¬â€ separate chevron toggle for items with children
   * Label = navigate to link, chevron = expand/collapse children
   * =========================================================== */
  function injectMobileChevrons() {
    if ($(window).width() >= 1024) return;
    $('[id^="mega-menu-wrap-primary"] [id^="mega-menu-primary"] > li.mega-menu-item-has-children, ' +
      '[id^="mega-menu-wrap-primary"] [id^="mega-menu-primary"] > li.mega-menu-megamenu, ' +
      '.mobile-headernav-list > li.menu-item-has-children').each(function() {
      var $li = $(this);
      if ($li.find('> .wj-mobile-chevron').length) return; // already injected
      var $chevron = $('<button type="button" class="wj-mobile-chevron" aria-label="Expand"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 9L12 15L18 9" stroke="#093C71" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>');
      $li.append($chevron);
    });
  }

  // Disable Max Mega Menu hover behavior at tablet/mobile
  $(document).on('mouseenter', '[id^="mega-menu-wrap-primary"] [id^="mega-menu-primary"] > li.mega-menu-item', function() {
    if ($(window).width() < 1024) {
      var $li = $(this);
      setTimeout(function() {
        if (!$li.data('wj-clicked')) {
          $li.removeClass('mega-toggle-on');
        }
      }, 10);
    }
  });

  injectMobileChevrons();
  $(window).on('resize', injectMobileChevrons);

  // Chevron click handler Ã¢â‚¬â€ toggle expanded state, prevent label navigation
  $(document).on('click touchend', '.wj-mobile-chevron', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $li = $(this).closest('li');
    var isOpen = $li.hasClass('mega-toggle-on');
    $li.toggleClass('mega-toggle-on mobile-sub-open wj-expanded');
    $li.data('wj-clicked', !isOpen);
    $(this).toggleClass('wj-rotated');
  });

  // Stop Mega Menu plugin from toggling children when label is clicked (capture phase).
  document.addEventListener('click', function(e) {
    if (window.innerWidth >= 1024) return;
    var $target = $(e.target);
    var $link = $target.closest('[id^="mega-menu-wrap-primary"] [id^="mega-menu-primary"] > li.mega-menu-item-has-children > a.mega-menu-link, [id^="mega-menu-wrap-primary"] [id^="mega-menu-primary"] > li.mega-menu-megamenu > a.mega-menu-link');
    if (!$link.length) return;
    if ($target.closest('.wj-mobile-chevron').length) return;
    var href = $link.attr('href');
    if (href && href !== '#' && href !== '') {
      e.stopImmediatePropagation();
    } else {
      e.stopImmediatePropagation();
      e.preventDefault();
    }
  }, true);

  // Mobile header-nav: toggle sub-menus on click
  $(document).on('click', '.mobile-headernav-list > li.menu-item-has-children > a', function(e){
    if ($(window).width() < 1024) {
      e.preventDefault();
      e.stopPropagation();
      $(this).parent().toggleClass('mobile-sub-open');
    }
  });

  // Language switcher toggle on mobile
  $('#header-submenu-nav > ul > li.menu-item-has-children > a').on('click', function(e){
    if ($(window).width() < 1024) {
      e.preventDefault();
      $(this).parent().toggleClass('lang-open');
    }
  });

  // Close language dropdown when clicking outside
  $(document).on('click', function(e){
    if (!$(e.target).closest('#header-submenu-nav li.menu-item-has-children').length) {
      $('#header-submenu-nav li.menu-item-has-children').removeClass('lang-open');
    }
  });

    $('.youtube-video-modal').on('hidden.bs.modal', function (event) {
        var iframe = $(this).find('iframe');
        iframe.attr("src", iframe.attr("src"));
    })

    $('.series-slides').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      infinite: true,
      arrows: false,
      dots: true
    });
//add carrousell slide number and total
var totalItems = $('#slider .carousel-item').length;
var currentIndex = $('#slider .carousel-item.active').index() + 1;
$('#slider .num').html('' + currentIndex + ' OF ' + totalItems + '');

$('.carousel').carousel({
	interval: 6000
});

$('#slider #carousel').bind('slid.bs.carousel', function() {
	currentIndex = $('#slider .carousel-item.active').index() + 1;
	$('#slider .num').html('' + currentIndex + ' OF ' + totalItems + '');
});

  var updateSlideNum = function(event) {
    var carousel = $(event.currentTarget);
    currentIndex = carousel.find('.carousel-item.active').index() + 1;
    carousel.find('.current-slide').html(currentIndex);
  }

$('.carousel-slide-counter').bind('slid.bs.carousel', updateSlideNum);


$(function(){

    wow = new WOW(
          {
          boxClass:     'wow',      // default
          animateClass: 'animate__animated', // default
          offset:       100,          // default
          mobile:       true,       // default
          live:         true        // default
        }
    );
    wow.init();

	//var $status = $('.pagingInfo');
  // var $slickElement = $('.slick-slider');
  // $slickElement.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
  //       //currentSlide is undefined on init -- set it to 0 in this case (currentSlide is 0 based)
  //       var i = (currentSlide ? currentSlide : 0) + 1;
  //       $status.text(i + '/' + slick.slideCount);
  //   });

  
  // $('.slick-slider').slick({
  //   slidesToShow: 1,
  //   slidesToScroll: 1,
  //   autoplay: true,
  //   autoplaySpeed: 45000,
  //   infinite: true,
  // });


  // if (document.querySelectorAll('.about-kpis'))
  // {
  //   config = { attributes: true, childList: false, subtree: false };
  //   callback = function(mutationsList, observer) {
  //     // Use traditional 'for loops' for IE 11
  //     for(const mutation of mutationsList) {
  //         if (mutation.type === 'attributes') {
  //             console.log('The ' + mutation.attributeName + ' attribute was modified.');
  //         }
  //     }
  //   };

  //   const observer = new MutationObserver(callback);
  //   observer.observe(targetNode, config);

  // }



});

  
  // $('#missionCarrousel').bind('slide.bs.carousel', function(event){

  //   var carousel = $(event.currentTarget);
  //   var items = carousel.find('.carousel-indicators li');
  //   var currentSlide = event.to;
  //   var numSlides = items.length;

  //   //add prev and next
  //   carousel.find('.carousel-indicators .prev-item, .carousel-indicators .next-item').removeClass('prev-item next-item');
  //   if (currentSlide > 0)
  //   {
  //     items.eq(currentSlide-1).addClass('prev-item');
  //   }

  //   if (currentSlide < (items.length-1))
  //   {
  //     items.eq(currentSlide+1).addClass('next-item');
  //   }

  //   //figure out which items need to be visible
  //   //usually it'd be from 2 items to the left, to 2 items to the right

  //   start = currentSlide - 2;
  //   end = currentSlide + 2;

  //   if (start < 0)
  //   {
  //     end += Math.abs(start);
  //     start =0;
  //   }

  //   if (end > numSlides)
  //   {
  //     start -= Math.abs(end-numSlides);
  //     end = numSlides-1;
  //   }

  //   carousel.find('.carousel-indicators li.item-visible').removeClass('item-visible');
  //   for(var i=start; i<=end; i++)
  //   {
  //     items.eq(i).addClass('item-visible');
  //   }             

  // });



  $('a.specs-toggle').click(function(e){
    e.preventDefault();
    var $self = $(this);
    var $table = $($self.data('target'));
    var column = $self.data('column');
    if($table.hasClass(column))
    {
      //do nothing
      return true;      
    }

    $table.removeClass(['imperial', 'metric']).addClass(column);
  });


    $('#search-icon').click(function(e){
      e.preventDefault();
      $('#search-row').toggleClass('d-none');
    })


    $('#sorter-btn').click(function() {

      $.post('/sorter-results', {'industry_id': $('.industries-select').val(), 'term_id': $('.products-select').val()}, function(data){

        $('#sorter-results').html(data);
        $('sorterCarousel').carousel({
          interval: 6000
        });
        $('.carousel-slide-counter').bind('slid.bs.carousel', updateSlideNum);
      });

    });

    $('#sorter-btn').click();

    // ============================================
    // Hero Video Carousel - Auto-play & Auto-advance (ported from blueprint)
    // ============================================
    var $heroVideoCarousel = $('#heroVideoCarousel');

    if ($heroVideoCarousel.length) {
        var $videos = $heroVideoCarousel.find('.hero-video');
        var $carousel = $heroVideoCarousel.carousel({
            interval: false, // Disable Bootstrap auto-slide
            pause: false
        });

        // Play the first video on load
        var $firstVideo = $heroVideoCarousel.find('.carousel-item.active .hero-video');
        if ($firstVideo.length) {
            $firstVideo[0].play().catch(function(error) {
                console.log('Auto-play prevented:', error);
            });
        }

        // When video ends, advance to next slide
        $videos.on('ended', function() {
            var $currentItem = $(this).closest('.carousel-item');
            var $nextItem = $currentItem.next('.carousel-item');

            $videos.each(function() {
                this.pause();
                this.currentTime = 0;
            });

            if ($nextItem.length) {
                $carousel.carousel('next');
            } else {
                $carousel.carousel(0);
            }
        });

        // When slide changes, play the new video
        $carousel.on('slid.bs.carousel', function(e) {
            var $activeVideo = $(e.relatedTarget).find('.hero-video');
            if ($activeVideo.length) {
                $activeVideo[0].play().catch(function(error) {
                    console.log('Auto-play prevented:', error);
                });
            }
        });

        // Pause video when navigating manually
        $carousel.on('slide.bs.carousel', function() {
            $videos.each(function() {
                this.pause();
            });
        });
    }

    // Features Slider Ã¢â‚¬â€ infinite translateX track
    (function() {
        var $slider = $('#featuresSlider');
        if (!$slider.length) return;

        var $origTrack = $slider.find('.features-track');
        var $originals = $origTrack.find('.features-track__slide');
        var totalOriginal = $originals.length;

        // Need at least 2 slides for looping to make sense
        if (totalOriginal < 2) return;

        var $prevBtn = $slider.find('.slider-arrow.prev');
        var $nextBtn = $slider.find('.slider-arrow.next');

        var interval = $slider.data('interval');
        var autoplay = interval && interval !== 'false';
        var autoplayInterval;
        var isAnimating = false;

        function getPerView() {
            var w = window.innerWidth;
            if (w <= 767) return 1;
            if (w <= 1023) return 2;
            return 3;
        }

        function getSlideMetrics() {
            var perView = getPerView();
            var gap = perView === 1 ? 0 : 24;
            var wrapperWidth = $origTrack.parent().width();
            var slideWidth = (wrapperWidth - gap * (perView - 1)) / perView;
            return { perView: perView, gap: gap, wrapperWidth: wrapperWidth, slideWidth: slideWidth };
        }

        // Build clones: prepend `perView` slides before, append `perView` slides after
        function buildClones() {
            $origTrack.find('.features-track__slide--clone').remove();
            var perView = getPerView();
            var prependCount = perView;
            var appendCount = perView;

            // Append clones to end
            for (var i = 0; i < appendCount; i++) {
                var $clone = $originals.eq(i % totalOriginal).clone();
                $clone.addClass('features-track__slide--clone');
                $origTrack.append($clone);
            }
            // Prepend clones to start (in reverse order)
            for (var j = 0; j < prependCount; j++) {
                var idx = (totalOriginal - 1 - (j % totalOriginal));
                var $clone = $originals.eq(idx).clone();
                $clone.addClass('features-track__slide--clone');
                $origTrack.prepend($clone);
            }
        }

        // currentIndex is 0-based index into original slides
        var currentIndex = 0;

        // The track position where index 0 of originals starts = perView slides worth of offset
        function getBaseOffset() {
            var m = getSlideMetrics();
            return m.perView * (m.slideWidth + m.gap);
        }

        function applyTransform(instant) {
            var m = getSlideMetrics();
            var offset = getBaseOffset() + currentIndex * (m.slideWidth + m.gap);
            if (instant) {
                $origTrack.css('transition', 'none');
            } else {
                $origTrack.css('transition', 'transform 0.5s cubic-bezier(0.25, 0.1, 0.25, 1)');
            }
            $origTrack.css('transform', 'translateX(-' + offset + 'px)');
        }

        function goNext() {
            if (isAnimating) return;
            isAnimating = true;
            stopAutoplay();

            currentIndex++;
            applyTransform(false);

            var safetyTimeout = setTimeout(function() {
                $origTrack.off('transitionend');
                isAnimating = false;
                if (currentIndex >= totalOriginal) {
                    currentIndex = currentIndex - totalOriginal;
                    applyTransform(true);
                }
                startAutoplay();
            }, 600);

            $origTrack.one('transitionend', function() {
                clearTimeout(safetyTimeout);
                isAnimating = false;
                if (currentIndex >= totalOriginal) {
                    currentIndex = currentIndex - totalOriginal;
                    applyTransform(true);
                    $origTrack[0].offsetHeight;
                }
                startAutoplay();
            });
        }

        function goPrev() {
            if (isAnimating) return;
            isAnimating = true;
            stopAutoplay();

            currentIndex--;
            applyTransform(false);

            var safetyTimeout = setTimeout(function() {
                $origTrack.off('transitionend');
                isAnimating = false;
                if (currentIndex < 0) {
                    currentIndex = currentIndex + totalOriginal;
                    applyTransform(true);
                }
                startAutoplay();
            }, 600);

            $origTrack.one('transitionend', function() {
                clearTimeout(safetyTimeout);
                isAnimating = false;
                if (currentIndex < 0) {
                    currentIndex = currentIndex + totalOriginal;
                    applyTransform(true);
                    $origTrack[0].offsetHeight;
                }
                startAutoplay();
            });
        }

        function startAutoplay() {
            if (autoplay) {
                autoplayInterval = setInterval(function() {
                    goNext();
                }, interval);
            }
        }

        function stopAutoplay() {
            clearInterval(autoplayInterval);
        }

        // Init
        buildClones();
        applyTransform(true);
        startAutoplay();
        adjustAccentLines();

        // Web fonts (Montserrat) load async — the init call above may measure the
        // fallback font before titles wrap in Montserrat, leaving accent lines
        // full-width on wrapped titles. Recalculate once fonts are ready.
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(adjustAccentLines);
        }

        $prevBtn.on('click', function() {
            goPrev();
        });
        $nextBtn.on('click', function() {
            goNext();
        });

        // Match accent-line width to last line of wrapped titles
        function adjustAccentLines() {
            $origTrack.find('.feature-title').each(function() {
                var el = this;
                var textNode = el.childNodes[0];
                if (!textNode || textNode.nodeType !== 3) return;

                var lineHeight = parseFloat(getComputedStyle(el).lineHeight);
                var elHeight = el.getBoundingClientRect().height;

                // Single line Ã¢â‚¬â€ inline-block wrapper handles it
                if (elHeight <= lineHeight * 1.2) {
                    $(el).siblings('.accent-line').css('width', '');
                    return;
                }

                // Multi-line Ã¢â‚¬â€ measure last line via getClientRects
                var range = document.createRange();
                range.selectNode(textNode);
                var rects = range.getClientRects();

                if (rects.length > 1) {
                    var maxWidth = 0;
                    for (var r = 0; r < rects.length; r++) {
                        if (rects[r].width > maxWidth) maxWidth = rects[r].width;
                    }
                    $(el).siblings('.accent-line').css('width', Math.ceil(maxWidth) + 'px');
                }
            });
        }

        // Touch / swipe support
        var touchStartX = 0;
        var touchEndX = 0;

        $slider.on('touchstart', function(e) {
            touchStartX = e.originalEvent.touches[0].clientX;
        });
        $slider.on('touchend', function(e) {
            touchEndX = e.originalEvent.changedTouches[0].clientX;
            var diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    goNext();
                } else {
                    goPrev();
                }
            }
        });

        // Rebuild on resize
        var resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                buildClones();
                applyTransform(true);
                adjustAccentLines();
            }, 150);
        });
    })();

    // Products section â€” mobile scroll dots
    (function() {
        if ($(window).width() >= 1024) return;
        var $grid = $('.products-section .products-grid');
        var $dots = $('.products-section .products-dots');
        if (!$grid.length || !$dots.length) return;

        var $cards = $grid.find('.product-card');
        var total = $cards.length;
        if (total < 2) return;

        // Generate dots
        for (var i = 0; i < total; i++) {
            var $dot = $('<button class="dot" data-index="' + i + '"></button>');
            if (i === 0) $dot.addClass('active');
            $dots.append($dot);
        }

        // Click dot to scroll
        $dots.on('click', '.dot', function() {
            var idx = $(this).data('index');
            var card = $cards.eq(idx);
            if (card.length) {
                $grid[0].scrollTo({ left: card[0].offsetLeft - 24, behavior: 'smooth' });
            }
        });

        // Update active dot on scroll
        var scrollTimer;
        $grid.on('scroll', function() {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                var scrollLeft = $grid[0].scrollLeft;
                var closest = 0;
                var closestDist = Infinity;
                $cards.each(function(i) {
                    var dist = Math.abs(this.offsetLeft - 24 - scrollLeft);
                    if (dist < closestDist) {
                        closestDist = dist;
                        closest = i;
                    }
                });
                $dots.find('.dot').removeClass('active');
                $dots.find('.dot').eq(closest).addClass('active');
            }, 50);
        });
    })();
	}); // jquery document ready