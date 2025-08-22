// Glider js
document.addEventListener('DOMContentLoaded', function () {
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
});
