// require([
//     'jquery',
//     'glider' // assuming you added glider as a library in requirejs-config.js
// ], function ($, Glider) {
//     'use strict';
// Glider js


    document.addEventListener('DOMContentLoaded', function() {
        new Glider(document.querySelector('.best-seller'), {
    slidesToShow: 4,
    slidesToScroll: 1,
    draggable: true,
    arrows: false,
    dots: '.bs-dots',
    responsive: [
        {
            breakpoint: 992, 
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 576, 
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        }
    ]
});

    // var $linkNode = $('.am-popup-trigger');

    // $linkNode.on('click', function (e) {
    //   e.preventDefault();
    //   let product_id = $(this).data("product-id");
    //   let product_name = $(this).parent().parent().find('h4').text();
    //   console.log(product_id, product_name);
    //   window.dispatchEvent(new CustomEvent('open-am-hideprice-popup', {
    //     detail: {
    //       productId: product_id,
    //       productName: product_name
    //     }
    //   }));
    // });

        new Glider(document.querySelector('.on-sales'), {
            slidesToShow: 4,
            slidesToScroll: 1,
            draggable: true,
            arrows: false,
            dots: '.onsle-dots',
            responsive: [
                {
                    breakpoint: 992,
                    settings: { slidesToShow: 4, slidesToScroll: 1 }
                },
                {
                    breakpoint: 576,
                    settings: { slidesToShow: 2, slidesToScroll: 1 }
                }
            ]
        });

        // Hide extra dots
        // var count = $(".on-sales .glider-track > .product-slide").length / 4;
        // $('.on-sales .glider-dots [data-index]').each(function () {
        //     const index = parseInt($(this).attr('data-index'), 10);
        //     if (index >= count) {
        //         $(this).hide();
        //     }
        // });

        // Get the number of slides divided by 4
        var slides = document.querySelectorAll(".on-sales .glider-track > .product-slide");
        var count = slides.length / 4;

        // Loop through each dot element
        var dots = document.querySelectorAll('.on-sales .glider-dots [data-index]');
        dots.forEach(function(dot) {
            var index = parseInt(dot.getAttribute('data-index'), 10);
            if (index >= count) {
                dot.style.display = 'none';
            }
        });
    });
// });
