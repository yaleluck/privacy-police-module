define([
    'jquery',
    'mage/url'
], function($, urlBuilder) {
    'use strict';
    
    return function() {
        $('#accept-privacy').on('click', function() {
            $.ajax({
                url: urlBuilder.build('privacypolicy/index/accept'),
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#privacy-modal').hide();
                    } else {
                        alert('Erro: ' + response.error);
                    }
                }
            });
        });
        
        $('#disable-modal').on('click', function() {
            $('#privacy-modal').hide();
        });
    };
});

