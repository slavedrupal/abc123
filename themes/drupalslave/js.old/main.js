jQuery(document).ready(function($) {
    "use strict";
    $('[data-toggle="tooltip"]').tooltip();
    $('#go-to-top').click(function() {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
    $(window).scroll(function() {
        if ($(document).scrollTop() > 50) {
            $('.navbar-light').addClass('affix');
            $('#go-to-top').fadeIn();
        } else {
            $('.navbar-light').removeClass('affix');
            $('#go-to-top').fadeOut();
        }
    });

    function scaleCaptcha(elementWidth) {
        var reCaptchaWidth = 304;
        var containerWidth = $('.dds-log-security').width();
        if (reCaptchaWidth > containerWidth) {
            var captchaScale = containerWidth / reCaptchaWidth;
            $('.g-recaptcha').css({
                'transform': 'scale(' + captchaScale + ')'
            });
        }
    }
    scaleCaptcha();
    $(window).resize(scaleCaptcha());
    var $uploadCrop,
        tempFilename,
        rawImg,
        imageId;

    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.upload-pic').addClass('ready');
                $('#cropImagePop').modal('show');
                $('#cropImagePop').css('display', 'block');
                rawImg = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            swal("Sorry - you're browser doesn't support the FileReader API");
        }
    }
    $uploadCrop = $('#upload-profile-pic').croppie({
        viewport: {
            width: 200,
            height: 267,
        },
        enforceBoundary: false,
        enableExif: true
    });
    $('#cropImagePop').on('shown.bs.modal', function() {
        $uploadCrop.croppie('bind', {
            url: rawImg
        }).then(function() {
            console.log('jQuery bind complete');
        });
    });
    $('.item-img').on('change', function() {
        imageId = $(this).data('id');
        tempFilename = $(this).val();
        $('#cancelCropBtn').data('id', imageId);
        readFile(this);
    });
    $('#cropImageBtn').on('click', function(ev) {
        $uploadCrop.croppie('result', {
            type: 'base64',
            format: 'jpeg',
            size: {
                width: 200,
                height: 267
            }
        }).then(function(resp) {
            $('#item-img-output').attr('src', resp);
            $('#cropImagePop').modal('hide');
        });
    });
});