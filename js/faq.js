// jQuery(document).ready(function($) {
//     $(".faq-question").click(function() {
//         if (faqSettings.singleOpen) {
//             // Single open mode - close others first
//             $(".faq-item").not($(this).parent()).removeClass("active");
//             $(this).parent().toggleClass("active");
//         } else {
//             // Multiple open mode - just toggle current
//             $(this).parent().toggleClass("active");
//         }
//     });
// });

// jQuery(document).ready(function($) {
//     // Get the current setting from localized data
//     var faqSettings = window.faqSettings || {};
//     var singleOpenMode = faqSettings.singleOpen || 0;
    
//     $(".faq-question").on('click', function(e) {
//         e.preventDefault();
//         var $faqItem = $(this).closest('.faq-item');
//         var isActive = $faqItem.hasClass('active');
        
//         if (singleOpenMode == 1) {
//             // Single open mode - close others first
//             if (!isActive) {
//                 $(".faq-item").removeClass("active");
//             }
//         }
        
//         // Toggle current item
//         $faqItem.toggleClass("active", !isActive);
//     });
// });



jQuery(document).ready(function($) {
    var faqSettings = window.faqSettings || {};
    var singleOpenMode = faqSettings.singleOpen || 0;
    
    $(document).on('click', '.faq-question', function(e) {
        e.preventDefault();
        var $faqItem = $(this).closest('.faq-item');
        var isActive = $faqItem.hasClass('active');
        
        if (singleOpenMode == 1) {
            // Get the closest container (works for both layouts)
            var $container = $faqItem.closest('.faq-container');
            // Close all other FAQs except the clicked one
            $container.find('.faq-item.active').not($faqItem).removeClass("active");
        }
        
        // Toggle the clicked item
        $faqItem.toggleClass("active", !isActive);
    });
});


// document.addEventListener("DOMContentLoaded", function () {
//     const faqQuestions = document.querySelectorAll(".faq-question");

//     faqQuestions.forEach(function (question) {
//         question.addEventListener("click", function () {
//             const item = this.closest(".faq-item");
//             item.classList.toggle("active");
//         });
//     });
// });