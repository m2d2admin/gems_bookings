<?php
    define("GEMS_PLUGIN_DIR", plugin_dir_path(__FILE__));

    $eventkey = get_query_var( 'event', 'abc123XYZ456' );
    //$eventkey = '64xcbb86c14fb2';
?>

<script>

    function validateEmail(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( email );
    }

    function calculateInsurancePrice(adults_count, children_count, children_under_3_count, nights, ins_price, ins_price_type, ins_price_per_participant, total_booking_price) {
        //console.log(adults_count);
        //console.log(children_count);
        //console.log(children_under_3_count);
        //console.log(ins_price);
        //console.log(ins_price_type);
        //console.log(ins_price_per_participant);
        //console.log(total_booking_price);
        //console.log('-----------------');
 
        var this_ins_price = 0,
            travellers = 0;

        if(ins_price_type == 1) {

            if(!isNaN(adults_count)){
                travellers += parseInt(adults_count);
            }
            if(!isNaN(children_count)){
                travellers += parseInt(children_count);
            }
            if(!isNaN(children_under_3_count)){
                travellers += parseInt(children_under_3_count);
            }
            var per_traveller_price = (nights * ins_price);
            if (ins_price_per_participant == 1 ) {
                this_ins_price =  parseFloat(per_traveller_price * travellers);
            } else {
                this_ins_price =  ins_price;
            }   
        }

        if(ins_price_type == 2){  // percentage based

            this_ins_price = (total_booking_price * ins_price) / 100;

        }

        return this_ins_price;
    }

    /* ------------- */

    jQuery(document).ready(function($) {

        // Check if the email is in a valid format
        $(document).on('blur', '.email', function(e) {
            var email = $(this).val(),
                emailErrorDiv = $(this).closest('.email-field').find('.email-error');

			if (!validateEmail(email)) {
				emailErrorDiv.html('Ongeldig e-mail formaat');
			} else {
				emailErrorDiv.html('');
			}
        });

        // Function to match emails
        $(document).on('blur', '.confirm-email', function(e) {
            var confirm_email = $(this).val(),
                emailErrorDiv = $(this).closest('.email-field').find('.email-error'),
                email = $(this).closest('.row').find('.email').val();

			if (!validateEmail(confirm_email)) {
				emailErrorDiv.html('Ongeldig e-mail formaat');
			} else if (email !== confirm_email) {
                emailErrorDiv.html('E-mails komen niet overeen');
			} else {
				emailErrorDiv.html('');
			}
        });

        // Update progress bar
        function updateProgressBar() {
            var required_fields = $('#booking_form input[required]');
            var total_fields = required_fields.length,
                completed_fields = 0,
                progress = 0;
                // alert(required_fields.length)

            required_fields.each(function() { 
                if ($(this).val() !== '' && $(this).val() !== '0') {
                    completed_fields++;
                }
            });

            // progress = (completed_fields / total_fields) * 100; 
            // progress = Math.round(progress);
            // $('#progress-bar').css('width', progress + '%').html(progress + '%');
            // // $('#progress-bar').css('width', 11 + '%').html(11 + '%');
        }

        // Update summaries stepss
        function updateSummaryStep1() {
            $('#summary_adults_count').html( $('#adults_count').val() );
            $('#summary_children_count').html( $('#children_count').val());
            $('#summary_children_under_3_count').html( $('#children_under_3_count').val() );
            $('#travellers_amount').val( parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) );
            // if($('#travellers_amount').val( parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) ) > 0){	
            //     updateProgressBar();
            // }

        }

        // // change input date format to NL
        // $("#gl_dateofbirth").on("change", function() {
        //     this.setAttribute(
        //         "data-date",
        //         moment(this.value, "YYYY-MM-DD")
        //         .format( this.getAttribute("data-date-format") )
        //     )
        // }).trigger("change")
        // $('#gl_dateofbirth').on('input', function(e) {
        //     let input = e.target.value.replace(/\D/g, ''); // Only keep digits
        //     if (input.length >= 2) input = input.slice(0, 2) + '-' + input.slice(2);
        //     if (input.length >= 5) input = input.slice(0, 5) + '-' + input.slice(5, 9);
        //     e.target.value = input;
        // });

        function updateSummaryStep2() {
            var options = { year: 'numeric', month: 'short', day: 'numeric' },
                birthdate_visitor = new Date( $('#gl_dateofbirth').val() ),
                birthdate_stayathome = new Date( $('#sah_dateofbirth').val() ),
                title_visitor = $('#gl_title').select2('data'),
                title_stayathome = $('#sah_title').select2('data'),
                country_visitor = $('#gl_country').select2('data'),
                country_stayathome = $('#sah_country').select2('data'),
                runners_count = $('#form_section2 input.traveller_is_runner:checked').length;
                country_visitor_nationality = $('#gl_nationality').select2('data'),
                country_stayathome = $('#sah_country').select2('data');

            $('#booking_visitor_title_div').html( title_visitor[0].text );  
            $('#booking_visitor_name_div').html( $('#gl_first_name').val() + ' ' + $('#gl_middle_name').val() + ' ' + $('#gl_last_name').val() );
            $('#booking_visitor_address_div').html( $('#gl_street').val() + ' ' + $('#gl_house_number').val() + ', ' + $("#gl_postal_code").val()  + ', ' + $('#gl_residence').val() + ' | ' + $('#gl_mobile').val() + ' | ' + $('#gl_email').val());
            $('#booking_visitor_birthdate_div').html( birthdate_visitor.toLocaleDateString("nl-NL", options)  + ' | ' + country_visitor[0].text + ' | ' + country_visitor_nationality[0].text);

            $('#booking_stayathome_title_div').html( title_stayathome[0].text );  
            $('#booking_stayathome_name_div').html( $('#sah_first_name').val() + ' ' + $('#sah_middle_name').val() + ' ' + $('#sah_last_name').val() );
            // $('#booking_stayathome_address_div').html( $('#sah_street').val() + ' ' + $('#sah_house_number').val() + ', ' + $('#sah_residence').val() );
            $('#booking_stayathome_address_div').html( $('#sah_mobile').val() + ' | ' + $('#sah_email').val() );
            $('#booking_stayathome_birthdate_div').html( birthdate_stayathome.toLocaleDateString("nl-NL", options) + ' | ' + country_stayathome[0].text );

            $( '#form_section3 input.bibs_count' ).data('max-qty', runners_count).prop('max', runners_count);

            /* The above code appears to be a comment block in PHP. It starts with `/*` and ends with
             which is used to add multi-line comments in PHP. Inside the comment block, there
            is a commented out line `// updateProgressBar();` which is a single-line comment in PHP.
            This line is likely a call to a function `updateProgressBar()` which is currently
            commented out and not being executed. The  */
            updateProgressBar();
        }

        function formatPrice(price) {
            if (isNaN(price) || price === null) {
                return price;
            }
            return price.toLocaleString('nl-NL', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).replace('.', ',');
        }

        function updateSummaryStep3() {
            $( '#summary_bibs_div' ).html('');
            $( '#form_section3 input.bibs_count' ).each(function() {
                var bibs_id = $(this).data('bib-id'),
                    bibs_name = $(this).data('bibs_name'),
                    bibs_count = $(this).val(),
                    bibs_price = $(this).data('price');
                if (bibs_count > 0) {
                    $('#summary_bibs_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>${bibs_name}</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>${bibs_count}</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>&euro; ${formatPrice(bibs_price)}</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>&euro; ${formatPrice(bibs_price*bibs_count)}</p>
                        </div>
                    </div>`);
                }
            });
        }

        function formatDate(date) {
            // If the input is a string, convert it to a Date object
            if (typeof date === 'string') {
                date = new Date(date);
            }

            // Check if the date is valid
            if (isNaN(date)) {
                console.error('Invalid date format');
                return null;
            }

            var options = { year: 'numeric', month: 'short', day: 'numeric' };
            
            // Return the date formatted in Dutch style
            return date.toLocaleDateString("nl-NL", options);
        }

        function updateSummaryStep6() {
            $( '#summary_extra_div, #summary_nonextra_div' ).html('');

            $( '#form_section6 input.extra_count' ).each(function() {
                var extra_name = $(this).data('extras_name'),
                    extra_count = $(this).val(),
                    extra_price = parseInt(extra_count) * parseFloat($(this).data('price'));

                if (extra_count > 0) {
                    $('#summary_extra_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${extra_name}</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${extra_count}</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>&euro; ${formatPrice(extra_price)}</p>
                        </div>
                    </div>`);
                }

            });

            $( '#form_section6 input.nonextra_count' ).each(function() {
                var extra_name = $(this).data('extras_name'),
                    extra_count = $(this).val(),
                    extra_price = parseInt(extra_count) * parseFloat($(this).data('price'));
                if (extra_count > 0) {
                    $('#summary_nonextra_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${extra_name}</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${extra_count}</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>&euro; ${formatPrice(extra_price)}</p>
                        </div>
                    </div>`);
                }
                
            });

            if( $( '#summary_extra_div' ).html() === "" ) {
                $('.hotel-extras').hide();
                $('#summary_extra_div').html('<p>Geen extra\'s geselecteerd</p>');
            }
            if( $( '#summary_nonextra_div' ).html() === "" ) {
                // $('.non-hotel-extras').hide();
                $('#summary_nonextra_div').html('<p>Geen Extra\'s buiten het hotel geselecteerd</p>');
            }

        }

        function updateSummaryStep8() {
            $( '#summary_insurance_div' ).html('');

            var total_insurance_price = 0;

            $( '#form_section8 input.insurance-options' ).each(function() {
    
                var $price_label = $(this).closest('.type-verzekering-body').find('.insurance_option'),
                    ins_name = $(this).data('name'),  
                    ins_price = $(this).data('price'),
                    ins_price_type = $(this).data('price_type'),
                    ins_price_per_participant = $(this).data('price_per_participant'),
                    total_booking_price = 0,
                    this_ins_price = 0,
                    adults_count = $("#adults_count").val(),
                    children_count = $("#children_count").val(),
                    children_under_3_count = $("#children_under_3_count").val(),
                    booking_start_date = $("#booking_start_date").val(),
                    booking_end_date = $("#booking_end_date").val();
                var insPrice = parseFloat($(this).closest('.type-verzekering-body').find('.insurance_option').html());

                let start_date = new Date(booking_start_date),
                    end_date = new Date(booking_end_date);

                // Calculating the time difference of two dates
                let date_diff_time = end_date.getTime() - start_date.getTime();
                
                // Calculating the no. of days between two dates
                var nights = Math.round(date_diff_time / (1000 * 3600 * 24));

                var total_booking_price = 
                    parseFloat($("#total_bibs_price").val()) +
                    parseFloat($("#total_extra_price").val()) +
                    parseFloat($("#total_nonextra_price").val()) +
                    parseFloat($("#total_room_price").val()) +
                    parseFloat($("#total_flight_departure_price").val()) +
                    parseFloat($("#total_flight_arrival_price").val());

                this_ins_price = calculateInsurancePrice(adults_count, children_count, children_under_3_count, nights, ins_price, ins_price_type, ins_price_per_participant, total_booking_price);

                if ($(this).is(":checked")) {
                    // $price_label.html('&euro; ' + parseFloat(this_ins_price).toFixed(2));
                    $price_label.css('display', 'inline-block');

                    // Update summary
                    $('#summary_insurance_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${ins_name}</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>&euro; ${ins_price}</p>
                        </div>
                    </div>`);

                    total_insurance_price += this_ins_price;

                } else {
                    $price_label.css('display', 'none');
                }

            });
            $('#total_insurance_price').val(parseFloat(total_insurance_price).toFixed(2));
            $('#total_insurance').html('&euro; ' + parseFloat(total_insurance_price).toFixed(2).replace('.', ','));
            // getTotals();
            updateProgressBar();
        }

        $(document).on('blur', '#form_section2 input, #form_section2 select', function(e) {
            updateSummaryStep2();
        });

        $(document).on('click', '.hotel-labels input', function(e) {
            var hotel_id = $(this).val(),
                hotel_name = $(this).data('hotel_name'),
                hotel_rating = $(this).data('rating'),
                hotel_photo = $(this).data('photo'),
                hotel_max_persons_per_room = $(this).data('max_persons_per_room'),
                hotel_price_from = $(this).data('price_from');
            $('#hotel_id').val(hotel_id);
            $('#summary_hotel_name').html(hotel_name);      
        });

        $(document).on('click', '.insurance-options', function(e) {

            // updateSummaryStep8();
        });

        function goNextStep(step, toggleVal){
            // get data-source id current step
            var dataSource = step.data('source');
            // get data-toggle to control the current step
            var dataToggle = step.data('toggle');
            // get data-target id of next step
            var dataTarget = step.data('target');
            var headeId = $(dataTarget).attr('aria-labelledby');

            // add collapse to data-toggle to show the next step
            $(`#${headeId} .btn-link`).attr('data-toggle', toggleVal);
            if(dataTarget == '#form_section2'){
                $('#form_section1 .btn-link').attr('data-toggle', toggleVal);
            }else{
                $(`${dataSource} .btn-link`).attr('data-toggle', toggleVal);
            }
        }

        // // reset hotel rooms count to 0 when hotel is changed
        // $(document).on('click', '.hotel-room-radio', function(e) {
        //     var checkedValue = $('input[type="radio"][name="hotel_room_id"]:checked').val();
        //     $('input[type="radio"][name="hotel_room_id"]').each(function() {
        //         if ($(this).val() != checkedValue) {
        //             $(this).closest('.booking-card').find('input[type="number"]').val(0);
        //         }else{
        //             //get name, prce and input number value
        //             var hotelRoomName = $(this).closest('.booking-card').find('.room-type-name').html();
        //             var hotelRoomPrice = $(this).closest('.booking-card').find('.room-type-price').html() + ' per hotelkamer'; // price/room
        //             var hotelRoomCount = $(this).closest('.booking-card').find('input[type="number"]').val();
        //             // set name, price and input number value to summary
        //             $('#summary_room_type_name').html(hotelRoomName);
        //             $('#summary_room_price').html(hotelRoomPrice);
        //             $('#summary_room_count').html(hotelRoomCount);
        //         }
        //     });
        // });

        // get hotel room type
        $(document).on('click', '.hotel-btn-form-step', function(){
            $("#summary_hotel_nights").html(calculateDaysBetweenDates($('#booking_end_date').val(), $('#booking_start_date').val()));
            $('#hotel-room-details').html('');
            $('.rooms_count').each(function(){
                var selectedRooms = "";
                if($(this).val() > 0){
                    selectedRooms += `<div class="row form-fields-rows">
                        <div class="col-md-5 col-lg-5 col-xl-5 strtbewijz-col4">
                            <p>Type hotelkamer: ${$(this).data('room_name')}</p>
                        </div>
                         <div class="col-md-3 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>Prijs per nacht: &euro; ${formatPrice($(this).data('price'))}</p>
                        </div>
                        <div class="col-md-2 col-lg-2 col-xl-4 strtbewijz-col4">
                            <p>Antal Kamers: ${$(this).val()}</p>
                        </div>
                        <div class="col-md-2 col-lg-2 col-xl-4 strtbewijz-col4">
                            <p>Prijs: &euro; ${formatPrice((parseFloat($(this).data('price'))*parseInt($(this).val())))}</p>
                        </div>`;

                    $('#hotel-room-details').append(selectedRooms);
                }
            })
        })

        // reset flight select flight-arrival & flight-departure option to null when flightplan_list is changed
        $(document).on('click', 'input[name="flightplan_list"]', function(e) {
            var checkedValue = $('input[type="radio"][name="flightplan_list"]:checked').val();
            $('input[type="radio"][name="flightplan_list"]').each(function() {
                if ($(this).val() != checkedValue) {
                    // set select option to null
                    $('.flight-arrival').val(null).trigger('change');
                    $('.flight-departure').val(null).trigger('change');
                }
            });
        });

        // rm flight price when own transport is selected
        $(document).on('click', 'input[name="flightplan_id_OLD"]', function(e) {
            if($('input[id="own-transport"]').is(':checked')){
                // append content to summary_flight_div div
                $('#flight-holder').css('display', 'none');
                $('#summary_flight_div').html('<p>Eigen vervoer</p>');
                var checkedValue = $('input[type="radio"][name="flightplan_list"]:checked').val();
                $('input[type="radio"][name="flightplan_list"]').each(function() {
                    if ($(this).val() != checkedValue) {
                        // set select option to null
                        $('.flight-arrival').val(null).trigger('change');
                        $('.flight-departure').val(null).trigger('change');
                    }
                });
            }else{
                $('#flight-holder').css('display', 'block');
                $('#summary_flight_div').html('');
                $(document).on('click', 'input[name="flightplan_list"]', function(e) {
                    var checkedValue = $('input[type="radio"][name="flightplan_list"]:checked').val();
                    var flightName = $(this).closest(".vervoer-radio-btn-label").find('#go_flight_details .col-flight-depart-details .departure-flight-airport').html();
                    var returnflightName = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-depart-details .departure-flight-airport').html();

                    // get departure fligth details from #departure_flight_details
                    var goDepartureFlight = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-depart-details .departure-flight-airport').html();
                    var goDate = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-depart-details .departure-flight-date').html();
                    var goTime = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-depart-details .departure-flight-time').html();
                    var goArrivalAirport = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-arrival-details .departure-flight-airport').html();
                    var goArrivaldate = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-arrival-details .arrival-flight-date').html();
                    var goArrivaltime = $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .col-flight-arrival-details .arrival-flight-time').html();
                    // get select value from select .flight-departure
                    $(this).closest('.vervoer-radio-btn-label').find('#go_flight_details .flight-departure').on('change', function() {
                        var departureFlightClasse = $(this).children('option:selected').text() + ' per persoon';
                        $("#summary_go_travel_classe").html(departureFlightClasse);
                    });
                    
                    // return flight details
                    var returnDepartureFlight = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-depart-details .departure-flight-airport').html();
                    var returnDate = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-depart-details .departure-flight-date').html();
                    var returnTime = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-depart-details .departure-flight-time').html();
                    var returnAirport = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-arrival-details .departure-flight-airport').html();
                    var returnArrivaldate = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-arrival-details .arrival-flight-date').html();
                    var returnArrivaltime = $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .col-flight-arrival-details .arrival-flight-time').html();
                    // // get select value from select .flight-departure
                    // // (this).children('option:selected').data('price')
                    // $(this).closest('.vervoer-radio-btn-label').find('#return_flight_details .flight-arrival').on('change', function() {
                    //     var returnFlightClasse = $(this).children('option:selected').text() + ' per persoon';
                    //     $("#summary_return_travel_classe").html(returnFlightClasse);
                    // });

                    
                    $(document).on('change', '.departure-select', function(){
                        var seatPrice = $(this).children('option:selected').data('price');
                        var classeName = $(this).children('option:selected').val();
                        var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        var seatClass = "";
                        if(classeName.toLowerCase() == 'eco'){
                            seatClass = 'Economy Class';
                        }else if(classeName.toLowerCase() == 'bus'){
                            seatClass = 'Business Class';
                        }else if(classeName.toLowerCase() == 'con'){
                            seatClass = 'Comfort  Class';
                        }
                        
                        $("#summary_return_travel_classe").html(seatClass);
                        $("#summary_flight_seats").html(travelers);
                        $("#summary_flight_price").html('&euro; ' + formatPrice(parseFloat(seatPrice).toFixed(2)));
                        $("#summary_flight_total_price").html('&euro; ' + formatPrice(parseFloat(seatPrice*travelers).toFixed(2)));
                    });
                    
                    // append go flight details
                    $("#summary_go_flight_name").html(goDepartureFlight);
                    $("#summary_go_departure").html(`${goDate} - ${goTime}`);
                    $("#summary_go_arrival").html(`${goArrivaldate} - ${goArrivaltime}`);

                    // append return flight details
                    $("#summary_return_flight_name").html(returnDepartureFlight);
                    $("#summary_return_departure").html(`${returnDate} - ${returnTime}`);
                    $("#summary_return_arrival").html(`${returnArrivaldate} - ${returnArrivaltime}`);

                });
            }
        });

        function filterInputsByEventDate(eventDate) {
            // Select all inputs with the class 'bibs_count'
            const inputs = document.querySelectorAll('input.bibs_count');
            
            // Filter inputs based on the provided event date and return them as an array
            return Array.from(inputs).filter(input => input.getAttribute('data-event-date') === eventDate).map(input => parseInt(input.value));
        }
        function removeDuplicateArrays(arr) {
            // Create a new array by filtering out duplicates
            return arr.filter((item, index, self) => 
                index === self.findIndex((t) => 
                    JSON.stringify(t) === JSON.stringify(item)
                )
            );
        }
         
        // $('#form_section3 input.bibs_count').each(function() {
        //    const filteredInputs = filterInputsByEventDate($(this).data('event-date'));
        //    console.log('filteredInputs', filteredInputs);
        // });

        function validateHotelRooms(numTravelers, numRooms) {
            // Calculate the minimum and maximum number of rooms
            const minRooms = Math.ceil(numTravelers / 2); // Round up to nearest integer
            const maxRooms = numTravelers;
            
            // Check if the number of rooms is within the valid range
            if (numRooms >= minRooms && numRooms <= maxRooms) {
                return { valid: true, message: `U kunt tussen de ${minRooms} en ${maxRooms} hotelkamers boeken` };
            } else {
                return { valid: false, message: `U kunt tussen de ${minRooms} en ${maxRooms} hotelkamers boeken` };
            }
        }

        var allotmentCheckResponse = [];
        var hotelRoomsData = [];
        var bibsAllotmentData = [];
        // function checkAllotment(productCategory){
        //     allotmentCheckResponse = [];
        //     $.ajax({
        //         url: '<?php echo $data->api_endpoint; ?>/booking/check-allotment/',
        //         type: 'GET',
        //         headers: {
        //             'e-key': '<?php echo $eventkey;?>',
        //             'merchant-key': '<?php echo $data->merchant_key; ?>'
        //         },
        //         data: {
        //             bibs: JSON.stringify(bibsAllotmentData),
        //             rooms: JSON.stringify(hotelRoomsData),
        //             flight_allotment_seats: JSON.stringify(allotmentFlightData),
        //             productCategory: productCategory
        //         },
        //         success: function(response) {
        //             localStorage.setItem('allotmentCheckStatus', response.success);
        //             localStorage.setItem('allotmentCheckMsg', response.message);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error fetching countries:', error);
        //             allotmentCheckResponse[0].status = false;
        //             allotmentCheckResponse[0].message = 'Er is een fout opgetreden bij het controleren van de beschikbaarheid, neem contact op met Loopreizen.be via het contactformulier of +31 10 12345678';
        //         }
        //     });
        // }

        // check bibs allotment
        // $(document).on('click', '#bibs_form_section', function(){
        //     bibsAllotmentData = [];
        //     var parentDiv = $(this).closest('.card-body');
        //     var bibsForm = parentDiv.find('#bibs_form');
        //     var bibsInput = bibsForm.find('input.bibs_count');
        //     bibsInput.each(function() {
        //         if($(this).val() > 0){
        //             var bibCountValue = $(this).val();
        //             var bibId = $(this).data('bib-id');
        //             bibsAllotmentData.push({
        //                 bib_id: bibId,
        //                 bib_count: bibCountValue
        //             });
        //         }
        //     });
        //     checkAllotment(1);
        // })

        // check hotel room allotment
        $(document).on('click', '#hotel_room_form_section', function(){
            // hotelRoomsData = [];
            // var parentDiv = $(this).closest('.card-body');
            // var hotelForm = parentDiv.find('#hotel_form');
            // var hotelRoomInput = hotelForm.find('input.rooms_count');
            // hotelRoomInput.each(function() {
            //     if($(this).val() > 0){
            //         var roomCountValue = $(this).val();
            //         var roomId = $(this).attr('name');
            //         var hotelId = $(this).attr('hotel-id');
            //         hotelRoomsData.push({
            //             room_id: roomId,
            //             room_count: roomCountValue,
            //             hotel_id: hotelId
            //         });
            //     }
            // });
            // checkAllotment(2);
        })

        // // check flight seats allotment
        //  $(document).on('click', '#transport_step_btn', function(){
        //     console.log('allotmentFlightData', allotmentFlightData);
        //     if(allotmentFlightData.length > 0){
        //         checkAllotment(5);
        //     }
        //  })

        // Check requited fields before moving to next step
        function updateMsg(variable, msg){
            variable = msg; 
        }

        function saveBookingData(data, summary){
            $.ajax({
                url: '<?php echo $data->api_endpoint; ?>/booking/save',
                type: 'POST',
                headers: {
                    'e-key': '<?php echo $eventkey;?>',
                    'merchant-key': '<?php echo $data->merchant_key; ?>'
                },
                data: data,
                success: function(response) {
                    // Handle success response
                    if(response.successful){
                        alert('Boekingsgegevens succesvol geplaatst!');
                        var bookingData = {
                            gl_first_name: $('#gl_first_name').val(),
                            gl_email: $('#gl_email').val(),
                            event_name: $('#booking_event').text(),
                            booking_code: response.booking_code,
                            summary: encodeURIComponent($('#summary_data').html()),
                        };
                        mailBookingData(bookingData, response.checkout_url);
                        // save booking summary to local storage
                        localStorage.setItem('booking_summary', summary);
                    }else{
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error('Fout bij het plaatsen van boekingsgegevens:', error);
                    // if(xhr.status == 200)
                    //     mailBookingData(bookingData);
                    alert('Fout bij het plaatsen van boekingsgegevens. Probeer het opnieuw.');
                }
            });
        }

        $(document).on('click', '#sendmail', function(e){
            var summary = `
                <div class="col-12 my-3" id="summary_data">
                                                <div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Volwassene(n)</p>
                                                            <span id="summary_adults_count">1</span>    
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Kinderen</p>
                                                            <span id="summary_children_count">0</span>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Baby's</p>
                                                            <span id="summary_children_under_3_count">0</span>
                                                        </div>                                                    
                                                    </div>
                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">02</span><span class="summ-heading">Bezoekersinformatie</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Hoofdboeker</p>
                                                            <span class="summary-sub-headings-txt"></span> <span id="booking_visitor_title_div">dhr.</span>&nbsp;<span id="booking_visitor_name_div">testuser  user</span><br>   
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Contactgegevens</p>
                                                            <div class="d-flex">
                                                                <div class="mr-2">
                                                                    <!-- <i class="fa-solid fa-location-dot"></i> -->
                                                                </div>
                                                                <div class="address"><span id="booking_visitor_address_div">ustraat 90, 7865rt, adam | 6789897978 | yanick.assignon@m2-d2.com</span><br></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Geboortedatum &amp; nationaliteit</p>
                                                            <span id="booking_visitor_birthdate_div">11 nov 1990 | Bermuda | Burger van Bosnië-Herzegovina</span>
                                                        </div>                                                    
                                                    </div>

                                                    <div id="extra_runners"></div>

                                                    <div class="row form-fields-rows thuisblijver-row">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p class="summary-table-head-subs">Thuisblijver</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p class="summary-table-head-subs">Contactgegevens</p>
                                                    </div>
                                                    </div>

                                                    <div class="row form-fields-rows thuisblijver-row">
                                                        <div class="col">
                                                        
                                                            <span id="booking_stayathome_title_div">dhr.</span>&nbsp;<span id="booking_stayathome_name_div">thuis  bl</span><br>   
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex">
                                                                <!-- <div class="mr-2">
                                                                    <i class="fa-solid fa-location-dot"></i>
                                                                </div> -->
                                                                <div class="address"><span id="booking_stayathome_address_div">9087-90-809-8 | yanick.assignon@m2-d2.com</span><br></div>
                                                            </div>
                                                        </div>
                                                        <div class="col"></div>
                                                        <!-- <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <span id="booking_stayathome_birthdate_div">
                                                        </div>                                                     -->
                                                    </div>

                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">03</span><span class="summ-heading">Startbewijzen</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Challenge</p>
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Aantal</p>
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Prijs per stuk</p>
                                                        </div>
                                                         <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_bibs_div"><div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>NEW BIB</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>1</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>€ 36,00</p>
                        </div>
                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>€ 36,00</p>
                        </div>
                    </div></div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading">Datums</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                                            <p class="summary-table-head-subs">Vertrek</p>
                                                            <span id="summary_departure_date" class="summary-body-txt">ma 16 okt 2023</span>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Aankomst</p>
                                                            <span id="summary_arrival_date" class="summary-body-txt">vr 20 okt 2023</span>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">05</span><span class="summ-heading">Hotel</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows" style="display:flex;flex-direction:column;justify-content:flex-start;align-content:flex-start;">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Hotel naam: <span id="summary_hotel_name" class="summary-body-txt">9Hotel Opera</span></p>
                                                            <p class="summary-table-head-subs">Aantal nachten: <span id="summary_hotel_nights" class="summary-body-txt">4</span></p>
                                                        </div>
                                                        <div id="hotel-room-details" style="display:flex;flex-direction:column;justify-content:flex-start;align-content:flex-start;width:95%;"><div class="row form-fields-rows">
                        <div class="col-md-5 col-lg-5 col-xl-5 strtbewijz-col4">
                            <p>Type hotelkamer: Business</p>
                        </div>
                         <div class="col-md-3 col-lg-4 col-xl-4 strtbewijz-col4">
                            <p>Prijs per nacht: € 75,00</p>
                        </div>
                        <div class="col-md-2 col-lg-2 col-xl-4 strtbewijz-col4">
                            <p>Antal Kamers: 1</p>
                        </div>
                        <div class="col-md-2 col-lg-2 col-xl-4 strtbewijz-col4">
                            <p>Prijs: € 75,00</p>
                        </div></div></div>
                                                    </div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">06</span><span class="summ-heading">Extra's</span></h3>
                                                </div>
                                                <div class="col-12 mob-hide">
                                                    <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Extra's van hotel</h4>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows hotel-extras" style="display: none;">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Opties</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Personen</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_extra_div"><p>Geen extra's geselecteerd</p></div>

                                                </div>
                                                <div class="col-12 mt-3 mob-hide">
                                                    <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Extra's buiten het hotel</h4>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows hotel-extras" style="display: none;">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Opties</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Personen</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_nonextra_div"><div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>Onderbroek</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>1</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>€ 100,00</p>
                        </div>
                    </div></div>


                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">07</span><span class="summ-heading">Vervoer</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">
                                                    <div id="flight-holder" style="display: block;">
                                                        <div class="row form-fields-rows">
                                                            <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Heenvlucht</h4>
                                                        </div>
                                                        <div class="row form-fields-rows">
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs summary-table-head-subs">Vlucht</p>
                                                                <span id="summary_go_flight_name" class="summary-body-txt">QQ --&gt; 123</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vertrek</p>
                                                                <span id="summary_go_departure" class="summary-body-txt">ma 16 okt 2023 - 09:00:00</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Aankomst</p>
                                                                <span id="summary_go_arrival" class="summary-body-txt">ma 16 okt 2023 - 10:00:00</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Reisklasse</p>
                                                                <span id="summary_go_travel_classe" class="summary-body-txt">-</span>
                                                            </div>
                                                        </div>

                                                        <div class="row form-fields-rows">
                                                            <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs" style="margin-top:20px;">Retourvlucht</h4>
                                                        </div>
                                                        <div class="row form-fields-rows">
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vlucht</p>
                                                                <span id="summary_return_flight_name" class="summary-body-txt">RR --&gt; www</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vertrek</p>
                                                                <span id="summary_return_departure" class="summary-body-txt">vr 20 okt 2023 - 06:00:00</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Aankomst</p>
                                                                <span id="summary_return_arrival" class="summary-body-txt">vr 20 okt 2023 - 07:00:00</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Reisklasse</p>
                                                                <span id="summary_return_travel_classe" class="summary-body-txt">Economy Class</span>
                                                            </div>
                                                        </div>
                                                        <div class="row summ-flight-deets-row">
                                                            <!-- <p class="summary-table-head-subs">Reisklasse</p> -->
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Aantal stoelen: <span id="summary_flight_seats">1</span></p></div>
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Prijs per stoel: <span id="summary_flight_price">€ 88,00</span></p></div>
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Prijs: <span id="summary_flight_total_price">€ 88,00</span></p></div>
                                                            <div class="col summ-flight-deets"></div>
                                                        </div>
                                                    </div>

                                                    <div id="summary_flight_div"></div>


                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">08</span><span class="summ-heading">Verzekeringen</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Verzekering</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_insurance_div"><div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>Travelinsurance eu</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>€ 11,18</p>
                        </div>
                    </div><div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>Cancellation insurance</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>€ 140,48</p>
                        </div>
                    </div></div>
    
                                                </div>
                                                <div class="col-12 my-3 box-padding-mob">
                                                    <h3 class="form-label-blue overigekost"><span class="summ-heading">Overige kosten<span class="summ-heading"></span></span></h3>
                                                </div>
                                                <div class="col-12">
                                                    <div class="row mb-1">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            SGR fee
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            <span class="summary-sub-headings-txt">+ €</span> <span id="booking_sgr_fee_div">12,25</span> <span class="summary-sub-headings-txt">per persoon</span>
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                                <span>Totaal: €<span style="margin-left:1px;" id="booking_sgr_fee_total">12,00</span></span>
                                                            
                                                            <span id="booking_sgr_fee_total"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            Administratiekosten verzekering
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            + <span id="booking_insurance_fee_div">0,00</span> <span class="summary-sub-headings-txt">% per verzekering</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            Calamiteitenfonds
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            <span class="summary-sub-headings-txt">+ €</span> <span id="booking_calamity_fund_div">2,50</span> per 9 personen
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">    
                                                            <span class="">Totaal: €</span> <span style="margin-left:1px;" id="booking_calamity_fund_total">2,50</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                                <div class="col-12">
                                                    <!-- <div class="col-8 col-sm-8 col-md-8 col-xl-8" style="width:40%;display: flex; flex-direction: column;align-items:flex-start;justify-content:flex-start">
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Verzekering<span id="insurance_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14x;">Calamiteitenfonds<span id="calamity_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">SGR fee<span id="sgrfee_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Boeking<span id="booking_summary" style="margin-left: 50px"></span></p>
                                                    </div> -->
                                                    <div class="row mb-2">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black" style="font-size:18px;font-weight:bold;">
                                                            Totaal
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary">
                                                            <span id="summary_total_booking" style="font-size:17px;font-weight:bold;">€ 689,50</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>
            `;
            // var summary = "%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%2\
            // 0%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-12%20mb-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22b\
            // adge%20badge-highlight%22%3E01%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3Ebezoekers%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%2\
            // 0%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob\
            // -hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%\
            // 20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%\
            // 20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-t\
            // able-head-subs%22%3EVolwassene(n)%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_adults_count%22%3E1%3C%2Fspan%3E%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EKinderen%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_children_count%22%3E0%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EBaby\
            // 's%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_children_under_3_count%22%3E0%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E02%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EBezoekersinformatie%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20\
            // overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EHoofdboeker%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20class%3D%22summary-sub-headings-txt%22%3E%3C%2Fspan%3E%20%3Cspan%20id%3D%22booking_visitor_title_div%22%3Edhr.%3C%2Fspan%3E%26nbsp%3B%3Cspan%20id%3D%22booking_visitor_name_div%22%3EAndrea%20%20G%3C%2Fspan%3E%3Cbr%3E%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EContactgegevens%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22d-flex%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22mr-2%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C!--%20%3Ci%20class%3D%22fa-solid%20fa-location-dot%22%3E%3C%2Fi%3E%20--%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22address%22%3E%3Cspan%20id%3D%22booking_visitor_address_div%22%3Egstraat%2098%2C%207623qe%2C%20rdam%20%7C%20563477654675%20%7C%20andrea.gericke%40m2-d2.com%3C%2Fspan%3E%3Cbr%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EGeboortedatum%20%26amp%3B%20nationaliteit%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22booking_visitor_birthdate_div%22%3E11%20nov%201990%20%7C%20Bosni%C3%AB%20en%20Herzegovina%20%7C%20Bengalese%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22extra_runners%22%3E%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%20thuisblijver-row%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EThuisblijver%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EContactgegevens%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%20thuisblijver-row%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22booking_stayathome_title_div%22%3Edhr.%3C%2Fspan%3E%26nbsp%3B%3Cspan%20id%3D%22booking_stayathome_name_div%22%3Ethuis%20%20bl%3C%2Fspan%3E%3Cbr%3E%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22d-flex%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C!--%20%3Cdiv%20class%3D%22mr-2%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ci%20class%3D%22fa-solid%20fa-location-dot%22%3E%3C%2Fi%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%20--%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22address%22%3E%3Cspan%20id%3D%22booking_stayathome_address_div%22%3E80790987%20%7C%20andrea.gericke%40m2-d2.com%3C%2Fspan%3E%3Cbr%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%22%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C!--%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22booking_stayathome_birthdate_div%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20--%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20\
            // %20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E03%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EStartbewijzen%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EChallenge%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EAantal%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPrijs%20per%20stuk%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPrijs%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22summary_bibs_div%22%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EDe%2010k%20run%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E1%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%2\
            // 0%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%2012%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%2012%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3ENEW%20BIB%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E1%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%2036%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-4%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%2036%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E04%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EDatums%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-8%20col-xl-8%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EVertrek%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_departure_date%22%20class%3D%22summary-body-txt%22%3Ema%2016%20okt%202023%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EAankomst%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_arrival_date%22%20class%3D%22summary-body-txt%22%3Evr%2020%20okt%202023%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%\
            // 20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E05%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EHotel%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%20style%3D%22display%3Aflex%3Bflex-direction%3Acolumn%3Bjustify-content%3Aflex-start%3Balign-content%3Aflex-start%3B%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EHotel%20naam%3A%20%3Cspan%20id%3D%22summary_hotel_name%22%20class%3D%22summary-body-txt%22%3E9Hotel%20Opera%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EAantal%20nachten%3A%20%3Cspan%20id%3D%22summary_hotel_nights%22%20class%3D%22summary-body-txt%22%3E4%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22hotel-room-details%22%20style%3D%22display%3Aflex%3Bflex-direction%3Acolumn%3Bjustify-content%3Aflex-start%3Balign-content%3Aflex-start%3Bwidth%3A95%25%3B%22%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-5%20col-lg-5%20col-xl-5%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EType%20hotelkamer%3A%20Basic%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-3%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EPrijs%20per%20nacht%3A%20%E2%82%AC%2050%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-2%20col-lg-2%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EAntal%20Kamers%3A%201%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-2%20col-lg-2%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EPrijs%3A%20%E2%82%AC%2050%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3C%2Fdiv%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E06%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%\
            // 3EExtra's%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20mob-hide%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch4%20class%3D%22body-14%20%20regular-400%20gray-1%20mb-1%20summary-table-head-subs%22%3EExtra's%20van%20hotel%3C%2Fh4%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%20hotel-extras%22%20style%3D%22display%3A%20none%3B%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EOpties%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPersonen%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPrijs%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22summary_extra_div%22%3E%3Cp%3EGeen%20extra's%20geselecteerd%3C%2Fp%3E%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20mt-3%20mob-hide%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch4%20class%3D%22body-14%20%20regular-400%20gray-1%20mb-1%20summary-table-head-subs%22%3EExtra's%20buiten%20het%20hotel%3C%2Fh4%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%20hotel-extras%22%20style%3D%22display%3A%20none%3B%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%2\
            // 0%3Cp%20class%3D%22summary-table-head-subs%22%3EOpties%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPersonen%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPrijs%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22summary_nonextra_div%22%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3EOnderbroek%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E1%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%20100%2C00%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3C%2Fdiv%3E%0A%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E07%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EVervoer%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22flight-holder%22%20style%3D%22display%3A%20block%3B%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch4%20class%3D%22body-14%20%20regular-400%20gray-1%20mb-1%20summary-table-head-subs%22%3EHeenvlucht%3C%2Fh4%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%\
            // 20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%20summary-table-head-subs%22%3EVlucht%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_go_flight_name%22%20class%3D%22summary-body-txt%22%3EQQ%20--%26gt%3B%20123%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EVertrek%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_go_departure%22%20class%3D%22summary-body-txt%22%3Ema%2016%20okt%202023%20-%2009%3A00%3A00%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EAankomst%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_go_arrival%22%20class%3D%22summary-body-txt%22%3Ema%2016%20okt%202023%20-%2010%3A00%3A00%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EReisklasse%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_go_travel_classe%22%20class%3D%22summary-body-txt%22%3E-%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch4%20class%3D%22body-14%20%20regular-400%20gray-1%20mb-1%20summary-table-head-subs%22%20style%3D%22margin-top%3A20px%3B%22%3ERetourvlucht%3C%2Fh4%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EVlucht%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_return_flight_name%22%20class%3D%22summary-body-txt%22%3ERR%20--%26gt%3B%20www%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EVertrek%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_return_departure%22%20class%3D%22summary-body-txt%22%3Evr%2020%20okt%202023%20-%2006%3A00%3A00%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EAankomst%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_return_arrival%22%20class%3D%22summary-body-txt%22%3Evr%2020%20okt%202023%20-%2007%3A00%3A00%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%20strtbewijz-col4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EReisklasse%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_return_travel_classe%22%20class%3D%22summary-body-txt%22%3EEconomy%20Class%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20summ-flight-deets-row%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C!--%20%3Cp%20class%3D%22summary-table-head-subs%22%3EReisklasse%3C%2Fp%3E%20--%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%20summ-flight-deets%22%3E%3Cp%20class%3D%22summary-body-txt%22%3EAantal%20stoelen%3A%20%3Cspan%20id%3D%22summary_flight_seats%22%3E1%3C%2Fspan%3E%3C%2Fp%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%20summ-flight-deets%22%3E%3Cp%20class%3D%22summary-body-txt%22%3EPrijs%20per%20stoel%3A%20%3Cspan%20id%3D%22summary_flight_price%22%3E%E2%82%AC%2088%2C00%3C%2Fspan%3E%3C%2Fp%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%20summ-flight-deets%22%3E%3Cp%20class%3D%22summary-body-txt%22%3EPrijs%3A%20%3Cspan%20id%3D%22summary_flight_total_price%22%3E%E2%82%AC%2088%2C00%3C%2Fspan%3E%3C%2Fp%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col%20summ-flight-deets%22%3E%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22summary_flight_div%22%3E%3C%2Fdiv%3E%0A%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20mob-hide%20summ-head-box%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%22%3E%3Cspan%20class%3D%22badge%20badge-highlight%22%3E08%3C%2Fspan%3E%3Cspan%20class%3D%22summ-heading%22%3EVerzekeringen%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20table-responsive%20overflow-y-clip%20mob-hide%22%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EVerzekering%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-8%20col-xl-8%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20class%3D%22summary-table-head-subs%22%3EPrijs%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20id%3D%22summary_insurance_div%22%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3ETravelinsurance%20eu%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-8%20col-xl-8%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%2011%2C18%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3Cdiv%20class%3D%22row%20form-fields-rows%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-4%20col-xl-4%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3ECancellation%20insurance%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-md-6%20col-lg-8%20col-xl-8%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%3E%E2%82%AC%20117%2C60%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%3C%2Fdiv%3E%0A%20%20%20%20%0A%20%\
            // 20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%20my-3%20box-padding-mob%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Ch3%20class%3D%22form-label-blue%20overigekost%22%3E%3Cspan%20class%3D%22summ-heading%22%3EOverige%20kosten%3Cspan%20class%3D%22summ-heading%22%3E%3C%2Fspan%3E%3C%2Fspan%3E%3C%2Fh3%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20mb-1%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-7%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-table-head-subs%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20SGR%20fee%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-body-txt%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20class%3D%22summary-sub-headings-txt%22%3E%2B%20%E2%82%AC%3C%2Fspan%3E%20%3Cspan%20id%3D%22booking_sgr_fee_div%22%3E12%2C25%3C%2Fspan%3E%20%3Cspan%20class%3D%22summary-sub-headings-txt%22%3Eper%20persoon%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-body-txt%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%3ETotaal%3A%20%E2%82%AC%3Cspan%20style%3D%22margin-left%3A1px%3B%22%20id%3D%22booking_sgr_fee_total%22%3E12%2C00%3C%2Fspan%3E%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22booking_sgr_fee_total%22%3E%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20mb-1%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-7%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-table-head-subs%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20Administratiekosten%20verzekering%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-body-txt%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%2B%20%3Cspan%20id%3D%22booking_insurance_fee_div%22%3E0%2C00%3C%2Fspan%3E%20%3Cspan%20class%3D%22summary-sub-headings-txt%22%3E%25%20per%20verzekering%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-7%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-table-head-subs%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20Calamiteitenfonds%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-body-txt%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20class%3D%22summary-sub-headings-txt%22%3E%2B%20%E2%82%AC%3C%2Fspan%3E%20%3Cspan%20id%3D%22booking_calamity_fund_div%22%3E2%2C50%3C%2Fspan%3E%20per%209%20personen%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20body-14%20medium-500%20gray-6%20summary-body-txt%22%3E%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20class%3D%22%22%3ETotaal%3A%20%E2%82%AC%3C%2Fspan%3E%20%3Cspan%20style%3D%22margin-left%3A1px%3B%22%20id%3D%22booking_calamity_fund_total%22%3E2%2C50%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Chr%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C!--%20%3Cdiv%20class%3D%22col-8%20col-sm-8%20col-md-8%20col-xl-8%22%20style%3D%22width%3A40%25%3Bdisplay%3A%20flex%3B%20flex-direction%3A%20column%3Balign-items%3Aflex-start%3Bjustify-content%3Aflex-start%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20style%3D%22width%3A100%25%3Bdisplay%3Aflex%3Bflex-direction%3Arow%3Bjustify-content%3Aspace-between%3Balign-items%3Acenter%3Bmargin-bottom%3A0px%3Btext-align%3Aleft%3Bfont-size%3A14px%3B%22%3EVerzekering%3Cspan%20id%3D%22insurance_summary%22%20style%3D%22margin-left%3A%2050px%22%3E%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20style%3D%22width%3A100%25%3Bdisplay%3Aflex%3Bflex-direction%3Arow%3Bjustify-content%3Aspace-between%3Balign-items%3Acenter%3Bmargin-bottom%3A0px%3Btext-align%3Aleft%3Bfont-size%3A14x%3B%22%3ECalamiteitenfonds%3Cspan%20id%3D%22calamity_summary%22%20style%3D%22margin-left%3A%2050px%22%3E%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20style%3D%22width%3A100%25%3Bdisplay%3Aflex%3Bflex-direction%3Arow%3Bjustify-content%3Aspace-between%3Balign-items%3Acenter%3Bmargin-bottom%3A0px%3Btext-align%3Aleft%3Bfont-size%3A14px%3B%22%3ESGR%20fee%3Cspan%20id%3D%22sgrfee_summary%22%20style%3D%22margin-left%3A%2050px%22%3E%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cp%20style%3D%22width%3A100%25%3Bdisplay%3Aflex%3Bflex-direction%3Arow%3Bjustify-content%3Aspace-between%3Balign-items%3Acenter%3Bmargin-bottom%3A0px%3Btext-align%3Aleft%3Bfont-size%3A14px%3B%22%3EBoeking%3Cspan%20id%3D%22booking_summary%22%20style%3D%22margin-left%3A%2050px%22%3E%3C%2Fspan%3E%3C%2Fp%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%20--%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22row%20mb-2%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-7%20col-md-6%20col-xl-4%20caption%20text-black%22%20style%3D%22font-size%3A18px%3Bfont-weight%3Abold%3B%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20Totaal%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22box-padding-mob%20col-6%20col-sm-5%20col-md-6%20col-xl-4%20caption%20theme-primary%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cspan%20id%3D%22summary_total_booking%22%20style%3D%22font-size%3A17px%3Bfont-weight%3Abold%3B%22%3E%E2%82%AC%20578%2C50%3C%2Fspan%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Cdiv%20class%3D%22col-12%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3Chr%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%3C%2Fdiv%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20";
            var bookingData = {
                gl_first_name: "Andrea",
                gl_email: "andrea.gericke@m2-d2.com",
                // gl_email: "yanick.assignon@m2-d2.com",
                event_name: $('#booking_event').text(),
                booking_code: "test_booking_code",
                // summary: encodeURIComponent($('#summary_data').html()),
                summary: encodeURIComponent(summary)
            };
            mailBookingData(bookingData, "");
    });

        $(document).on('click', '.btn-form-step', function(e) {
            var stepType     = $(this).attr('type'), 
                sourceStep   = $(this).data('source'),
                targetStep   = $(this).data('target'),
                percent = parseInt($(this).data('percent')),
                errorMessage = '';
                var currentStep = $(this);
                var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());

                var bibsCount = [];
                var hotelRoomCount = [];
                $('input[type="number"].bibs_count').each(function() {
                    bibsCount.push(parseInt($(this).val()) || 0);
                });
                var bibsCountSum = bibsCount.reduce((total, num) => total + num, 0);
                var runnersCount = $('#form_section2 input.traveller_is_runner:checked').length;

                $('input[type="number"].rooms_count').each(function() {
                    hotelRoomCount.push(parseInt($(this).val()) || 0);
                });
                var hotelRoomCountSum = hotelRoomCount.reduce((total, num) => total + num, 0);

            if (stepType === 'submit') {
                postBookingDetails();
            } else {
                function validateStep(){
                    $( sourceStep + ' [required]').each(function() {
                        var fieldValue = $(this).val();
                        var fieldType = $(this).prop('nodeName').toLowerCase(); // Get the type of element
                        var fieldName = $(this).attr('name');
                        var fieldTitle = $(this).attr('placeholder');
                        var fieldId = $(this).attr('id');

                        // Check field type and set appropriate error message
                        if (fieldType === 'input') {
                            fieldType = $(this).attr('type'); // Get the type attribute for input elements
                        } else if (fieldType === 'select') {
                            fieldType = 'select-one'; // For select elements, set type to select-one
                        }
                        
                        // if (fieldName != "hotel_room_count[]" && fieldType === 'number' && (fieldValue <= 0 || fieldValue == "" )) {

                        var bibsCount = [];
                        var bibFilterDate = [];
                        $('input[type="number"].bibs_count').each(function() {
                            bibsCount.push(parseInt($(this).val()) || 0);
                            const filteredInputs = filterInputsByEventDate($(this).data('event-date'));
                            bibFilterDate.push(filteredInputs);
                        });
                        var bibCountPerDay = removeDuplicateArrays(bibFilterDate);

                        var bibArrSum = bibCountPerDay.flat().reduce((acc, val) => acc + val, 0);
                        var runnersTotalSum = parseInt(bibCountPerDay.length)*runnersCount;

                        if(sourceStep === "#form_section3"){
                            var bools = [];
                            // if(localStorage.getItem('allotmentCheckStatus') == 'false'){
                            //     errorMessage = localStorage.getItem('allotmentCheckMsg');
                            // }else{
                            if(fieldTitle == "Bib" && bibsCountSum == 0){
                                errorMessage = 'Selecteer ten minste 1 bib.<br/>';
                            }else{
                                for (var i = 0; i < bibCountPerDay.length; i++) {
                                    var sum = bibCountPerDay[i].reduce((acc, val) => acc + val, 0);
                                    if (fieldTitle == "Bib" &&  runnersCount > 0 && bibArrSum > 0 && sum <= runnersCount) {
                                        bools.push(true);
                                    } else {
                                        bools.push(false);
                                    }

                                    if(bools.includes(false)){
                                        errorMessage = 'Het aantal startbewijzen mag op iedere dag niet hoger zijn dan het aantal hardlopers <br/>';
                                    }else{
                                        errorMessage = '';
                                        $(sourceStep).removeClass('show');
                                        $(targetStep).addClass('show');
                                        goNextStep(currentStep, 'collapse');
                                        $('#progress-bar').css('width', percent + '%').html(percent + '%');
                                    }
                                }
                            }
                            // }
                        }

                        if(sourceStep === "#form_section5"){
                            // if(localStorage.getItem('allotmentCheckStatus') == 'false'){
                            //     $(sourceStep).addClass('show');
                            //     $(targetStep).removeClass('show');
                            //     goNextStep(currentStep, 'null');
                            //     errorMessage = localStorage.getItem('allotmentCheckMsg');
                            // }
                            if ($('#hotel_rooms_container').children().length == 0 && fieldTitle == "GetHotels") {
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                errorMessage = 'Selecteer ten minste 1 hotelkamer.<br/>';
                            }else if(hotelRoomCountSum > travelers){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                errorMessage = 'Het aantal hotelkamers mag niet hoger zijn dan het aantal reizigers <br/>';
                            }
                        }


                        if (fieldType === 'number' && (fieldValue <= 0 || fieldValue == "" )) {
                            
                            // $.each(bibCountPerDay, function(index, value){
                            // var bibSum = value.reduce((total, num) => total + num, 0);
                            // console.log('bibSum', bibSum);
                            
                            // });
                            //  if(sourceStep === "#form_section5" && localStorage.getItem('allotmentCheckStatus') == 'false'){
                            //     console.log('#form_section5');
                            //     $(sourceStep).addClass('show');
                            //     $(targetStep).removeClass('show');
                            //     goNextStep(currentStep, 'null');
                            //     errorMessage = localStorage.getItem('allotmentCheckMsg');
                            // }
                            // if(sourceStep === "#form_section7" && localStorage.getItem('allotmentCheckStatus') == 'false'){
                            //     console.log('#form_section7');
                            //     $(sourceStep).addClass('show');
                            //     $(targetStep).removeClass('show');
                            //     goNextStep(currentStep, 'null');
                            //     errorMessage = localStorage.getItem('allotmentCheckMsg');
                            // }
                            if(sourceStep === "#form_section5" && travelers > 1 && validateHotelRooms(travelers, hotelRoomCountSum).valid === false){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                errorMessage = validateHotelRooms(travelers, hotelRoomCountSum).message;
                            }else if (hotelRoomCountSum > 0 && hotelRoomCountSum <= travelers){
                                errorMessage = '';
                                $(sourceStep).removeClass('show');
                                $(targetStep).addClass('show');
                                goNextStep(currentStep, 'collapse');
                                $('#progress-bar').css('width', percent + '%').html(percent + '%');
                            }else if(hotelRoomCountSum <= 0 && fieldTitle == "Hotelroom"){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                errorMessage = 'Selecteer ten minste 1 hotelkamer.<br/>';
                            }

                            if(fieldTitle != "Bib" && fieldTitle != "Hotelroom"){
                                errorMessage = 'Vul het volgende in: ' + fieldTitle + '.<br/>';
                            }
                        }
                        else if($('input[name="flightplan_id_OLD"]:checked').length <= 0 && fieldTitle == "owntransport"){
                            errorMessage = 'Selecteer een vervoer.<br/>';
                        }else if(fieldTitle == "flighttransport" && $('input[id="flight-transport"]').is(':checked') && $('input[name="flightplan_list"]:checked').length <= 0){
                            errorMessage = 'Selecteer een vliegtuigvlucht.<br/>';
                        }
                        else if(fieldTitle == "Bib" && bibsCountSum == 0){
                            errorMessage = 'Selecteer ten minste 1 bib.<br/>';
                        }else if (fieldType === 'text' && fieldValue == "") {
                            errorMessage = 'Vul het volgende in ' + fieldTitle + '.<br/>';
                        } else if (fieldType === 'textarea' && fieldValue == "") {
                            errorMessage = 'Vul het volgende in ' + fieldTitle + '.<br/>';
                        } else if (fieldType === 'tel' && fieldValue == "") {
                            errorMessage = 'Vul het volgende in ' + fieldTitle + '.<br/>'; 
                        } else if (fieldType === 'email' && fieldValue == "") {
                            errorMessage = 'Vul het volgende in ' + fieldTitle + '.<br/>'; 
                        }  else if (fieldType === 'select-one') {
                            if (!fieldValue) {
                                errorMessage = 'Kies een optie voor ' + fieldTitle + '.<br/>';
                            }
                        }else if (fieldType === 'checkbox' || fieldType === 'radio') {
                            var fieldGroup = $(this).attr('name');
                            if ($('input[name="' + fieldGroup + '"]:checked').length === 0) {
                                errorMessage = 'Kies een optie voor ' + fieldTitle + '.<br/>';
                            }
                            if ($('input[name="flightplan_id_OLD"]:checked').length === 0){
                                errorMessage = 'Selecteer een vervoer.<br/>';
                            }
                        
                        }
                    });

                    $( sourceStep + ' .error-message').html(errorMessage);

                    if (errorMessage !== "") {
                        //alert('Please fill out all required fields.');
                        //e.stopPropagation();
                        $(sourceStep).addClass('show');
                        $(targetStep).removeClass('show');
                        goNextStep(currentStep, 'null');
                    } else {
                        $(sourceStep).removeClass('show');
                        $(targetStep).addClass('show');
                        goNextStep(currentStep, 'collapse');
                        $('#progress-bar').css('width', percent + '%').html(percent + '%');
                        updateSummaryStep1();
                    }
                }

                // check if emails are the same
                if(sourceStep == "#form_section2"){
                    if($('#gl_email').val() != $('#gl_email_confirm').val() || $('#sah_email').val() != $('#sah_email_confirm').val()){
                        errorMessage = 'E-mails komen niet overeen';
                        $(sourceStep).addClass('show');
                        $(targetStep).removeClass('show');
                        goNextStep(currentStep, 'null');
                    }else{
                        errorMessage = '';
                        $(sourceStep).removeClass('show');
                        $(targetStep).addClass('show');
                        goNextStep(currentStep, 'collapse');
                    }
                }

                if(sourceStep == "#form_section2" || sourceStep == "#form_section4" || sourceStep == "#form_section6" || sourceStep == "#form_section8"){
                    validateStep();
                }

                var loaderText = "<p style='font-size:13px;text-transform:uppercase;margin:0px'>In Behandeling...</p>";
                if(sourceStep == "#form_section1"){
                    $('#travelers_form_section svg').hide();
                    $('#travelers_form_txt').html(loaderText);
                    $.ajax({
                        url: '<?php echo $data->api_endpoint; ?>/booking/merchant-linkedTo-event/',
                        type: 'GET',
                        headers: {
                            'e-key': '<?php echo $eventkey;?>',
                            'merchant-key': '<?php echo $data->merchant_key; ?>'
                        },
                        success: function(response) {
                            if (response.success == false){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                alert(response.message);
                                $('#travelers_form_section svg').show();
                                $('#travelers_form_txt').html('GA DOOR');
                            }else{
                                $('#travelers_form_section svg').show();
                                $('#travelers_form_txt').html('GA DOOR');
                                validateStep();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error checking flights allotment:', error);
                            alert('Er is een fout opgetreden bij het controleren van de beschikbaarheid, neem contact op met Loopreizen.be via het contactformulier of +31 10 12345678');
                        },
                    });
                }

                // check allotment
                if(sourceStep === "#form_section3"){
                    $('#bibs_form_section svg').hide();
                    $('#bibs_form_txt').html(loaderText);
                    // get bibs data
                    bibsAllotmentData = [];
                    var parentDiv = $(this).closest('.card-body');
                    var bibsForm = parentDiv.find('#bibs_form');
                    var bibsInput = bibsForm.find('input.bibs_count');
                    bibsInput.each(function() {
                        if($(this).val() > 0){
                            var bibCountValue = $(this).val();
                            var bibId = $(this).data('bib-id');
                            bibsAllotmentData.push({
                                bib_id: bibId,
                                bib_count: bibCountValue
                            });
                        }
                    });

                    $.ajax({
                        url: '<?php echo $data->api_endpoint; ?>/booking/check-allotment/',
                        type: 'GET',
                        headers: {
                            'e-key': '<?php echo $eventkey;?>',
                            'merchant-key': '<?php echo $data->merchant_key; ?>'
                        },
                        data: {
                            bibs: JSON.stringify(bibsAllotmentData),
                            rooms: JSON.stringify(hotelRoomsData),
                            flight_allotment_seats: JSON.stringify(allotmentFlightData),
                            productCategory: 1
                        },
                        success: function(response) {
                            if (response.success == false){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                alert(response.message);
                                $('#bibs_form_section svg').show();
                                $('#bibs_form_txt').html('GA DOOR');
                            }else{
                                $('#bibs_form_section svg').show();
                                $('#bibs_form_txt').html('GA DOOR');
                                validateStep();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error checking bibs allotment:', error);
                            alert('Er is een fout opgetreden bij het controleren van de beschikbaarheid, neem contact op met Loopreizen.be via het contactformulier of +31 10 12345678');
                        },
                    });
                }else if(sourceStep === "#form_section5"){
                    $('#hotel_room_form_section svg').hide();
                    $('#hotel_room_form_txt').html(loaderText);
                    // get hotel room data
                    hotelRoomsData = [];
                    var parentDiv = $(this).closest('.card-body');
                    var hotelForm = parentDiv.find('#hotel_form');
                    var hotelRoomInput = hotelForm.find('input.rooms_count');
                    hotelRoomInput.each(function() {
                        if($(this).val() > 0){
                            var roomCountValue = $(this).val();
                            var roomId = $(this).attr('name');
                            var hotelId = $(this).attr('hotel-id');
                            hotelRoomsData.push({
                                room_id: roomId,
                                room_count: roomCountValue,
                                hotel_id: hotelId
                            });
                        }
                    });

                    $.ajax({
                        url: '<?php echo $data->api_endpoint; ?>/booking/check-allotment/',
                        type: 'GET',
                        headers: {
                            'e-key': '<?php echo $eventkey;?>',
                            'merchant-key': '<?php echo $data->merchant_key; ?>'
                        },
                        data: {
                            bibs: JSON.stringify(bibsAllotmentData),
                            rooms: JSON.stringify(hotelRoomsData),
                            flight_allotment_seats: JSON.stringify(allotmentFlightData),
                            productCategory: 2
                        },
                        success: function(response) {
                            if (response.success == false){
                                $(sourceStep).addClass('show');
                                $(targetStep).removeClass('show');
                                goNextStep(currentStep, 'null');
                                alert(response.message);
                                $('#hotel_room_form_section svg').show()
                                $('#hotel_room_form_txt').html('GA DOOR');
                            }else{
                                $('#hotel_room_form_section svg').show()
                                $('#hotel_room_form_txt').html('GA DOOR');
                                validateStep();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error checking hotel rooms allotment:', error);
                            alert('Er is een fout opgetreden bij het controleren van de beschikbaarheid, neem contact op met Loopreizen.be via het contactformulier of +31 10 12345678');
                        },
                    });
                }
                else if(sourceStep === "#form_section7"){
                    if(allotmentFlightData.length > 0){
                        $('#transport_step_btn svg').hide()
                        $('#transport_step_txt').html(loaderText);
                        $.ajax({
                            url: '<?php echo $data->api_endpoint; ?>/booking/check-allotment/',
                            type: 'GET',
                            headers: {
                                'e-key': '<?php echo $eventkey;?>',
                                'merchant-key': '<?php echo $data->merchant_key; ?>'
                            },
                            data: {
                                bibs: JSON.stringify(bibsAllotmentData),
                                rooms: JSON.stringify(hotelRoomsData),
                                flight_allotment_seats: JSON.stringify(allotmentFlightData),
                                productCategory: 5
                            },
                            success: function(response) {
                                if (response.success == false){
                                    $(sourceStep).addClass('show');
                                    $(targetStep).removeClass('show');
                                    goNextStep(currentStep, 'null');
                                    alert(response.message);
                                    $('#transport_step_btn svg').show()
                                    $('#transport_step_txt').html('GA DOOR');
                                }else{
                                    $('#transport_step_btn svg').show()
                                    $('#transport_step_txt').html('GA DOOR');
                                    validateStep();
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error checking flights allotment:', error);
                                alert('Er is een fout opgetreden bij het controleren van de beschikbaarheid, neem contact op met Loopreizen.be via het contactformulier of +31 10 12345678');
                            },
                        });
                    } else{
                        validateStep();
                    }
                }
            }

        });

        $(document).on('click', '.traveler-plus-min', function(e) {
            var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
            $('#runners_div').html('');
            for (var i = 1; i <= travelers-1; i++) {
                addTravellerToForm(i+1); // +1 because the first traveller is already added
            }
        });

        // Increasing and decreasing the quantity (travellers, bibs, hotel rooms)
        $(document).on('click', '.plus-minus-input .button', function() {
            var $input = $(this).closest('.plus-minus-input').find('.input-group-field'),
            //  fieldName = $(this).data('field'),
                type = $(this).data('quantity'),
                currentValue = parseInt($input.val(), 10);
            
            if (!isNaN(currentValue)) {
                // update flight price calculation
                var arrivalPrice = $('#total_flight_arrival_price').val();
                var arrivalUnitPrice = parseFloat($('.flight-arrival option:selected').data('price'));

                var departurePrice = $('#total_flight_departure_price').val();
                var departureUnitPrice = parseFloat($('.flight-departure option:selected').data('price'));
                var totalFlightPrice = parseFloat(arrivalPrice) + parseFloat(departurePrice);

                if (type === 'minus') {
                    if (currentValue > 0) {
                        $input.val(currentValue - 1);
                    }
                    var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                    if(!isNaN(arrivalUnitPrice) && !isNaN(departureUnitPrice) || arrivalUnitPrice != 0 && departureUnitPrice != 0){    
                        $('#total_flight_arrival_price').val(arrivalUnitPrice*travelersCount);
                        $('#total_flight_departure_price').val(departureUnitPrice*travelersCount);
                    }
                } else if (type === 'plus') {
                    if($(this).data('bibs-max')) {
                        var bibsCount = [];
                        var bibFilterDate = [];
                        $('input[type="number"].bibs_count').each(function() {
                            bibsCount.push(parseInt($(this).val()) || 0);
                            const filteredInputs = filterInputsByEventDate($(this).data('event-date'));
                            // console.log('filteredInputs', filteredInputs);
                            bibFilterDate.push(filteredInputs);
                        });
                        // remove duplicate from bibFilterDate with set
                        // bibFilterDate = [...new Set(bibFilterDate)];
                        var bibCountPerDay = removeDuplicateArrays(bibFilterDate);
                        // console.log('bibFilterDate', bibCountPerDay);

                        // let bibsCountSum = bibsCount.reduce((total, num) => total + num, 0);
                        var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        var runnersCount = $('#form_section2 input.traveller_is_runner:checked').length;

                        $.each(bibCountPerDay, function(index, value){
                            var bibSum = value.reduce((total, num) => parseInt(total) + parseInt(num), 0);
                            if (parseInt(bibSum) < parseInt(runnersCount)) {
                                $input.val(currentValue + 1);
                            }
                        });
                        // if (bibsCountSum < parseInt(travelersCount)) {
                        if(parseInt(runnersCount) == 0){
                            alert('Eeen of meer reizigers moeten hardlopers zijn');
                        }
                        // if (bibsCountSum < parseInt(runnersCount)) {
                        //     $input.val(currentValue + 1);
                        // }
                    }else if($(this).data('hotels-max')){
                        // var hotelCount = [];
                        // $('input[type="number"].rooms_count').each(function() {
                        //     hotelCount.push(parseInt($(this).val()) || 0);
                        // });
                        // let hotelCountSum = hotelCount.reduce((total, num) => total + num, 0);
                        var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        if (currentValue < parseInt(travelersCount)) {
                            $input.val(currentValue + 1);
                        }
                    }else if($(this).data('hotel-extras-max')){
                        // var extraCount = [];
                        // $('input[type="number"].extra_count').each(function() {
                        //     extraCount.push(parseInt($(this).val()) || 0);
                        // });
                        // let extraCountSum = extraCount.reduce((total, num) => total + num, 0);
                        var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        if (currentValue < parseInt(travelersCount)) {
                            $input.val(currentValue + 1);
                        }
                    }else if($(this).data('non-hotel-extras-max')) {
                        var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        if (currentValue < parseInt(travelersCount)) {
                            $input.val(currentValue + 1);
                        }
                    } else{
                        $input.val(currentValue + 1);
                    }
                    // update flight price calculation
                    var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                    if(!isNaN(arrivalUnitPrice) && !isNaN(departureUnitPrice) || arrivalUnitPrice != 0 && departureUnitPrice != 0){ 
                        $('#total_flight_arrival_price').val(arrivalUnitPrice*travelersCount);
                        $('#total_flight_departure_price').val(departureUnitPrice*travelersCount);
                    }
                }
            } else {
                $input.val(0);
            }

            // Add/delete travellers section
            var adults_count = $('#adults_count').val();
            var children_count = $('#children_count').val();
            var children_under_3_count = $('#children_under_3_count').val();

            // Write to summary
            $('#summary_adults_count').html(adults_count);
            $('#summary_children_count').html(children_count);
            $('#summary_children_under_3_count').html(children_under_3_count);

            // var travelers = parseInt(adults_count) + parseInt(children_count) + parseInt(children_under_3_count) - 1;
            // TODO: confirm by Arie why the -1 is there
            var travelers = parseInt(adults_count) + parseInt(children_count) + parseInt(children_under_3_count);
 
            // if (parseInt(travelers) > 1) {
            //     addTravellerToForm(travelers);
            // } else if (parseInt(travelers) < 2) {
            //     removeTravellerToForm();
            // }
            if (parseInt(travelers) < 2) {
                removeTravellerToForm();
            }
            $('#travellers_amount').val(travelers);

            // Bibs price calculation
            var total_bibs_count = 0,
                total_bibs_price = 0;
            $( '.bibs-item input.bibs_count').each(function() {
                total_bibs_count += parseInt($(this).val());
                total_bibs_price += parseInt($(this).val()) * parseFloat($(this).data('price'));
            });
            $('#total_bibs_count').val(total_bibs_count);
            $('#total_bibs_price').val(total_bibs_price);

            // Hotel room price calculation           
            var total_room_count = 0,
                total_room_price = 0;
            $( '.rooms-item input.rooms_count').each(function() {
                total_room_count += parseInt($(this).val());
                total_room_price += parseInt($(this).val()) * parseFloat($(this).data('price'));
            });
            $('#total_room_count').val(total_room_count);
            $('#total_room_price').val(total_room_price);
            $('#summary_room_price').html(total_room_price.toFixed(2));

            // Hotel extra price calculation 
            var total_extra_count = 0,
                total_extra_price = 0;
            $( '.hotelextra-item input.extra_count').each(function() {
                total_extra_count += parseInt($(this).val());
                total_extra_price += parseInt($(this).val()) * parseFloat($(this).data('price'));
            });
            $('#total_extra_count').val(total_extra_count);
            $('#total_extra_price').val(total_extra_price); 

            // Non-Hotel extra price calculation  
            var total_nonextra_count = 0,
                total_nonextra_price = 0;
            $( '.nonhotelextra-item input.nonextra_count').each(function() {
                total_nonextra_count += parseInt($(this).val());
                total_nonextra_price += parseInt($(this).val()) * parseFloat($(this).data('price'));
            });
            $('#total_nonextra_count').val(total_nonextra_count);
            $('#total_nonextra_price').val(total_nonextra_price); 

            // Write to summary
            $('#summary_adults_count').html(adults_count);
            $('#summary_children_count').html(children_count);
            $('#summary_children_under_3_count').html(children_under_3_count);

            updateSummaryStep1();
            updateSummaryStep3();
            updateSummaryStep6();
            // getTotals();

        });
    
        function removeTravellerToForm() {
            $('#runners_div .runner_info').last().remove();
        }

        $(document).on('click', '.vervoer-radio-btn-group .vervoer-radio-btn-label input', function() {
            var transport_id = $(this).val();
            $('#flight_plan_id').val(transport_id);
        });

        function nationalities(){
            $.ajax({
                url: '<?php echo $data->api_endpoint; ?>/nationalities-list/?locale=nl',
                type: 'GET',
                data: {
                },
                success: function(response) {

                    // Check if response is valid
                    if (response && response.type === 'success') {
                        var nationalities = response.data;
                        // nationalitiesArr = response.data;

                        $.each(nationalities, function(index, nationality) {
                            if (!$('select[name="gl_nationality"] option[value="' + nationality.id + '"]').length) {
                                $('select[name="gl_nationality"]').append('<option value="' + nationality.id + '">' + nationality.name + '</option>');
                            }
                            if (!$('select[name="v_nationality"] option[value="' + nationality.id + '"]').length) {
                                $('select[name="v_nationality"]').append('<option value="' + nationality.id + '">' + nationality.name + '</option>');
                            }
                        });
                    } else {
                        console.error('Failed to fetch nationalities:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching nationalities:', error);
                }
            });
        }

        function addMainTravellerDob() {
            $("#main-traveller-dob").html();
            var dob = `
                <div class="form-group">
                    <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                    <input class="form-control" type="date" id="gl_dateofbirth" name="gl_dateofbirth" placeholder="Geboortedatum" required>
                </div>
            `;
            // Append the HTML to the runners_div
            $('#main-traveller-dob').html(dob);
        }
        addMainTravellerDob();

        
        function addStayHomeDob() {
            $("#stay-home-dob").html();
            var dob = `
                <div class="form-group">
                    <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                    <input class="form-control" type="date" id="gl_dateofbirth" name="gl_dateofbirth" placeholder="Geboortedatum" required>
                </div>
            `;
            // Append the HTML to the runners_div
            $('#stay-home-dob').html(dob);
        }
        addStayHomeDob();

        function addTravellerToForm(i = 1) {
            var htmlToAdd = `
                <div class="runner_info extra_runners_info">
                    <div class="row">
                        <div class="col-12">
                            <div class="visitor">
                                <div class="align-self-center">
                                    <p class="caption theme-color-secondary mb-0 form-label-blue">REIZIGER #${i}</p>
                                </div>
                                <div class="mt-2">
                                    <div class="custom-control custom-checkbox">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="v_is_runner[]" id="traveller_is_runner_${i}" class="custom-control-input form-input-checkbox traveller_is_runner">
                                            <label title="" for="traveller_is_runner_${i}" class="custom-control-label"></label>
                                        </div>
                                        <label class="form-label">
                                            <span class="checkbox-label ml-2">REIZIGER #${i} is een hardloper</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-fields-rows fm-rws-travelers">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label field-label">Titel <span class="required">*</span></label>
                                <select placeholder="Titel" data-placeholder="Titel" name="v_title[]" id="v_title_${i}" class="pl-2 form-control form-select" required>
                                    <option value="dhr.">dhr.</option>
                                    <option value="mevr.">mevr.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">

                            <div class="form-group">
                                <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                                <input class="form-control" type="date" id="v_dob_${i}" name="v_dob[]" placeholder="Date of birth" required>
                            </div>
                        
                        </div>
                        <div class="col-4">

                            <div class="form-group">
                                <label class="form-label field-label">Nationaliteit <span class="required">*</span></label>
                                <select placeholder="Nationaliteit" class="form-control form-select" name="v_nationality" id="v_nationality_${i}" required>
                                    <option value="">Nationaliteit</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="row form-fields-rows  fm-rws-travelers">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="form-label field-label">Voornaam (volgens paspoort)  <span class="required">*</span></label>
                                <input type="text" placeholder="Voornaam" name="v_first_name[]" id="v_first_name_${i}" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="form-label field-label">Tussenvoegsel (optioneel)</label>
                                <input type="text" placeholder="Tussenvoegsel" name="v_middle_name[]" id="v_middle_name_${i}" class="form-control">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="form-label field-label">Achternaam (volgens paspoort)  <span class="required">*</span></label>
                                <input type="text" placeholder="Achternaam" name="v_last_name[]" id="v_last_name_${i}" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
    
            // Append the HTML to the runners_div
            $('#runners_div').append(htmlToAdd);

            $('.form-select').select2();
            nationalities();
        }

        nationalities();
        
        // add the extra traveler info to summary
        extraRunnersData = [];
        $(document).on('click', '.btn-form-step', function(e) {
           if($(this).data('source') == "#form_section2"){
            // console.log('nation selected', $(this).find('select[name="gl_nationality"] option:selected').text());
            // console.log('nation text()', $(this).find('select[name="gl_nationality"]').text());
                $('#extra_runners').html('');
                $(".extra_runners_info").each(function(){
                    var options = { year: 'numeric', month: 'short', day: 'numeric' };
                    var dof =  new Date( $(this).find('input[name="v_dob[]"]').val() );
                    var booking_visitor_title = $(this).find('select[name="v_title[]"]').val();
                    var booking_visitor_name = $(this).find('input[name="v_first_name[]"]').val() + ' ' + $(this).find('input[name="v_middle_name[]"]').val() + ' ' + $(this).find('input[name="v_last_name[]"]').val();
                    // var booking_visitor_address = $(this).find('input[name="v_address[]"]').val() + ' ' + $(this).find('input[name="v_postal_code[]"]').val() + ' ' + $(this).find('input[name="v_city[]"]').val() + ' ' + $(this).find('select[name="gl_country"]').val();
                    var booking_visitor_birthdate = dof.toLocaleDateString("nl-NL", options) + ' | ' + $(this).find('select[name="v_nationality"] option:selected').text();

                    var extraRunners = `
                        <div class="row form-fields-rows">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <p class="summary-table-head-subs">Naam</p>
                                <span>${booking_visitor_title}</span>&nbsp;<span>${booking_visitor_name}</span><br>   
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <p class="summary-table-head-subs">Contactgegevens</p>
                                <div class="d-flex">
                                    <div class="mr-2">
                                       
                                    </div>
                                    <div class="address"><span></span><br></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <p class="summary-table-head-subs">Geboortedatum &amp; nationaliteit</p>
                                <span>${booking_visitor_birthdate}</span>
                            </div>                                                    
                        </div>
                    `;
                    $('#extra_runners').append(extraRunners);
                });

                // add the extra runner data to extrarunnersData array
                extraRunnersData = [];
                $(".extra_runners_info").each(function(){
                    extraRunnersData.push(
                        {
                            v_is_runner: $(this).find('input[name="v_is_runner[]"]').is(':checked'),
                            title: $(this).find('select[name="v_title[]"]').val(),
                            first_name: $(this).find('input[name="v_first_name[]"]').val(),
                            middle_name: $(this).find('input[name="v_middle_name[]"]').val(),
                            last_name: $(this).find('input[name="v_last_name[]"]').val(),
                            dob: $(this).find('input[name="v_dob[]"]').val(),
                            nationality: $(this).find('select[name="v_nationality"] option:selected').text()

                        }
                    )
                });
            }
        });



        $(document).on("click",".vervoer-radio-btn-group",function(){
            var transport_id = $(this).find('input').val();
            $('#transport_id').val(transport_id);
            if (transport_id == 0) {
                $('#flights_container').show();
            } else {
                $('#flights_container').hide();
            }
        });

        // Recalculate the total price when the number of travellers changes
        $(document).on("change",".flight-departure",function(){
            var travelers_amount = $('#travellers_amount').val(),
                departure_price = $(this).children('option:selected').data('price');
            $('#total_flight_departure_price').val(departure_price * travelers_amount);
            // getTotals();
        });

        // set number of runners in local storage
        // var runnersCount = $('#form_section2 input.traveller_is_runner:checked').length;
        // console.log('runnersCount', runnersCount);
        // localStorage.setItem('runners_count', 0);
        // $(document).on('click', '.traveller_is_runner', function() {
        //     var runners_count = $('#form_section2 input.traveller_is_runner:checked').length;
        //     console.log(runners_count);
        //     localStorage.setItem('runners_count', runners_count);

        //     // $( '#form_section3 input.bibs_count' ).data('max-qty', runners_count).prop('max', runners_count);
        // });

        // Recalculate the total price when the number of travellers changes
        // $(document).on("change",".flight-arrival",function(){
        //     var travelers_amount = $('#travellers_amount').val(),
        //         arrival_price = $(this).children('option:selected').data('price');
        //     $('#total_flight_arrival_price').val(arrival_price * travelers_amount);
            // getTotals();
        // });

        let bookingPricesArr = [
            {'bibs': 0}, {'rooms': 0}, {'extras': 0}, {'nonextras': 0}, 
            {'flight_departure': 0}, {'insurance': 0}, {'calamity_fund': 0}, {'sgr_fee': 0}
        ];
        var travelersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
        function updateBookingPrice(newValue, key) {
            for (var i = 0; i < bookingPricesArr.length; i++) {
                if (bookingPricesArr[i].hasOwnProperty(key)) {  // Check if the object has the 'bib' key
                    bookingPricesArr[i][key] = newValue;  // Update the value
                    break;  // Exit loop after updating to prevent unnecessary iterations
                }
            }
        }

        function sumBookingPrices(arr) {
            return arr.reduce((total, obj) => {
                // Loop through the keys in each object and add their values to the total
                for (let key in obj) {
                    if (obj.hasOwnProperty(key)) {
                        total += parseFloat(obj[key]);
                    }
                }
                return total;
            }, 0);
        }

        function sumSpecificKeys(arr, keysToSum) {
            return arr.reduce((total, obj) => {
                // Loop through the keys in each object and add their values if the key is in keysToSum
                for (let key in obj) {
                    if (obj.hasOwnProperty(key) && keysToSum.includes(key)) {
                        total += parseFloat(obj[key]);
                    }
                }
                return total;
            }, 0);
        }

        function updateStepPrice(selector) {
            let bookingBaseTotalPrice = 0;
            // Select all bibs_count inputs
            document.querySelectorAll(selector).forEach(input => {
                const count = parseInt(input.value);
                const price = parseFloat(input.getAttribute('data-price'));
                bookingBaseTotalPrice += count * price;
            });
            return bookingBaseTotalPrice;
        }

        function calculateDaysBetweenDates(endDate, startDate) {
            // Parse the date strings into Date objects
            const parsedEndDate = new Date(endDate);
            const parsedStartDate= new Date(startDate);

            // Calculate the difference in time between the two dates
            const differenceInTime = parsedEndDate - parsedStartDate;

            // Convert the difference in time to days
            const differenceInDays = differenceInTime / (1000 * 3600 * 24);

            return differenceInDays;
        }
        
        function bibsPrice() {
            var totalBibsPrice = 0;

            $(document).on('click', '.bib-count-minus, .bib-count-plus', function(){
                // var bibId = $(this).data('bib-id');
                // var bibCount = parseInt($('#bibs_count_' + bibId).val());
                // var bibPrice = parseFloat($('#bibs_count_' + bibId).data('price'));
                var runnersCount = $('#form_section2 input.traveller_is_runner:checked').length;
                if(parseInt(runnersCount) > 0){
                    updateBookingPrice(updateStepPrice('.bibs_count'), 'bibs');
                    let bookingSum = sumBookingPrices(bookingPricesArr);
                    $("#total_booking").html("€ "+formatPrice(bookingSum.toFixed(2)));
                    $('#summary_total_booking').html('€ '+formatPrice(bookingSum.toFixed(2)));
                }
            })
        }
        bibsPrice();

        // calculate hotel price
        function hotelPrice() {
            var totalRoomPrice = 0;

            $(document).on('click', '.room-count-minus, .room-count-plus', function(){
                if(parseInt(travelersCount) > 0){
                    var nights = calculateDaysBetweenDates($('#booking_end_date').val(), $('#booking_start_date').val());
                    var hotelTotalPrice = updateStepPrice('.rooms_count') * parseInt(nights);
                    updateBookingPrice(hotelTotalPrice, 'rooms');
                    let bookingSum = sumBookingPrices(bookingPricesArr);
                    $("#total_booking").html("€ "+formatPrice(bookingSum.toFixed(2)));
                    $('#summary_total_booking').html('€ '+formatPrice(bookingSum.toFixed(2)));
                }
            })
        }
        hotelPrice();

        // calculate non hotel extra
        function nonHotelExtraPrice() {
            var totalNonExtraPrice = 0;

            $(document).on('click', '.nonextra-count-minus, .nonextra-count-plus', function(){
                if(parseInt(travelersCount) > 0){
                    updateBookingPrice(updateStepPrice('.nonextra_count'), 'nonextras');
                    let bookingSum = sumBookingPrices(bookingPricesArr);
                    $("#total_booking").html("€ "+formatPrice(bookingSum.toFixed(2)));
                    $('#summary_total_booking').html('€ '+formatPrice(bookingSum.toFixed(2)));
                }
            })
        }
        nonHotelExtraPrice();

        // calculat hotel extra
        function hotelExtraPrice() {
            var totalExtraPrice = 0;

            $(document).on('click', '.extra-count-minus, .extra-count-plus', function(){
                if(parseInt(travelersCount) > 0){
                    updateBookingPrice(updateStepPrice('.extra_count'), 'extras');
                    let bookingSum = sumBookingPrices(bookingPricesArr);
                    $("#total_booking").html("€ "+formatPrice(bookingSum.toFixed(2)));
                    $('#summary_total_booking').html('€ '+formatPrice(bookingSum.toFixed(2)));
                }
            })
        }
        hotelExtraPrice();

        // calculate flight price
        function flightPrice() {
            var totalFlightPrice = 0;

            $(document).on('change', '.flight-departure', function(){
                if(parseInt(travelersCount) > 0){
                    var seatPrice = $(this).children('option:selected').data('price');
                    var travelers = parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) + parseInt($('#adults_count').val())
                    var flightPrice = parseFloat(seatPrice*travelers)
                    updateBookingPrice(flightPrice, 'flight_departure');
                    let bookingSum = sumBookingPrices(bookingPricesArr);
                    $("#total_booking").html("€ "+formatPrice(bookingSum.toFixed(2)));
                    $('#summary_total_booking').html('€ '+formatPrice(bookingSum.toFixed(2)));
                }
            })
        }
        flightPrice();

        // calculate sgr fee price
        function sgrFeePrice() {
            if(parseInt(travelersCount) > 0){
                var sgrFee = parseFloat($('#booking_sgr_fee_div').text());
                var travelers = parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) + parseInt($('#adults_count').val())
                var sgrFreeTotal = parseFloat(sgrFee*travelers);
                updateBookingPrice(sgrFreeTotal, 'sgr_fee');
                let bookingSum = sumBookingPrices(bookingPricesArr);
                $('#booking_sgr_fee_total').html(formatPrice(sgrFreeTotal));
                $("#total_booking").html("€ "+formatPrice(parseFloat(bookingSum).toFixed(2)));
                $('#summary_total_booking').html('€ '+formatPrice(parseFloat(bookingSum).toFixed(2)));
            }
        }

        // calculate the booking base total price (price without sgr fee, insurance and calamity fund)
        function bookingBaseTotalPrice(arr) {
            var keysToSum = ['bibs', 'rooms', 'extras', 'nonextras', 'flight_departure'];
            return parseFloat(sumSpecificKeys(bookingPricesArr, keysToSum)).toFixed(2);
        }

        // calculate the calamity fund,  calamity fund is EUR 2,50 per booking up to 9 persons. 
        // If there are more than 9 persons in a booking, it becomes twice EUR 2,50 up to 18 persons, etc.
        function calamityFundPrice() {
            const CALAMITY_FUND_PRICE = 2.50;
            const TRAVELERS_BASE_COUNT_CALAMITY = 9;
            var travelers = parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) + parseInt($('#adults_count').val())
            if(parseInt(travelersCount) > 0){
                if(travelers < TRAVELERS_BASE_COUNT_CALAMITY){
                    var calamityPrice = parseFloat(CALAMITY_FUND_PRICE);
                    updateBookingPrice(calamityPrice, 'calamity_fund');
                    $('#booking_calamity_fund_div').html(formatPrice(calamityPrice));
                    $('#booking_calamity_fund_total').html(formatPrice(calamityPrice));
                }else{
                    var calamityCount = Math.ceil(parseFloat(travelers/TRAVELERS_BASE_COUNT_CALAMITY));
                    var calamityPrice = parseFloat(CALAMITY_FUND_PRICE*calamityCount);
                    updateBookingPrice(calamityPrice, 'calamity_fund');
                    $('#booking_calamity_fund_div').html(formatPrice(CALAMITY_FUND_PRICE));
                    $('#booking_calamity_fund_total').html(formatPrice(calamityPrice));
                }
                let bookingSum = sumBookingPrices(bookingPricesArr);
                $("#total_booking").html("€ "+formatPrice(parseFloat(bookingSum).toFixed(2)));
                $('#summary_total_booking').html('€ '+formatPrice(parseFloat(bookingSum).toFixed(2)));
            }
        }
       
        // calculate insurance price
        var excludedInsNames = ["travelinsurance eu", 'travelinsurance non-eu', "cancellation insurance", "injury insurance"];
        function travelInsurance(insPrice, numberOfDays, travellersCount){
            const POLICECOST = parseFloat(3.50); // per booking
            
            var travelInsurance = (travellersCount * numberOfDays * insPrice) + POLICECOST;
            // var travelInsurance = parseFloat(travelInsuranceCalc+POLICECOST);

            return parseFloat(travelInsurance);
        }

        function cancellationInsurance(insPrice, bookingBasePrice){
            const INSURANCE_TAX  = parseFloat(21); // per booking
            const POLICECOST = parseFloat(4.24); // per booking

            var cancellation = parseFloat((insPrice * bookingBasePrice) / 100);
            var tax = parseFloat((INSURANCE_TAX * bookingBasePrice) / 100);
            var cancellationInsurance = parseFloat(cancellation + tax + POLICECOST);
            
            return parseFloat(cancellationInsurance);
        }

        function injuryInsurance(insPrice, bookingBasePrice){
            const INSURANCE_TAX  = parseFloat(21); // per booking
            const POLICECOST = parseFloat(4.24); // per booking
            
            var injury = (insPrice * bookingBasePrice) / 100;
            var tax = (INSURANCE_TAX * bookingBasePrice) / 100;
            var injuryInsurance = parseFloat(injury + tax + POLICECOST);
            
            return parseFloat(injuryInsurance);
        }

        function insurancePrice() {
            // $(document).on('click', '.insurance-checkbox', function(){
                var bookingBasePrice = parseFloat(bookingBaseTotalPrice(bookingPricesArr));
                var sgrFee = parseFloat($('#booking_sgr_fee_total').text());
                var insuranceBookingTotal = parseFloat(bookingBasePrice) + parseFloat(sgrFee);
                var travellersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                var numberOfDays = calculateDaysBetweenDates($('#booking_end_date').val(), $('#booking_start_date').val());

                if(parseInt(travelersCount) > 0 && bookingBasePrice > 0){
                    // price type1 = calculation based on total days
                    // price type2 = calculation based on % of booking base price
                    $(".insurance-options").each(function(){
                        var insName = $(this).data('name');
                        var insPrice = parseFloat($(this).data('price'));
                        var insId = $(this).attr('id');
                        var insType = $(this).data('price_type');
                        var insPerParticipant = $(this).data('per-participant');
                         
                        if(!excludedInsNames.includes(insName.toLowerCase())){
                            if(insType == 1){
                                var baseInsPrice = parseFloat(insPrice * numberOfDays);
                                if(insPerParticipant == 1){
                                    var totalInsPrice = baseInsPrice*travellersCount;
                                    $(`.insurance_id_${insId}`).html('€ '+totalInsPrice.toFixed(2).replace('.', ','));
                                }else{
                                    $(`.insurance_id_${insId}`).html('€ '+baseInsPrice.toFixed(2).replace('.', ','));
                                }
                            }else{
                                 var baseInsPrice = ((insPrice * bookingBasePrice) / 100);
                                if(insPerParticipant == 1){
                                    var totalInsPrice = baseInsPrice*travellersCount;
                                    $(`.insurance_id_${insId}`).html('€ '+totalInsPrice.toFixed(2).replace('.', ','));
                                }else{
                                    $(`.insurance_id_${insId}`).html('€ '+baseInsPrice.toFixed(2).replace('.', ','));
                                }
                            }
                        }else{
                           if(insName.toLowerCase() == "travelinsurance eu" || insName == "travelinsurance non-eu"){
                                // console.log('travelInsurance(insPrice)', travelInsurance(ins_price));
                                var travelIns = travelInsurance(insPrice, numberOfDays, travellersCount);
                                $(`.insurance_id_${insId}`).html('€ '+travelIns.toFixed(2).replace('.', ','));
                            }else if(insName.toLowerCase() == "cancellation insurance"){
                                // console.log('cancellationInsurance(insPrice, bookingBasePrice)', cancellationInsurance(ins_price, bookingBasePrice));
                                var cancalationIns = cancellationInsurance(insPrice, bookingBasePrice);
                                $(`.insurance_id_${insId}`).html('€ '+cancalationIns.toFixed(2).replace('.', ','));
                            }else if(insName.toLowerCase() == "injury insurance"){
                                // console.log('injuryInsurance(insPrice, bookingBasePrice)', injuryInsurance(ins_price, bookingBasePrice));
                                var injuryIns = injuryInsurance(insPrice, bookingBasePrice);
                                $(`.insurance_id_${insId}`).html('€ '+injuryIns.toFixed(2).replace('.', ','));
                            }
                        }
                    });
                    // var insurancePrice = parseFloat($('#booking_insurance_fee').val());
                    // updateBookingPrice(insurancePrice, 'insurance');
                    // let bookingSum = sumBookingPrices(bookingPricesArr);
                    // $("#total_booking").html("€ "+bookingSum.toFixed(2));
                    // $('#summary_total_booking').html('€ '+bookingSum.toFixed(2));
                    // console.log('bookingPricesArr', bookingPricesArr);
                }
            // })
        }
        $(document).on('click', '#transport_step_btn', function(){
            insurancePrice();
        })

        function calculateInsPrice(){
            let totalinsurancePrice = 0;
            $(document).on('click', '#form_section8 input.insurance-options', function(){
                // var insName = $(this).data('name');
                // var insPrice = parseFloat($(this).data('price'));
                var insId = $(this).attr('id');
                // var insType = $(this).data('price_type');
                // var insPerParticipant = $(this).data('per-participant');

                var insPrice = parseFloat($(`.insurance_id_${insId}`).text().replace('€ ', ''));
            
               
                // check if input checkbox is checked
                if($(this).is(':checked')){
                    totalinsurancePrice += parseFloat(insPrice);
                }else{
                    totalinsurancePrice -=  parseFloat(insPrice);
                }
                updateBookingPrice(parseFloat(totalinsurancePrice).toFixed(2), 'insurance');
                $('#total_insurance_price').val(totalinsurancePrice);
                $('#total_insurance').html(totalinsurancePrice.toFixed(2).replace('.', ','));

                let bookingSum = sumBookingPrices(bookingPricesArr);
                $("#total_booking").html("€ "+parseFloat(bookingSum).toFixed(2));
            })
        }
        calculateInsPrice();

        function addSelectedInsToSummary(){
            $( '#summary_insurance_div' ).html('');
            $( '#form_section8 input.insurance-options' ).each(function(){
                if($(this).is(':checked')){
                    var insName = $(this).data('name');
                    var insId = $(this).attr('id');
                    var insPrice = $(`.insurance_id_${insId}`).text();

                    $('#summary_insurance_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${insName}</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>${insPrice.replace('.', ',')}</p>
                        </div>
                    </div>`);
                }
            })
        }

        $(document).on('click', '#insurances_step_btn', function(){
            calamityFundPrice();
            sgrFeePrice();
            // add insurance price to booking total price
            // let bookingSum = sumBookingPrices(bookingPricesArr);
            // $("#total_booking").html("€ "+parseFloat(bookingSum).toFixed(2));
            // $('#summary_total_booking').html('€ '+parseFloat(bookingSum).toFixed(2));

            // break all the prices
            $("#insurance_summary").html(`€ ${parseFloat($('#total_insurance').text()).toFixed(2)}`);
            $("#calamity_summary").html(`€ ${parseFloat($('#booking_calamity_fund_total').text()).toFixed(2)}`);
            $("#sgrfee_summary").html(`€ ${parseFloat($('#booking_sgr_fee_total').text()).toFixed(2)}`);
            $("#booking_summary").html(`€ ${parseFloat(bookingBaseTotalPrice(bookingPricesArr)).toFixed(2)}`);

            addSelectedInsToSummary();

        })


        // function excludedIns(insName, insPrice, bookingBasePrice){
        //     // console.log('insName', insName);
        //     // console.log('insPrice', insPrice);
        //     // console.log('bookingBasePrice', bookingBasePrice);
        //     if(parseFloat(bookingBasePrice) == 0){
        //         return insPrice;
        //     }else{
        //         if(insName == "travelinsurance eu" || insName == "travelinsurance non-eu"){
        //             console.log('travelInsurance(insPrice)', travelInsurance(insPrice));
        //             return travelInsurance(insPrice);
        //         }else if(insName == "cancellation insurance"){
        //             console.log('cancellationInsurance(insPrice, bookingBasePrice)', cancellationInsurance(insPrice, bookingBasePrice));
        //             return cancellationInsurance(insPrice, bookingBasePrice);
        //         }else if(insName == "injury insurance"){
        //             console.log('injuryInsurance(insPrice, bookingBasePrice)', injuryInsurance(insPrice, bookingBasePrice));
        //             return injuryInsurance(insPrice, bookingBasePrice);
        //         }
        //     }
        // }

        function getTotals() {
            var booking_calamity_fund = parseFloat($('#booking_calamity_fund_div').text());
            var booking_insurance_fee_div = parseFloat($('#booking_insurance_fee_div').text());
            var sgr_fee = parseFloat($('#booking_sgr_fee_div').text());
            var adults_count = $("#adults_count").val();
            var children_count = $("#children_count").val();
            var children_under_3_count = $("#children_under_3_count").val();
            var total_travelers = parseInt(adults_count) + parseInt(children_count) + parseInt(children_under_3_count);
            var total_sgr_fee = sgr_fee * total_travelers;

            var total_booking = parseFloat($('#total_bibs_price').val()) + 
                parseFloat($('#total_room_price').val()) + 
                parseFloat($('#total_extra_price').val()) + 
                parseFloat($('#total_nonextra_price').val()) + 
                parseFloat($('#total_flight_departure_price').val()) + 
                parseFloat($('#total_flight_arrival_price').val()) +
                parseFloat($('#total_insurance_price').val());
                // parseFloat(total_sgr_fee) +
                // booking_calamity_fund +
                // booking_insurance_fee_div;

            $('#total_booking').html('&euro; ' + formatPrice(total_booking.toFixed(2)));
            $('#summary_total_booking').html('&euro; ' + formatPrice(total_booking.toFixed(2)));
            // display selected event(s) names
            // $('#form_section3 input.bibs_count' ).each(function(){
            //     $('#selected_events').html();
            //     $('#selected_events').html($('#summary_bibs_div').html());
            // });
        }

        // AJAX request to fetch countries list
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/countries-list/',
            type: 'GET',
			data: {
                locale: 'nl'
            },
            success: function(response) {

                // Check if response is valid
                if (response && response.type === 'success') {
                    var countries = response.data;

                    // Populate select boxes with countries 
                    $.each(countries, function(index, country) {
                        // Append options to each select box

                        $('select[name="gl_country"]').append('<option value="' + country.id + '">' + country.name + '</option>');
                        // $('select[name="v_nationality"]').append('<option value="' + country.id + '">' + country.name + '</option>');
                        $('select[name="gl_lives_in_country"]').append('<option value="' + country.id + '">' + country.name + '</option>');
                        $('select[name="sah_country"]').append('<option value="' + country.id + '">' + country.name + '</option>');
                    });
                } else {
                    console.error('Failed to fetch countries:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching countries:', error);
            }
        });

        // Make an AJAX request to the API endpoint for event settings
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/booking/event-settings',
            type: 'GET',
            dataType: 'json',
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
            success: function(response) {
                // Check if the response type is success
                if (response.type === 'success') {
                    // Access the event_settings object from the response data
                    var eventSettings = response.data.event_settings,
						eventDetails  = response.data.event_details;

					var booking_event  = eventDetails.name;
                    var booking_start_date = eventSettings.start_date;
                    var booking_end_date = eventSettings.end_date;
                    var booking_sgr_fee = eventSettings.sgr_fee;
                    var booking_insurance_fee = eventSettings.insurance_fee;
                    var booking_calamity_fund = eventSettings.calamity_fund;

                    var booking_date_info = booking_start_date + ' - ' + booking_end_date;
 
                    var start_date = $('#booking_start_date').val();
                    var end_date = $('#booking_end_date').val();

                    $("#booking_event").html(booking_event);
                    $("#booking_start_date").val(booking_start_date);
                    $("#booking_end_date").val(booking_end_date);

                    $("#booking_sgr_fee").val(booking_sgr_fee);
                    $("#booking_calamity_fund").val(booking_calamity_fund);
                    $("#booking_insurance_fee").val(booking_insurance_fee);

                    $("#booking_sgr_fee_div").html(formatPrice(booking_sgr_fee));
                    $("#booking_calamity_fund_div").html(booking_calamity_fund);
                    $("#booking_insurance_fee_div").html(booking_insurance_fee.replace('.', ','));

                    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' },
                        options_summary = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' },
                        start_date = new Date(booking_start_date),
                        end_date = new Date(booking_end_date);

                    var dateRange = start_date.toLocaleDateString("nl-NL", options) + ' - ' + end_date.toLocaleDateString("nl-NL", options);

                    $('#booking_date_info').text(dateRange);
                    $('#summary_departure_date').text(start_date.toLocaleDateString("nl-NL", options_summary));
                    $('#summary_arrival_date').text(end_date.toLocaleDateString("nl-NL", options_summary));

                    // Make an AJAX request to the API endpoint for event settings

                    getHotelDetails();

                } else {
                    // Handle the case when the response type is not success
                    console.error('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX Error: ' + error);
            }
        });

        // Make an AJAX request to the API endpoint for available bibs
        var bibs_html = '';
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/booking/available-bibs/',
            type: 'GET',
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
            success: function(response) {

                // Check if the response type is success
                if (response.type === 'success') {
                    // get travellers count
                    var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                    // Iterate over the data array in the response
                    $.each(response.data, function(index, item) {
                        // Generate the HTML content for each item
                        bibs_html += `
                            <div class="col-md-4 col-lg-4 col-sm-4 bibs-item">
                                <label class="hotel-labels">
                                    <div class="card card-default card-input tickets" style="cursor:default;">
                                    <div class="card-header hotels-details-header">
                                        <div class="card-title">${item.challenge_name}</div>
                                        <div class="card-title-icon">
                                        <svg id="distance" xmlns="http://www.w3.org/2000/svg" width="15.534" height="15.533" viewBox="0 0 15.534 15.533">
                                            <g id="Group_234" data-name="Group 234" transform="translate(10.679)">
                                            <g id="Group_233" data-name="Group 233">
                                                <path id="Path_166" data-name="Path 166" d="M356.369,0h-3.883A.486.486,0,0,0,352,.485V5.34a.485.485,0,1,0,.971,0V3.883h3.4a.486.486,0,0,0,.485-.485V.485A.486.486,0,0,0,356.369,0Z" transform="translate(-352)" fill="#0093cb" />
                                            </g>
                                            </g>
                                            <g id="Group_236" data-name="Group 236" transform="translate(1.942 6.796)">
                                            <g id="Group_235" data-name="Group 235">
                                                <path id="Path_167" data-name="Path 167" d="M75.65,227.883H70.8a.971.971,0,0,1,0-1.942h1.06a1.456,1.456,0,1,0,0-.971H70.8a1.942,1.942,0,1,0,0,3.883H75.65a.971.971,0,1,1,0,1.942H66.823a1.456,1.456,0,1,0,0,.971H75.65a1.942,1.942,0,0,0,0-3.883Z" transform="translate(-64 -224)" fill="#0093cb" />
                                            </g>
                                            </g>
                                            <g id="Group_238" data-name="Group 238" transform="translate(0 2.913)">
                                            <g id="Group_237" data-name="Group 237">
                                                <path id="Path_168" data-name="Path 168" d="M3.4,96A3.4,3.4,0,0,0,0,99.4c0,1.744,2.726,4.832,3.037,5.178a.485.485,0,0,0,.722,0C4.07,104.23,6.8,101.142,6.8,99.4A3.4,3.4,0,0,0,3.4,96Zm0,4.854A1.456,1.456,0,1,1,4.854,99.4,1.457,1.457,0,0,1,3.4,100.854Z" transform="translate(0 -96)" fill="#0093cb" />
                                            </g>
                                            </g>
                                        </svg>
                                        <div class="col race-distance-txt">${item.running_distance}km</div>
                                        </div>
                                    </div>
                                    <div style='background-color: #f0f4f7;padding: 0 10px;'>${formatDate(item.event_date)}</div>
                                    </div>
                                </label>
                                <div class="card-title"> &euro; ${formatPrice(item.single_ticket_price)} </div>
                                <div class="input-group plus-minus-input">
                                    <div class="input-group-button">
                                    <span class="button hollow circle value-button-room minus_room bib-count-minus" data-bib-id="${item.id}" data-bibs-max="${travelers}" data-quantity="minus" data-field="quantity">
                                        <i class="fa fa-minus" aria-hidden="true"></i>
                                    </span>
                                    </div>
                                    <input type="hidden" name="bibs_id[${item.id}]" id="bibs_id_${item.id}" value="${item.id}">
                                    <input placeholder="Bib" class="input-group-field bibs_count number" type="number" name="bibs_count[${item.id}]" id="bibs_count_${item.id}" data-bib-id="${item.id}" value="0" required data-event-date="${item.event_date}" data-max-qty="${item.quantity}" data-bibs_name="${item.challenge_name}" data-price="${item.single_ticket_price}" data-bibs_count="0">
                                    <div class="input-group-button">
                                    <span class="button hollow circle value-button-room plus_room bib-count-plus" data-bib-id="${item.id}" data-quantity="plus" data-bibs-max="${travelers}" data-field="quantity">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </span>
                                    </div>
                                </div>
                            </div>                            
                        `;
                    });
                    $('#bibs_div').html(bibs_html);

                } else {
                    // Handle the case when the response type is not success
                    console.error('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX Error: ' + error);
            }
        });

        function getHotelDetails() {
            // Make an AJAX request to the API endpoint
            var start_date = $('#booking_start_date').val(),
                end_date = $('#booking_end_date').val();

            $.ajax({
                url: '<?php echo $data->api_endpoint; ?>/booking/available-hotels',
                type: 'GET',
                data: {
                    start_date: start_date,
                    end_date: end_date
                },
                headers: {
                    'e-key': '<?php echo $eventkey; ?>',
                    'merchant-key': '<?php echo $data->merchant_key; ?>'
                },
                success: function(response) {
                    var purchased_hotels = response.purchased_hotels;
                    var image_path = "<?php echo $data->img_endpoint; ?>/uploads/hotel/";
                    var message = response.message;
                    // Check if the response type is success
                    if (response.type === 'success') {
                        // Iterate over the data array in the response
                        var hotels_html = '';
                        $.each(response.data, function(index, item) {
                            var hotelName = item.name.length > 27 ? item.name.substring(0, 27) + '...' : item.name;
                            // Generate the HTML content for each item
                            hotels_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards">
                                <label class="hotel-labels">
                                    <input type="radio" class="card-input-element" name="hotel_id_OLD" value="${item.id}" data-hotel_name="${item.name}" data-rating="${item.rating}" data-photo="${item.photo_1}" data-max_persons_per_room="${item.max_persons_per_room}" data-price_from="${item.price_from}" />
                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header" style="height: 75px;">
                                            <div class="card-title">${hotelName}</div>
                                            <div class="card-title-icon">

                                                <svg height="40" width="40" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve">
                                                    <g>
                                                        <g>
                                                            <g>
                                                                <path d="M138.667,341.333h-64C68.779,341.333,64,346.112,64,352v64c0,5.888,4.779,10.667,10.667,10.667h64 c5.888,0,10.667-4.779,10.667-10.667v-64C149.333,346.112,144.555,341.333,138.667,341.333z M128,405.333H85.333v-42.667H128 V405.333z" fill="#0093cb"/>
                                                                <path d="M373.333,320h64c5.888,0,10.667-4.779,10.667-10.667v-64c0-5.888-4.779-10.667-10.667-10.667h-64 c-5.888,0-10.667,4.779-10.667,10.667v64C362.667,315.221,367.445,320,373.333,320z M384,256h42.667v42.667H384V256z" fill="#0093cb"/>
                                                                <path d="M373.333,213.333h64c5.888,0,10.667-4.779,10.667-10.667v-64c0-5.888-4.779-10.667-10.667-10.667h-64 c-5.888,0-10.667,4.779-10.667,10.667v64C362.667,208.555,367.445,213.333,373.333,213.333z M384,149.333h42.667V192H384V149.333 z" fill="#0093cb" />
                                                                <path d="M373.333,426.667h64c5.888,0,10.667-4.779,10.667-10.667v-64c0-5.888-4.779-10.667-10.667-10.667h-64 c-5.888,0-10.667,4.779-10.667,10.667v64C362.667,421.888,367.445,426.667,373.333,426.667z M384,362.667h42.667v42.667H384 V362.667z" fill="#0093cb"/>
                                                                <path d="M501.333,106.667c5.888,0,10.667-4.779,10.667-10.667V53.333c0-5.888-4.779-10.667-10.667-10.667H448v-32 C448,4.779,443.221,0,437.333,0H74.667C68.779,0,64,4.779,64,10.667v32H10.667C4.779,42.667,0,47.445,0,53.333V96 c0,5.888,4.779,10.667,10.667,10.667h10.667V448H10.667C4.779,448,0,452.779,0,458.667v42.667C0,507.221,4.779,512,10.667,512 h490.667c5.888,0,10.667-4.779,10.667-10.667v-42.667c0-5.888-4.779-10.667-10.667-10.667h-10.667V106.667H501.333z M85.333,21.333h341.333v21.333H85.333V21.333z M490.667,490.667H21.333v-21.333h469.333V490.667z M288,341.333h-64 c-5.888,0-10.667,4.779-10.667,10.667v96H42.667V106.667h426.667V448H298.667v-96C298.667,346.112,293.888,341.333,288,341.333z M277.333,362.667V448h-42.667v-85.333H277.333z M21.333,85.333V64h469.333v21.333H21.333z" fill="#0093cb"/>
                                                                <path d="M298.667,245.333c0-5.888-4.779-10.667-10.667-10.667h-64c-5.888,0-10.667,4.779-10.667,10.667v64 c0,5.888,4.779,10.667,10.667,10.667h64c5.888,0,10.667-4.779,10.667-10.667V245.333z M277.333,298.667h-42.667V256h42.667 V298.667z" fill="#0093cb"/>
                                                                <path d="M224,213.333h64c5.888,0,10.667-4.779,10.667-10.667v-64c0-5.888-4.779-10.667-10.667-10.667h-64 c-5.888,0-10.667,4.779-10.667,10.667v64C213.333,208.555,218.112,213.333,224,213.333z M234.667,149.333h42.667V192h-42.667 V149.333z" fill="#0093cb" />
                                                                <path d="M138.667,128h-64C68.779,128,64,132.779,64,138.667v64c0,5.888,4.779,10.667,10.667,10.667h64 c5.888,0,10.667-4.779,10.667-10.667v-64C149.333,132.779,144.555,128,138.667,128z M128,192H85.333v-42.667H128V192z" fill="#0093cb"/>
                                                                <path d="M138.667,234.667h-64c-5.888,0-10.667,4.779-10.667,10.667v64C64,315.221,68.779,320,74.667,320h64 c5.888,0,10.667-4.779,10.667-10.667v-64C149.333,239.445,144.555,234.667,138.667,234.667z M128,298.667H85.333V256H128V298.667 z" fill="#0093cb" />
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>

                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">Max. ${item.max_persons_per_room} personen per kamer</div>
                                            <div class="card-body-price-heading">
                                                <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                                <div class="card-body-price">&#8364; ${formatPrice(item.price_from)}</div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>`;
                        });

                        // Append the generated HTML to the container
                        $('#hotels_container').append(hotels_html);

                    } else {
                        // Handle the case when the response type is not success
                        console.error('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    console.error('AJAX Error: ' + error);
                }
            });
        }

        $(document).on('click', '.card-input-element', function() {
            var hotel_id = $(this).val();

            getHotelRoomDetails(hotel_id);
        });

        function getHotelRoomDetails(hotel_id = 0) {
            var hotel_rooms_html 	= "";

            // Make an AJAX request to the API endpoint
            var start_date 	 = $('#booking_start_date').val(),
                end_date 	 = $('#booking_end_date').val(),
                adults_count = $('#adults_count').val();

            $.ajax({
                url: '<?php echo $data->api_endpoint; ?>/booking/available-hotel-rooms',
                type: 'GET',
                data: {
                    start_date: start_date,
                    end_date: end_date,
                    hotel_id: hotel_id,
                    adults: adults_count,
                    booking_id: 1  // ????
                },
                headers: {
                    'e-key': '<?php echo $eventkey;?>',
                    'merchant-key': '<?php echo $data->merchant_key; ?>'
                },
                success: function(response) {
                    var message = response.message;
                    // Check if the response type is success
                    if (response.type === 'success') {
                        var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                        // Iterate over the data array in the response
                        $.each(response.data, function(index, item) {
                            var hotelName = item.name.length > 27 ? item.name.substring(0, 27) + '...' : item.name;
                            // Generate the HTML content for each item
                            hotel_rooms_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards-rooms  booking-card">

                                <label class="hotel-rooms-labels">
                                    

                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header" style="height: 75px;">
                                            <div class="card-title room-type-name">${hotelName}</div>
                                            <div class="card-title-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="40" width="40" viewBox="0 0 60 43.548">
                                                    <path id="hotel" d="M56.614,43.009l-1.935-1.631V30.231h1.935Zm5.308-16.064-3.15-7.36a.975.975,0,0,0-.89-.585H53.411a.963.963,0,0,0-.885.585l-3.15,7.36a.969.969,0,0,0,.89,1.35H61.032a.969.969,0,0,0,.89-1.35ZM16.661,38.161V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035H32.14V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035h4.984V33.1a5.344,5.344,0,0,0-5.337-5.337h-26.1A5.344,5.344,0,0,0,11.677,33.1v5.061ZM54.5,46.29h3.01l-.9-.755-1.935-1.626L50.14,40.1H9.989L2.615,46.29ZM6.355,59.3H4.419V61.58a.971.971,0,0,0,.968.968H10.1a.971.971,0,0,0,.968-.968V59.3ZM51,59.3H49.061V61.58a.971.971,0,0,0,.968.968h4.713a.971.971,0,0,0,.968-.968V59.3ZM3.935,48.226H2V56.4a.971.971,0,0,0,.968.968H57.161a.971.971,0,0,0,.968-.968V48.226Z" transform="translate(-2 -19)" fill="#0093cb" />
                                                </svg>

                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price room-type-price">&#8364; ${formatPrice(item.price)}</div>
                                            </div>
                                            <div class="card-body-hotel-room-qty-sel rooms-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow room-count-minus circle value-button-room minus_room" data-bib-id="${item.id}" data-quantity="minus" data-hotels-max="${travelers}" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="hotel_rooms[]" value="${item.id}">
                                                    <input placeholder="Hotelroom" class="input-group-field rooms_count number hotel-room-count" type="number" id="rooms_count_${item.hotel_room_id}" hotel-id="${hotel_id}" name="${item.hotel_room_id}" value="0" required data-room_name="${item.name}" data-quantity="${item.quantity}" data-price="${item.price}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow room-count-plus circle value-button-room plus_room" data-bib-id="${item.id}" data-quantity="plus" data-hotels-max="${travelers}" data-field="quantity">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                        </div>
                                    </div>

                                </label>

                            </div>`;
                        });

                        // Append the generated HTML to the container
                        $('#hotel_rooms_container').html(hotel_rooms_html);

                    } else {
                        // Handle the case when the response type is not success
                        console.error('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX errors
                    console.error('AJAX Error: ' + error);
                }
            });
        }

        // Make an AJAX request to the API endpoint for available extras
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/booking/available-extras',
            type: 'GET',
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
            success: function(response) {
                var purchased_extras = response.purchased_extras;
                var message = response.message;
                // Check if the response type is success
                if (response.type === 'success') {
                    var hotel_extras_html ="", non_hotel_extras_html = '';
                    var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                    // Iterate over the data array in the response
                    $.each(response.data, function(index, item) {

                        var extras_description = item.extras_description ?? '-';
                        var hotelName = item.name.length > 27 ? item.name.substring(0, 27) + '...' : item.name;

                        if(item.related_product_category == 1){ //1 = hotel extras
                            
                            hotel_extras_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards-rooms booking-card">
                                <label class="hotel-rooms-labels">
                                    <input type="radio" name="product" selected checked class="card-input-element" />
                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header" style="height: 75px;">
                                            <div class="card-title">${hotelName}</div>											
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">${extras_description}</div>
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price">&#8364; ${formatPrice(item.price)}</div>
                                            </div> 
                                            <div class="card-body-hotel-room-qty-sel hotelextra-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow circle value-button-room minus_room extra-count-minus" data-hotel_extras_id="${item.id}" data-quantity="minus" data-hotel-extras-max="${travelers}" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="hotel_extras[]" value="${item.id}">
                                                    <input placeholder="Extra hotel" class="input-group-field extra_count number" type="number" name="extras[${item.id}]" value="0" required data-extras_name="${item.name}" data-price="${item.price}" data-related_product_category="${item.related_product_category}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow circle value-button-room plus_room extra-count-plus" data-hotel_extras_id="${item.id}" data-quantity="plus" data-hotel-extras-max="${travelers}" data-field="quantity">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </span>											
                                                    </div>
                                                </div>	
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            `;
                        }else if(item.related_product_category == 2){ //2 = non hotel extras
                            non_hotel_extras_html 		+= `
                            <div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards-rooms booking-card">
                                <label class="hotel-rooms-labels">
                                    <input type="radio" name="product" selected checked class="card-input-element" />
                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header" style="height: 75px;">
                                            <div class="card-title">${hotelName}</div>											
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">${extras_description}</div>
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price">&#8364; ${formatPrice(item.price)}</div>
                                            </div> 
                                            <div class="card-body-hotel-room-qty-sel nonhotelextra-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow circle value-button-room minus_room nonextra-count-minus" data-hotel_extras_id="${item.id}" data-quantity="minus" data-non-hotel-extras-max="${travelers}" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="nonhotel_extras[]" value="${item.id}">
                                                    <input placeholder="Extra non hotel" class="input-group-field nonextra_count number" type="number" data-nonextra-id="${item.id}" name="extras[${item.id}]" value="0" data-extras_name="${item.name}" data-total_quantity="${item.total_quantity}" data-total_quantity="${item.total_quantity}" data-price="${item.price}" data-related_product_category="${item.related_product_category}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow circle value-button-room  plus_room nonextra-count-plus" data-hotel_extras_id="${item.id}" data-quantity="plus" data-non-hotel-extras-max="${travelers}"="${travelers}" data-field="quantity">
                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                        </span>											
                                                    </div>
                                                </div>	

                                        </div>
                                    </div>
                                </label>
                            </div>
                            `; 
                        }

                    });
                    
                    $('#hotel_extras_details_div').html(hotel_extras_html);
                    if($('#hotel_extras_details_div').html() === ''){
                        $("#hotel_extra_title").hide();
                    }else{
                        $("#hotel_extra_title").show();
                    }
                    $('#non_hotel_extras_details_div').html(non_hotel_extras_html);

                } else {
                    // Handle the case when the response type is not success
                    console.error('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX Error: ' + error);
            }
        });
   
        // Make an AJAX request to the API endpoint for available flights
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/booking/available-flights',
            type: 'GET',
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
            success: function(response) {
                // Check if the response type is success
                if (response.type === 'success') {
                    // Access the flight data from the response
                    var flightsData = response.data;

                    // Process all flight data
                    var allFlightPlanHtml = '';

                    $('#flights_container').html('');

                    $.each(flightsData, function(index, j) {
                        // Access flight plan details
     
                        // Process each flight info 
                        var flightInfoHtml 				= "";
                        var returnflightInfoHtml 		= "";
                        var travelClass = "";
                        $.each(j.flight_info, function(index, item) {
                            // Access flight info details
                            var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
                            var flight_departure_date = new Date(item.departure_date),
                                flight_arrival_date = new Date(item.arrival_date);
              
                            if(item.route == 'D') {  //departure
            
                                // Construct HTML for flight info
                                flightInfoHtml += `<div class="flight-details-box-row" id="go_flight_details" data-flight-id="${item.flight_id}" data-flight-plan-id="${item.flight_plan_id}">
                                    <div class="col-flight-depart-details">
                                        <div class="departure-flight-airport">${item.flight_number} --> ${item.departure_port}</div>
                                        <div class="departure-flight-depart">Vertrek</div>
                                        <div class="departure-flight-date">${flight_departure_date.toLocaleDateString("nl-NL", options)}</div>
                                        <div class="departure-flight-time">${item.departure_time}</div>
                                    </div>
                                    <div class="col-flight-arrival-details">
                                        <div class="departure-flight-airport">${item.flight_number} --> ${item.arrival_port}</div>
                                        <div class="arrival-flight-depart">Aankomst</div>
                                        <div class="arrival-flight-date">${flight_arrival_date.toLocaleDateString("nl-NL", options)}</div>
                                        <div class="arrival-flight-time">${item.arrival_time}</div>
                                    </div>
                                    
                                </div>`;
                                travelClass += `
                                    <div class="flight-seat-select-box">
                                        <h5 class="heenvlucht-details">Reisklasse</h5>
                                        <select placeholder="Reisklasse" data-placeholder="Reisklasse" name="flight-seat-flight-one" id="flight-seat-flight-one" class="form-select flight-departure departure-select">
                                            <option value="" data-price="0.00" disabled selected>Reisklasse</option>                                            
                                            <option value="eco" data-price="${item.economy_ticket_price}">Economy class - &#8364; ${formatPrice(item.economy_ticket_price)}</option>
                                            <option value="com" data-price="${item.comfort_ticket_price}">Comfort class - &#8364; ${formatPrice(item.comfort_ticket_price)}</option>
                                            <option value="bus" data-price="${item.business_ticket_price}">Business class - &#8364; ${formatPrice(item.business_ticket_price)}</option>
                                        </select>
    
                                    </div>
                                `;
            
                            }else if(item.route == 'H') {   //arrival
            
                                // Construct HTML for flight info
                                returnflightInfoHtml += `<div class="flight-details-box-row" id="return_flight_details" data-flight-id="${item.flight_id}">
                                    <div class="col-flight-depart-details">
                                        <div class="departure-flight-airport">${item.flight_number} --> ${item.departure_port}</div>
                                        <div class="departure-flight-depart">Vertrek</div>
                                        <div class="departure-flight-date">${flight_departure_date.toLocaleDateString("nl-NL", options)}</div>
                                        <div class="departure-flight-time">${item.departure_time}</div>
                                    </div>
                                    <div class="col-flight-arrival-details">
                                        <div class="arrival-flight-airport">${item.flight_number} --> ${item.arrival_port}</div>
                                        <div class="arrival-flight-depart">Aankomst</div>
                                        <div class="arrival-flight-date">${flight_arrival_date.toLocaleDateString("nl-NL", options)}</div>
                                        <div class="arrival-flight-time">${item.arrival_time}</div>
                                    </div>
                                    
                                </div>`
    
                            }
            
                        });
            
                        // Construct HTML for flight plan
                        var flightPlanHtml = `
                        <div class="col vervoer-radio-btn-group">
                            <label class="vervoer-radio-btn-label">
                                <input type="radio" placeholder="flightlist" name="flightplan_list" value="${j.fp_id}" data-flight-id="${j.fp_id}" data-planname="${j.plan_name}" data-price="${j.price}" data-standard_luggage_weight="${j.standard_luggage_weight}" data-customer_pays_for_hand_luggage="${j.customer_pays_for_hand_luggage}" data-hand_luggage_price="${j.hand_luggage_price}" data-customer_book_extra_luggage="${j.customer_book_extra_luggage}" data-extra_luggage_weight="${j.extra_luggage_weight}" data-extra_luggage_price="${j.extra_luggage_price}" class="card-input-element" />
                                <div class="card card-default card-input">
                                    <div class="card-header">
                                        <div class="card-title">${j.plan_name}</div>
                                        <div class="card-title-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="40" width="40" viewBox="0 0 24 24" fill="none">
                                                <path d="M21 16V14L13 9V3.5C13 2.67 12.33 2 11.5 2C10.67 2 10 2.67 10 3.5V9L2 14V16L10 13.5V19L8 20.5V22L11.5 21L15 22V20.5L13 19V13.5L21 16Z" fill="#0093cb"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body flight-deets-cards">
                                        <div class="heenvlucht-details">
                                            <h5>Heenvlucht</h5>
                                            ` + flightInfoHtml + `
                                        </div>
                                        
                                        <div class="heenvlucht-details">
                                            <h5>Retourvlucht</h5>
                                            ` + returnflightInfoHtml + `
                                        </div>
                                        `+travelClass+`
                                        
                                    </div>
                                </div>
                            </label>
                        </div>
                        `;
            
                        // Append flight plan HTML to allFlightPlanHtml
                        allFlightPlanHtml += flightPlanHtml;
                    });
            
                    // Append all flight plan HTML to container
                    $('#flights_container').append(allFlightPlanHtml);
                    $('.form-select').select2({
                        // dropdownParent: $('.flight-seat-select-box')
                        // dropdownParent: $(this).parent()
                    });

                } else {
                    // Handle the case when the response type is not success
                    console.error('Error: ' + response.message);
                }

            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX Error: ' + error);
            }
        });

        // handle insurances who depend on other insurances
        function disable_dependant_insurance(){
            $('.insurance-options').each(function(){
                var dependOnId = $(this).data('depend-on');
                if(dependOnId > 0){
                    console.log('dependOnId', dependOnId);
                    // current insurance depends on another insurance
                    // prevent user from checking current insurance if dependOnId is not checked
                    $(this).prop('disabled', true);
                    var dependOnElement = $('#'+dependOnId);
                    if(dependOnElement.is(':checked')){
                        $(this).prop('checked', true);
                    }else{
                        $(this).prop('checked', false);
                    }
                }
            })
        }

        function handle_dependent_insurance(){
            $(document).on('click', '#form_section8 input.insurance-options', function(){
                var clickedInsId = $(this).attr('id');
                var clickedIns = $(this);
                var insurancesParent = $('#insurances_container').children();
                var insuranceTotal = parseFloat($('#total_insurance').text());
                $(insurancesParent).each(function(){
                    var insurances = $(this).find('input');
                    if(insurances.data('depend-on') > 0){
                        var dependentId = insurances.data('depend-on');
                        var insuranceId = insurances.attr('id');
                        if(dependentId == clickedInsId){
                            if(clickedIns.is(':checked')){
                                insurances.removeAttr('disabled');
                            }else{
                                insurances.prop('checked', false);
                                insurances.prop('disabled', true);
                                // calculateInsPrice();
                                var insurancePrice = parseFloat(insurances.data('price'));
                                console.log('insurancePrice', insurancePrice);
                                console.log('insuranceTotal', insuranceTotal);
                                insuranceTotal -= insurancePrice;
                                console.log('insuranceTotal', insuranceTotal);
                                $('#total_insurance').html(insuranceTotal.toFixed(2));
                                updateBookingPrice(parseFloat(insuranceTotal).toFixed(2), 'insurance');
                                // check if the dependent insurance has another insurance that depends on it
                                var recursiveDependent = $(`input[data-depend-on="${insuranceId}"]`);
                                if(recursiveDependent.length > 0){
                                    // handle_dependent_insurance();
                                    recursiveDependent.prop('checked', false);
                                    recursiveDependent.prop('disabled', true);

                                    var recursiveDependentPrice = parseFloat(recursiveDependent.data('price'));
                                    console.log('recursiveDependentPrice', recursiveDependentPrice);
                                    console.log('insuranceTotal', insuranceTotal);
                                    insuranceTotal -= recursiveDependentPrice;
                                    console.log('insuranceTotal2', insuranceTotal);
                                    $('#total_insurance').html(insuranceTotal.toFixed(2));
                                    updateBookingPrice(parseFloat(insuranceTotal).toFixed(2), 'insurance');
                                }
                            }
                        }
                    }
                })
            })
        }
        // handle_dependent_insurance();

        // Make an AJAX request to the API endpoint for available insurance
		$.ajax({
			url: '<?php echo $data->api_endpoint; ?>/booking/available-insurance',
			type: 'GET',
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
			success: function(response) {
				// Check if the response type is success
				if (response.type === 'success') {
					// Access the insurance data from the response 
					var insurancesData 		= response.data;
					var purchased_insurance = response.purchased_insurance;

					// Process all insurance data
                    var insuranceHtml = '',
                        total_insurance = 0;

                    $('#summary_insurance_div').html('');

                    // Process each insurance plan
                    $.each(insurancesData, function(index, item) {
                        // Access insurance details
            
                        var this_ins_price = 0,
                            total_booking_price = 0,
                            ins_price = item.price,
                            ins_price_type = item.price_type,
                            ins_price_per_participant = item.price_per_participant,
                            adults_count = $("#adults_count").val(),
                            children_count = $("#children_count").val(),
                            children_under_3 = $("#children_under_3_count").val();
                            booking_start_date = $("#booking_start_date").val(),
                            booking_end_date = $("#booking_end_date").val();
        
                        let start_date = new Date(booking_start_date),
                            end_date = new Date(booking_end_date);
        
                        // Calculating the time difference of two dates
                        let date_diff_time = end_date.getTime() - start_date.getTime();
                        
                        // Calculating the no. of days between two dates
                        var nights = Math.round(date_diff_time / (1000 * 3600 * 24));

                        total_booking_price = 
                            parseFloat($("#total_bibs_price").val()) +
                            parseFloat($("#total_extra_price").val()) +
                            parseFloat($("#total_nonextra_price").val()) +
                            parseFloat($("#total_room_price").val()) +
                            parseFloat($("#total_flight_departure_price").val()) +
                            parseFloat($("#total_flight_arrival_price").val());

                        var  isPriceHidden = '';
                        this_ins_price = calculateInsurancePrice(adults_count, children_count, children_under_3, nights, ins_price, ins_price_type, ins_price_per_participant, total_booking_price);

                        var isOptionChecked	= '';
                        // if(parseInt(item.default_ticked) == 1){
                        //     isOptionChecked	= ' checked="checked"';
                        //     isPriceHidden =  '';
                        //     total_insurance += parseFloat(item.price);

                        //     $('#summary_insurance_div').append(`<div class="row form-fields-rows">
                        //         <div class="col-md-6 col-lg-4 col-xl-4">
                        //             <p>${item.insurance_name}</p>
                        //         </div>
                        //         <div class="col-md-6 col-lg-8 col-xl-8">
                        //             <p>${this_ins_price}</p>
                        //         </div>
                        //     </div>`);


                        // } else {
                        //     isPriceHidden =  'style="display:none;"';
                        // }
                        // Construct HTML for flight plan
                        insuranceHtml += `<tr class="type-verzekering-body">
                            <td class="type-verzekering-col">
                                <div class="insurance-options-check">
                                    <input id="${item.insurance_id}" class="insurance-options form-input-checkbox" type="checkbox" name="insurance_id[${index + 1}]" data-insurance-id="${index + 1}" data-name="${item.insurance_name}" ${isOptionChecked} data-price="${item.price}" 
                                    data-price_type="${item.price_type}" data-price_per_participant="${item.price_per_participant}" data-depend-on="${item.depend_on_insurance_id}" value="1" />
                                    <label for="extra-bagage" class="insurancetype-check-label">${item.insurance_name}</label>
                                </div>
                                <div class="verzekering-description-box">
                                    <p>${item.information}</p>
                                </div>
                            </td>
                            <td class="verzekering-optie-col">
                                <span id="insurance_option${index + 1}" class="insurance_option insurance_id_${item.insurance_id}" ${isPriceHidden}>&euro; ${item.price.replace('.', ',')}</span>
                            </td>
                        </tr>`;
                    });
 
                    // Append all insurance HTML to container
                    $('#insurances_container').html(insuranceHtml);
                    $('#total_insurance_price').val(total_insurance.toFixed(2).replace('.', ','));
                    $('#total_insurance').html(total_insurance.toFixed(2).replace('.', ','));

                    // disable_dependant_insurance();
				} else {
					// Handle the case when the response type is not success
					console.error('Error: ' + response.message);
				}
			},
			error: function(xhr, status, error) {
				// Handle AJAX errors
				console.error('AJAX Error: ' + error);
			}
		});

        function mailBookingData(bookingData, checkoutUrl){
            var url = "<?php echo admin_url('admin-ajax.php'); ?>";
            $.ajax({
                method: "POST",
                dataType: "json",
                url: url,
                data: { action: 'mail_booking_details', bookingData: bookingData },
                success: function(data) {
                    var result = JSON.parse(data);
                    // redirect to checkout page
                    // window.location.href = checkoutUrl;
                    alert('Mail sent successfully!');
                    console.log('boekingsgegevens succesvol gemaild!');
                },
                error: function(xhr, status, error) {
                    if(xhr.status == 200)
                        // redirect to checkout page
                        window.location.href = checkoutUrl;
                    else
                        // redirect to checkout page
                        window.location.href = checkoutUrl;
                    console.error('Fout bij het verzenden van e-mail:', error);
                }
            });
        }

        var hotelRooms = [];
        // var hotelRoomCount = [];
        // $('input[type="number"].rooms_count').each(function() {
        //     hotelRoomCount.push(parseInt($(this).val()) || 0);
        // });
        // // var hotelRoomCountSum = hotelRoomCount.reduce((total, num) => total + num, 0);
        // var hotelRoomCountSum = 0;
        // var travelers = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
        // function appendSelectedRoom(el) {
        //     // hotelRooms = [];
        //     var parentDiv = el.closest('.input-group-button');
        //     var roomInput = parentDiv.siblings('input.rooms_count');
        //     var roomCountValue = roomInput.val();
        //     var roomId = roomInput.attr('name');
        //     hotelRoomCountSum += parseInt(roomCountValue);
        //     console.log('roomCountValue', roomCountValue);
        //     console.log('travelers', travelers);
        //     console.log('hotelRoomCountSum', hotelRoomCountSum);
        //     if(roomCountValue == 0 && hotelRoomCountSum <= travelers){
        //         alert('append');
        //         hotelRooms.push({
        //             room_id: roomId,
        //             room_count: roomCountValue
        //         });
        //     }else{
        //         alert('not append');
        //     }
        // }

        $(document).on('click', '.hotel-btn-form-step', function(e){
            var parentDiv = $(this).closest('.card-body');
            var hotelForm = parentDiv.find('#hotel_form');
            var hotelRoomInput = hotelForm.find('input.rooms_count');
            hotelRoomInput.each(function() {
                if($(this).val() > 0){
                    var roomCountValue = $(this).val();
                    var roomId = $(this).attr('name');
                    var hotelId = $(this).attr('hotel-id');
                    hotelRooms.push({
                        room_id: roomId,
                        room_count: roomCountValue,
                        hotel_id: hotelId
                    });
                }
            });
        })

        // $(document).on('click', '.room-count-minus', function(e){
        //     appendSelectedRoom($(this));
        // })

        // $(document).on('click', '.room-count-plus', function(e){
        //     appendSelectedRoom($(this));
        // })

        var nonextrasData = [];
        function appendSelectedNonExtra(el) {
            nonextrasData = [];
            var parentDiv = el.closest('.input-group-button');
            var nonExtraInput = parentDiv.siblings('input.nonextra_count');
            var nonExtraCountValue = nonExtraInput.val();
            var nonExtraId = nonExtraInput.data('nonextra-id');
            nonextrasData.push({
                nonextra_id: nonExtraId,
                nonextra_count: nonExtraCountValue
            });
            console.log('nonextrasData', nonextrasData);
        }

        $(document).on('click', '.nonextra-count-minus', function(e){
            appendSelectedNonExtra($(this));
        })

        $(document).on('click', '.nonextra-count-plus', function(e){
            appendSelectedNonExtra($(this));
        })

        // get hotel extra data
        var hotelExtrasData = [];
        function appendSelectedHotelExtra(el) {
            hotelExtrasData = [];
            var parentDiv = el.closest('.input-group-button');
            var hotelExtraInput = parentDiv.siblings('input.extra_count');
            var hotelExtraCountValue = hotelExtraInput.val();
            var hotelExtraId = hotelExtraInput.data('hotel_extras_id');
            hotelExtrasData.push({
                extra_id: hotelExtraId,
                extra_count: hotelExtraCountValue
            });
            console.log('hotelExtrasData', hotelExtrasData);
        }

        $(document).on('click', '.extra-count-minus', function(e){
            appendSelectedHotelExtra($(this));
        })

        $(document).on('click', '.extra-count-plus', function(e){
            appendSelectedHotelExtra($(this));
        })

        // get bibs data
        var bibsData = [];
        var bibsCount = [];
        function getBibsData(el) {
            bibsData = [];
            bibsCount = []
            var parentDiv = el.closest('.input-group-button');
            var bibInput = parentDiv.siblings('input.bibs_count');
            var bibCountValue = bibInput.val();
            var bibId = bibInput.data('bib-id');
            bibsData.push({
                bib_id: bibId,
                bib_count: bibCountValue
            });
            bibsCount.push({
                prod_id: bibId,
                prod_count: bibCountValue
            
            });
            // console.log('bibsData', bibsData);
        }

        $(document).on('click', '.bib-count-minus', function(e){
            getBibsData($(this));
        })

        $(document).on('click', '.bib-count-plus', function(e){
            getBibsData($(this));
        })

        // get flightsFormData
        var flightData = [];
        var allotmentFlightData = [];
        $(document).on('click', 'input[name="flightplan_id_OLD"]', function(e){
            if($('input[id="own-transport"]').is(':checked')){
                flightData = [];
                allotmentFlightData = [];
            }else{
                $(document).on('click', 'input[name="flightplan_list"]', function(e){
                    flightData = [];
                    allotmentFlightData = [];
                    var travellersCount = parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val());
                    var checkedValue = $('input[type="radio"][name="flightplan_list"]:checked').val();
                    $('input[type="radio"][name="flightplan_list"]').each(function() {
                        if ($(this).val() == checkedValue) {
                            // var flight_id = $(this).data("flight-id");
                            var cardDiv = $(this).closest('.card-input');     
                            var goFlightDetails = cardDiv.find('#go_flight_details');  
                            var go_select_box = cardDiv.find('.flight-seat-select-box');
                            var go_select = go_select_box.find('.departure-select');
                            // get flight id from goFlightDetails select
                            var go_flight_id = goFlightDetails.data('flight-id');
                            // var go_flight_plan_id = goFlightDetails.data('flight-plan-id');
                            var go_flight_plan_id = $(this).data("flight-id");
                            var goFlightSelect = goFlightDetails.find('.select2-selection__rendered');
                            // var departureClass = goFlightSelect.text();
                            
                            //return flight details
                            var returnFlightDetails = cardDiv.find('#return_flight_details');   
                            var return_select = returnFlightDetails.find('.arrival-select');
                            // get flight id from returnFlightDetails select
                            var return_flight_id = returnFlightDetails.data('flight-id');     
                            // get select value from returnFlightDetails
                            var returnFlightSelect = returnFlightDetails.find('.select2-selection__rendered');
                            // var arrivalClass = returnFlightSelect.text();
                            // $(document).on('change', return_select, function(e){
                            //     localStorage.setItem('arrivalClass', returnFlightDetails.find('.arrival-select').find('option:selected').val());
                            // })
                            
                            flightData.push(
                                {
                                    flight_id: go_flight_id,
                                    flight_type: [
                                        {
                                            route: 2,
                                            // info: localStorage.getItem('departureClass')
                                            info: ""
                                        }
                                    ]
                                }
                            );
                            flightData.push(
                                {
                                    flight_id: return_flight_id,
                                    flight_type: [
                                        {
                                            route: 1,
                                            info: ''
                                        }
                                    ]
                                }
                            );
                            // get flight data to check allotment
                            allotmentFlightData.push(
                                {
                                    flight_plan_id: go_flight_plan_id,
                                    ticket_type: "",
                                    seat_count: travellersCount
                                }
                            );
                        }
                    });
                
                    $(document).on('change', '.departure-select', function(){
                        // localStorage.setItem('departureClass', e.target.value);
                        var seatClass = $(this).children('option:selected').val();
                        flightData[0].flight_type[0].info = seatClass;
                        allotmentFlightData[0].ticket_type = seatClass;
                    })
                    if(flightData.length > 2){
                        flightData = [flightData[0], flightData[1]];
                    }
                    if(allotmentFlightData.length > 1){
                        allotmentFlightData = [allotmentFlightData[0]];
                    }
                })
            }
        })
        

        // Function to post booking details
        function postBookingDetails() {
            // Capture values of fields in variables
            // console.log(('flightData', flightData));
            console.log('extraRunnersData', extraRunnersData);
             var insuranceData = [];
            $('#insurances_container input[type="checkbox"]:checked').each(function() {
                insuranceData.push({
                    insurance_id: $(this).data('insurance-id'),
                    insurance_count: 1,
                    // insurance_price: $(this).data('price'),
                    // insurance_name: $(this).data('name')
                });
            });

            var adultsCount 				= $('#adults_count').val();
            var childrenCount 				= $('#children_count').val();
            var childrenUnder3Count 		= $('#children_under_3_count').val();
        
            var arrivalDate 				= $('#booking_end_date').val();
            var departureDate 				= $('#booking_start_date').val();
            var hotelId 					= $('#hotel_id').val();
        
            var glFormData 					= $('#gl_form').serialize();
            var sahFormData 				= $('#sah_form').serialize();
            var optionalFormData 			= $('#optional_form').serialize();
            var bibsFormData 				= $('#bibs_form').serialize();
            var roomtypeFormData 			= $('#roomtype_form').serialize();
            var extrasFormData				= $('#extras_form').serialize();
            var insuranceFormData 			= $('#insurance_form').serialize();
            
            var flightsFormData 			= $('#flights_form').serialize();
            var hotelFormData 				= $('#hotel_form').serialize();
            var visitor_listFormData 		= $('#visitor_list_form').serialize();
            var visitor_dtlFormData 		= $('#visitor_dtl_from').serialize();
            var datesFormData 				= $('#dates_form').serialize();
        
            var flightPlanId 				= $('#flight_plan_id').val();
            var specialMessage 				= $('#special_message').val();

            var priceString 			    = $('#total_booking').text();
            var priceNumericValue           = parseFloat(priceString.replace(/[^0-9.-]+/g, ""));
            var bookingPrice 				= priceNumericValue.toFixed(2);

            // var selectedRadio = $('input[name="hotel_room_id"]:checked');
            // console.log('selectedRadio', selectedRadio);
            // var parentDiv = selectedRadio.closest('.hotel-rooms-labels');
            // console.log('parentDiv', parentDiv);
            // var selectedRoom = parentDiv.find('input[type="number"]');
            // var roomCountValue = selectedRoom.val();
            // var roomId = selectedRoom.attr('name');
            // console.log('roomCountValue', roomCountValue);
            // console.log('roomId', roomId);
        /*
        - gl_is_runner
        - gl_title
        - gl_first_name
        - gl_middle_name
        - gl_last_name
        - gl_dob
        - gl_country
        - gl_nationality
        - gl_lives_in_country
        - gl_street
        - gl_house_number
        - gl_city
        - gl_postal_code
        - gl_fixed_phone
        - gl_mobile
        - gl_email
        - gl_email_confirm
        
        - sah_title
        - sah_first_name
        - sah_middle_name
        - sah_last_name
        - sah_dob
        - sah_country
        - sah_lives_in_country
        - sah_street
        - sah_house_number
        - sah_city
        - sah_postal_code
        - sah_fixed_phone
        - sah_mobile
        - sah_email
        - sah_email_confirm
        
        - v_title[]
        - v_first_name[]
        - v_middle_name[]
        - v_last_name[]
        - v_dob[]
        - v_country[]
        - v_lives_in_country[]
        - v_street[]
        - v_house_number[]
        - v_city[]
        - v_postal_code[]
        
        - bibs_id[] -> value={bib id}  (hidden)
        - bibs_count[]
        
        - arrival_date  (hidden)
        - departure_date (hidden)
        
        - hotel_id -> value={hotelb id} (hidden)
        
        - room_type_id  -> value={roomtype id} (hidden)
        - room_quantity  (hidden)
        
        - extras[] -> value={extra id} (hidden)
        - extras_quantity[]
        
        - flight_plan_id -> value={flightplan id}
        - flight_plan_id -> value=-1 (own transport)
        
        - insurance_id[{insurance id}]  -> value={price}
        - special_message
        - booking_price
        */
            // Constructing the POST data
            var data = '&adults_count=' + adultsCount;
            data += '&children_count=' + childrenCount;
            data += '&children_under_3_count=' + childrenUnder3Count;
            data += '&' + glFormData; // json object with user informations(name, email etc..)
            data += '&' + sahFormData;
            data += '&' + optionalFormData;
            data += '&' + bibsFormData;
            data += '&arrival_date=' + arrivalDate;
            data += '&departure_date=' + departureDate;
            data += '&' + hotelFormData;
            data += '&' + roomtypeFormData;
            data += '&' + extrasFormData;
            data += '&' + flightsFormData;
            data += '&' + insuranceFormData;
            data += '&special_message=' + specialMessage;
            // data += '&booking_price=' + bookingPrice;
            data += '&booking_price=' + $('#total_booking').text();
            data += '&rooms=' + JSON.stringify(hotelRooms);
            data += '&bibs=' + JSON.stringify(bibsData);
            data += '&flight_seats=' + JSON.stringify(flightData);
            data += '&flight_allotment_seats=' + JSON.stringify(allotmentFlightData);
            data += '&bibs_count=' + JSON.stringify(bibsCount);
            data += '&nonextras=' + JSON.stringify(nonextrasData);
            data += '&extras=' + JSON.stringify(hotelExtrasData);
            // data += '&insurance=' + JSON.stringify([{"insurance_id": 3, "insurance_count": 1}, {"insurance_id": 4, "insurance_count": 1}]);
            data += '&insurance=' + JSON.stringify(insuranceData);
            data += '&visitor_list=' + JSON.stringify(extraRunnersData);
            // data += '&summary=' + JSON.stringify(encodeURIComponent($('#summary_data').html()));
            
            var options = { year: 'numeric', month: 'short', day: 'numeric' };
            var birthdate_visitor = new Date( $('#gl_dateofbirth').val() );
            var country_visitor = $('#gl_country').select2('data');
            var title_visitor = $('#gl_title').select2('data');
            var title_stayathome = $('#sah_title').select2('data');
            var country_visitor = $('#gl_country').select2('data');
            var country_visitor_nationality = $('#gl_nationality').select2('data');
            var country_stayathome = $('#sah_country').select2('data');
            var bibs = [];
            var birthdate_stayathome = new Date( $('#sah_dateofbirth').val() );
            $( '#form_section3 input.bibs_count' ).each(function(){
                bibs.push({
                    bibs_id: $(this).data('bib-id'),
                    bibs_name: $(this).data('bibs_name'),
                    bibs_count: $(this).val(),
                    bibs_price: $(this).data('price')
                })
            })
            var start_date = $('#booking_start_date').val();
            var end_date = $('#booking_end_date').val();
            // var eventSettings = response.data.event_settings;
            // var booking_start_date = eventSettings.start_date;
            // var booking_end_date = eventSettings.end_date;
            // var booking_sgr_fee = eventSettings.sgr_fee;
            // var booking_insurance_fee = eventSettings.insurance_fee;
            // var booking_calamity_fund = eventSettings.calamity_fund;
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            var options_summary = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
            // var start_date = new Date(booking_start_date);
            // var end_date = new Date(booking_end_date);
            var extra_of_hotels = [];
            $( '#form_section6 input.extra_count' ).each(function(){
                extra_of_hotels.push({
                    extra_name: $(this).data('extras_name'),
                    extra_count: $(this).val(),
                    extra_price: parseInt(extra_count) * parseFloat($(this).data('price'))
                })
            })
            var non_extra_of_hotels = [];
            // $( '#form_section6 input.nonextra_count' ).each(function() {
            //     non_extra_of_hotels.push({
            //         non_extra_name: $(this).data('extras_name'),
            //         non_extra_count: $(this).val(),
            //         non_extra_price: parseInt(extra_count) * parseFloat($(this).data('price'))
            //     })
            // })

            // var booking_date_info = booking_start_date + ' - ' + booking_end_date;

            // var bookingData = {
                // adults_count: $('#adults_count').val(),
                // children_count: $('#children_count').val(),
                // children_under_3_count: $('#children_under_3_count').val(),
                // visitor_title: title_visitor[0].text,
                // visitor_name: $('#gl_first_name').val() + ' ' + $('#gl_middle_name').val() + ' ' + $('#gl_last_name').val(),
                // visitor_address: $('#gl_street').val() + ' ' + $('#gl_house_number').val() + ', ' + $('#gl_residence').val(),
                // gl_title: $('#gl_title').select2('data')[0].text,
                // birthdate_visitor: birthdate_visitor.toLocaleDateString("nl-NL", options)  + ' | ' + country_visitor[0].text,
                // birthdate_visitor: birthdate_visitor.toLocaleDateString("nl-NL", options)  + ' | ' + country_visitor[0].text + ' | ' + country_visitor_nationality[0].text,
                // country_stayathome: title_stayathome[0].text,
                // booking_stayhome_name: $('#sah_first_name').val() + ' ' + $('#sah_middle_name').val() + ' ' + $('#sah_last_name').val(),
                // booking_stayathome_address_div: $('#sah_street').val() + ' ' + $('#sah_house_number').val() + ', ' + $('#sah_residence').val(),
                // booking_stayathome_birthdate_div: birthdate_stayathome.toLocaleDateString("nl-NL", options) + ' | ' + country_stayathome[0].text,
                // summary_bibs: bibs,
                // departure_date: $('#summary_departure_date').text(),
                // arrival_date: $('#summary_arrival_date').text(),
                // hotel_name: $('#summary_hotel_name').text(),
                // hotel_price: $('#summary_room_price').text(),
                // extra_of_hotels: extra_of_hotels,
                // non_extra_of_hotels: non_extra_of_hotels,
                // flight: $('#summary_flight_div').text(),
                // insurance: $('#summary_insurance_div').text(),
                // sgr_fee: $('#booking_sgr_fee_div').text(),
                // insurance_fee: $('#booking_insurance_fee_div').text(),
                // calamity_fund: $('#booking_calamity_fund_div').text(),
                // total_booking: $('#total_booking').text(),
            // }
            // mailBookingData(bookingData);

            // Ajax call to post data
            var summary = JSON.stringify(encodeURIComponent($('#summary_data').html()));
            saveBookingData(data, summary);
        }

        
    });
    
</script>

<div class="booking_section booking-form-section-cont">
    <div class="container">

    <!-- <div class="row"> -->
            <div class="col-12 sticky-progress">
                <div class="mt-4">
                    <div class="progress">
                        <div role="progressbar" id="progress-bar" class="progress-bar _bg-info _progress-bar-striped" style="width:0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </div>
            </div>
        <!-- </div> -->

        <div class="row">
            <div class="col-12">
                <h2 id="booking_event">[Event]</h2>
            </div>
        </div>

        
        <div class="row booking-form-section">
            <div class="col-sm-8 booking_leftcol">
                <!-- [Left column] -->
                    
                    <div class="accordion full-form-container" id="booking_form">
                        <!-- #01 Aantal bezoekers -->
                        <div class="card">
                            <div class="card-header" id="heading1">
                                <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#form_section1" aria-expanded="true" aria-controls="form_section1">
                                    <span class="steps body-18 regular-400">01</span>
                                    AANTAL REIZIGERS
                                </button>
                                </h2>
                            </div>
                    
                            <div id="form_section1" class="collapse show" aria-labelledby="heading1" data-parent="#booking_form">
                                <div class="card-body">

                                    <!-- Error Message Display -->
                                    <div class="error-message text-danger"></div>

                                    <form>
                                        <div class="row">
                                            <div class="col-md-4 col-xl-4 col-12">
                                                <div class="form-group grey-blue-bg-box">
                                                    <label class="form-label form-label-blue">Volwassenen <span class="required">*</span></label>
                                                    <div class="input-group plus-minus-input traveler-plus-min">
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="minus" data-field="quantity">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                        <input placeholder="Bezoekers (volwassen)" class="input-group-field" type="number" name="adults_count" id="adults_count" min="1" value="1" required>
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="plus" data-field="quantity">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="info sub-label">(12 jaar en ouder)</span>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xl-4 col-12">
                                                <div class="form-group grey-blue-bg-box">
                                                    <label class="form-label form-label-blue">Kinderen</label>
                                                    <div class="input-group plus-minus-input traveler-plus-min">
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="minus" data-field="quantity">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                        <input placeholder="Bezoekers (kinderen)" class="input-group-field" type="number" name="children_count" id="children_count" min="0" value="0">
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="plus" data-field="quantity">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="info sub-label">(tussen 3 en 12 jaar)</span>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xl-4 col-12">
                                                <div class="form-group grey-blue-bg-box">
                                                    <label class="form-label form-label-blue">Baby's</label>
                                                    <div class="input-group plus-minus-input traveler-plus-min">
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="minus" data-field="quantity">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                        <input placeholder="Bezoekers (kinderen < 3jr)" class="input-group-field" type="number" name="children_under_3_count" id="children_under_3_count" min="0" value="0">
                                                        <div class="input-group-button">
                                                            <span class="button hollow circle" data-quantity="plus" data-field="quantity">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="info sub-label">(jonger dan 3 jaar)</span>
                                                </div>
                                            </div>

                                        </div>
                                    </form>
                                    <div class="row">
                                        <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                            <button id="travelers_form_section" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="11" data-toggle="" data-target="#form_section2" data-source="#form_section1" aria-expanded="true" aria-controls="form_section2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                    <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                        <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                        <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                    </g>
                                                </svg>
                                                <span id="travelers_form_txt">Ga door</span>
                                            </button>   

                                        </div>                   
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    
                        <!-- #02 Reizigers aanmaken -->
                        <div class="card">
                        <div class="card-header" id="heading2">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="null" data-target="#form_section2" aria-expanded="false" aria-controls="form_section2">
                                <span class="steps body-18 regular-400 numb">02</span>
                                Gegevens van de reizigers
                            </button>
                            </h2>
                        </div>
                        <div id="form_section2" class="collapse" aria-labelledby="heading2" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>
                                
                                <form name="gl_form" id="gl_form" method="POST">

                                    <input type="hidden" name="travellers_amount" id="travellers_amount" value="1">

                                    <div class="row fm-rws-travelers">
                                        <div class="col-12">
                                            <div class="visitor">
                                                <div class="align-self-center">
                                                    <p class="caption theme-color-secondary mb-0 form-label-blue">Hoofdboeker</p>
                                                </div>

                                                <div class="mt-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="gl_is_runner" id="gl_is_runner" class="form-input-checkbox traveller_is_runner">
                                                            <label title="" for="gl_is_runner" class="custom-control-label"></label>
                                                        </div>
                                                        <label class="form-label">
                                                            <span class="checkbox-label ml-2">De hoofdboeker is een hardloper</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
										<div class="row fm-rws-travelers">
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Geslacht <span class="required">*</span></label>
                                                <select placeholder="Titel" data-placeholder="Titel" name="gl_title" id="gl_title" class="pl-2 form-control form-select" required>
                                                    <option value="dhr.">dhr.</option>
                                                    <option value="mevr.">mevr.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-12" id="main-traveller-dob">
                                            <!-- <div class="form-group">
                                                <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                                                <input class="form-control" type="date" id="gl_dateofbirth" name="gl_dateofbirth" placeholder="Geboortedatum" required>
                                            </div> -->
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-12">
                                        <div class="form-group">
                                                <!-- <label class="form-label field-label">Nationaliteit <span class="required">*</span></label>
                                                <select placeholder="Nationaliteit" dataplaceholder="Nationaliteit" class="form-control form-select" name="gl_nationality" id="gl_nationality" required>
                                                    <option value="">Nationaliteit</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div> -->
                                             <div class="form-group">
                                                <label class="form-label field-label">Nationaliteit <span class="required">*</span></label>
                                                <select placeholder="Nationality" dataplaceholder="Nationality" class="form-control form-select" name="gl_nationality" id="gl_nationality" required>
                                                    <option value="">Nationaliteit</option>
                                                </select>
                                            </div>
                                            
                                        </div>
                                    </div>
										</div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Voornaam (volgens paspoort)  <span class="required">*</span></label>
                                                <input type="text" placeholder="Voornaam" name="gl_first_name" id="gl_first_name" class="form-control" required>

                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Tussenvoegsel (optioneel)</label>
                                                <input type="text" placeholder="Tussenvoegsel" name="gl_middle_name" id="gl_middle_name" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Achternaam (volgens paspoort)  <span class="required">*</span></label>
                                                <input type="text" placeholder="Achternaam" name="gl_last_name" id="gl_last_name" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- <div id="runners_div" class="row">

                                    </div> -->

                                    <div class="row fm-rws-travelers">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label class="form-label field-label">Straat <span class="required">*</span></label>
                                                <input type="text" placeholder="Straat" name="gl_street" id="gl_street" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Huisnummer <span class="required">*</span></label>
                                                <input type="text" placeholder="Huisnummer" name="gl_house_number" id="gl_house_number" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Postcode <span class="required">*</span></label>
                                                <input type="text" placeholder="Postcode" name="gl_postal_code" id="gl_postal_code" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Woonplaats <span class="required">*</span></label>
                                                <input type="text" placeholder="Woonplaats" name="gl_residence" id="gl_residence" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Land <span class="required">*</span></label>
                                                <select placeholder="Land" dataplaceholder="Land" class="form-control form-select" name="gl_country" id="gl_country" required>
                                                    <option value="">Land</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                           
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">Vast telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Vast telefoonnummer" name="gl_fixed_phone" id="gl_fixed_phone" class="form-control" required>
                                        </div>
                                        </div>
                                        <div class="col-sm-6 col-sm-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">Mobiel telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Mobiel telefoonnummer" name="gl_mobile" id="gl_mobile" class="form-control" required>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12 email-field">
                                        <div class="form-group">
                                            <label class="form-label field-label">E-mailadres <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres" name="gl_email" id="gl_email" class="form-control email" required>
                                        </div>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12 email-field">
                                        <div class="form-group">
                                            <label class="form-label field-label">E-mailadres bevestigen <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres bevestigen" name="gl_email_confirm" id="gl_email_confirm" class="form-control confirm-email" required>
                                        </div>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                    </div>

                                    <div id="runners_div" class="row fm-rws-travelers">

                                    </div>

                                </form>
							
							<div class="row fm-rws-travelers">		
                                <form name="sah_form" id="sah_form" method="POST">
                                    <p class="caption theme-color-secondary mb-0 form-label-blue">Thuisblijversinformatie</p>
                                    <p class="body-text-regular">Deze persoon wordt gecontacteerd in geval van nood</p>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Geslacht* <span class="required"></span></label>
                                                <select placeholder="Titel" required data-placeholder="Titel" name="sah_title" id="sah_title" class="pl-2 form-control form-select">
                                                    <option value="dhr.">dhr.</option>
                                                    <option value="mevr.">mevr.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4" id="stay-home-dob">
                                            <!-- <div class="form-group">
                                                <label class="form-label field-label">Geboortedatum <span class="required"></span> </label>
                                                <input class="form-control" type="date" id="sah_dateofbirth" name="sah_dateofbirth" placeholder="Geboortedatum">
                                            </div> -->
                                        </div>
                                        <div class="col-4">
                                            
                                        </div>
                                    </div>

                                    <div class="row  fm-rws-travelers">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Voornaam* <span class="required"></span></label>
                                                <input type="text" placeholder="Voornaam" required name="sah_first_name" id="sah_first_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6  col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Tussenvoegsel (optioneel)</label>
                                                <input type="text" placeholder="Tussenvoegsel" name="sah_middle_name" id="sah_middle_name" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Achternaam*<span class="required"></span></label>
                                                <input type="text" placeholder="Achternaam" required name="sah_last_name" id="sah_last_name" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label class="form-label field-label">Straat <span class="required"></span></label>
                                                <input type="text" placeholder="Straat" name="sah_street" id="sah_street" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Huisnummer <span class="required"></span></label>
                                                <input type="text" placeholder="Huisnummer" name="sah_house_number" id="sah_house_number" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-fields-rows fm-rws-travelers">
                                        <div class="col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Postcode <span class="required"></span></label>
                                                <input type="text" placeholder="Postcode" name="sah_postal_code" id="sah_postal_code" class="form-control">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Woonplaats <span class="required"></span></label>
                                                <input type="text" placeholder="Woonplaats" name="sah_residence" id="sah_residence" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Land <span class="required"></span></label>
                                                <select placeholder="Land" data-placeholder="Land" class="form-control form-select" name="sah_country" id="sah_country">
                                                    <option value="">Land</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">Vast telefoonnummer <span class="required"></span></label>
                                            <input type="tel" placeholder="Vast telefoonnummer" name="sah_fixed_phone" id="sah_fixed_phone" class="form-control">
                                        </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">Mobiel telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Mobiel telefoonnummer" name="sah_mobile" id="sah_mobile" class="form-control" required>
                                        </div>
                                        </div>
                                    </div>

                                    <div class="row fm-rws-travelers">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">E-mailadres*<span class="required"></span></label>
                                            <input type="email" placeholder="E-mailadres" required name="sah_email" id="sah_email" class="form-control email">
                                        </div>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label field-label">E-mailadres bevestigen*<span class="required"></span></label>
                                            <input type="email" placeholder="E-mailadres bevestigen" required name="sah_email_confirm" id="sah_email_confirm" class="form-control confirm-email">
                                        </div>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                    </div>

                                </form>
							</div>	

                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button id="travellers_form_section" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="22" data-toggle="null" data-target="#form_section3" data-source="#form_section2" aria-expanded="true" aria-controls="form_section3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="travellers_step_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>

                            </div>
                        </div>
                        </div>
                    
                        <!-- #03 Startbewijzen -->
                        <div class="card">
                            <div class="card-header" id="heading3">
                                <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section3" aria-expanded="false" aria-controls="form_section3">
                                    <span class="steps body-18 regular-400 numb">03</span>
                                    Startbewijzen
                                </button>
                                </h2>
                            </div>
                            <div id="form_section3" class="collapse" aria-labelledby="heading3" data-parent="#booking_form">
                                <div class="card-body">

                                    <!-- Error Message Display -->
                                    <div class="error-message text-danger"></div>

                                    <form name="bibs_form" id="bibs_form" method="POST">
                                        <input type="hidden" id="total_bibs_count" name="total_bibs_count" value="0">
                                        <input type="hidden" id="total_bibs_price" value="0.00">          

                                        <div class="row" id="bibs_div">										
                                            
                                        </div>
            
                                    </form>

                                    <div class="row">
                                        <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                            <button id="bibs_form_section" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="33" data-toggle="" data-target="#form_section4" data-source="#form_section3" aria-expanded="true" aria-controls="form_section4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                    <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                        <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                        <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                    </g>
                                                </svg>
                                                <span id="bibs_form_txt">Ga door</span>
                                            </button>   

                                        </div>                   
                                    </div>                                
                                </div>
                            </div>
                        </div>
                    
                        <!-- #04 Data -->
                        <div class="card">
                        <div class="card-header" id="heading4">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section4" aria-expanded="false" aria-controls="form_section4">
                                <span class="steps body-18 regular-400 numb">04</span>
                                Data
                            </button>
                            </h2>
                        </div>
                        <div id="form_section4" class="collapse" aria-labelledby="heading4" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>

                                <form name="dates_form" id="dates_form">

                                    <input type="hidden" id="booking_start_date" name="arrival_date" value="">
                                    <input type="hidden" id="booking_end_date" name="departure_date" value="">

                                    <div class="row">
        
                                        <div class="d-flex justify-content-between date-information">
                                            <div class="date-info"><span>van:</span>
                                                <div class="date-info_in" id="booking_date_info">[] - []</div>
                                            </div>
                                        </div>

                                    </div>
                                </form>

                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button id="event_date_form_section" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="44" data-toggle="" data-target="#form_section5" data-source="#form_section4" aria-expanded="true" aria-controls="form_section5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="event_date_form_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>

                            </div>
                        </div>
                        </div>
                    
                        <!-- #05 Hotel -->
                        <div class="card">
                        <div class="card-header" id="heading5">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section5" aria-expanded="false" aria-controls="form_section5">
                                <span class="steps body-18 regular-400 numb">05</span>
                                Hotel
                            </button>
                            </h2>
                        </div>
                        <div id="form_section5" class="collapse" aria-labelledby="heading5" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>												

                                <form name="hotel_form" id="hotel_form" method="POST">
 
                                    <input type="hidden" id="hotel_id" name="hotel_id" value="0">
                                    <input type="hidden" id="total_room_count" name="total_room_count" value="0">
                                    <input type="hidden" id="total_room_price" value="0.00">                                     
                                    
                                    <div class="row radio-btn-grp-row" id="hotels_container">
        
                                    </div>

                                    <div id="rooms-titles" class="selecteer-kamer-title">Selecteer kamers</div>
                                    
                                    <input type="hidden" placeholder="GetHotels" required="required" value="null">
                                    <div class="row radio-btn-grp-row" id="hotel_rooms_container">
                        
                                    </div>                                    
        
                                </form>
 
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button id="hotel_room_form_section" class="btn btn-link btn-block btn-form-step text-left hotel-btn-form-step" type="button" data-percent="55" data-toggle="" data-target="#form_section6" data-source="#form_section5" aria-expanded="true" aria-controls="form_section6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="hotel_room_form_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>                                

                            </div>
                        </div>
                        </div>
                    
                        <!-- #06 Extra's -->
                        <div class="card">
                        <div class="card-header" id="heading6">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section6" aria-expanded="false" aria-controls="form_section6">
                                <span class="steps body-18 regular-400 numb">06</span>
                                Extra's
                            </button>
                            </h2>
                        </div>
                        <div id="form_section6" class="collapse" aria-labelledby="heading6" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>

                                <form name="extras_form" id="extras_form" method="POST">
                                    <input type="hidden" id="total_extra_count" value="0">
                                    <input type="hidden" id="total_extra_price" value="0.00">  
                                    <input type="hidden" id="total_nonextra_count" value="0">
                                    <input type="hidden" id="total_nonextra_price" value="0.00">  

                                    <div class="row extras-details-row">
        
                                        <label class="card-title" id="hotel_extra_title">Hotel extras</label>		
                                        <div class="radio-btn-grp-row" id="hotel_extras_details_div"></div>

                                        <label class="card-title">Extra's van het hotel</label>		
                                        <div class="radio-btn-grp-row" id="non_hotel_extras_details_div"></div>
        
                                    </div>
                                </form>   
                                
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="66"  data-toggle="" data-target="#form_section7" data-source="#form_section6" aria-expanded="true" aria-controls="form_section7">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="extra_form_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>                                

                            </div>
                        </div>
                        </div>
                    
                        <!-- #07 Vervoer -->
                        <div class="card">
                            <div class="card-header" id="heading7">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section7" aria-expanded="false" aria-controls="form_section7">
                                        <span class="steps body-18 regular-400 numb">07</span>
                                        Vervoer
                                    </button>
                                </h2>
                            </div>
                        <div id="form_section7" class="collapse" aria-labelledby="heading7" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>

                                <form name="flights_form" id="flights_form">
                                    <input type="hidden" id="flight_plan_id" name="flight_plan_id" value="0">
                                    <input type="hidden" id="total_flight_departure_price" value="0.00">
                                    <input type="hidden" id="total_flight_arrival_price" value="0.00">                                        
                                    
                                    <div class="row">

                                        <div class="col vervoer-radio-btn-group">
                                            <label class="vervoer-radio-btn-label">
                                                <input type="radio" name="flightplan_id_OLD" value="-1" class="card-input-element" required placeholder="owntransport" id="own-transport"/>
    
                                                <div class="card card-default card-input">
                                                    <div class="card-header">
                                                        <div class="card-title">Eigen vervoer</div>
                                                        <div class="card-title-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="33.333" viewBox="0 0 50 33.333">
                                                                <path id="car" d="M65.732,109.956c-.312-.521-3.4-1.853-3.4-1.853.536-.277.9-.334.9-1.48,0-1.25-.006-1.667-.84-1.667H59.572c-.011-.025-.024-.051-.035-.077-1.825-3.985-2.07-4.993-4.792-6.349-3.651-1.816-10.5-1.907-13.179-1.907s-9.528.092-13.176,1.907c-2.725,1.354-2.657,2.051-4.792,6.349a.4.4,0,0,1-.042.077h-2.83c-.827,0-.833.417-.833,1.667,0,1.146.367,1.2.9,1.48a29.7,29.7,0,0,0-3.4,1.853c-.417.417-.833,3.333-.833,8.333s.417,10,.417,10h1.244c0,1.458.215,1.667.84,1.667H27.4c.625,0,.833-.208.833-1.667H54.9c0,1.458.208,1.667.833,1.667h8.542c.417,0,.625-.312.625-1.667h1.25s.417-5.1.417-10-.521-7.812-.833-8.333Zm-37.785,4.681a53.836,53.836,0,0,1-5.712.319c-2.127,0-2.2.136-2.35-1.192a7.517,7.517,0,0,1,.053-1.824l.066-.318h.313a14.366,14.366,0,0,1,4.641.706A10.208,10.208,0,0,1,28.09,113.9a1.5,1.5,0,0,1,.558.642Zm25.746,7.5-.46,1.152H29.9s.041-.064-.521-1.165c-.417-.815.1-1.335.928-1.631a34.66,34.66,0,0,1,11.259-2.2,29.5,29.5,0,0,1,11.3,2.2c.573.3,1.284.5.825,1.65ZM26.922,107.916a10.03,10.03,0,0,1-1.01.007c.272-.483.423-1.022.689-1.584.833-1.771,1.786-3.775,3.483-4.62,2.452-1.221,7.534-1.771,11.482-1.771s9.03.546,11.482,1.771c1.7.845,2.646,2.85,3.483,4.62.268.568.417,1.11.7,1.6-.208.011-.448,0-1.02-.02Zm36.221,5.845c-.223,1.3-.015,1.2-2.246,1.2a53.839,53.839,0,0,1-5.713-.319.447.447,0,0,1-.144-.74,9.776,9.776,0,0,1,3.134-1.569,13.5,13.5,0,0,1,4.7-.7.335.335,0,0,1,.322.313,7.3,7.3,0,0,1-.051,1.82Z" transform="translate(-16.565 -96.623)" fill="#0093cb" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="card-body-descr">
                                                            <p class="extras-sub-label">Selecteer deze optie als je gebruik maakt van eigen vervoer</p>
                                                        </div>
    
                                                    </div>
                                                </div>
    
                                            </label>
    
                                        </div>                                        

                                        <div class="col vervoer-radio-btn-group">
                                            <label class="vervoer-radio-btn-label">
                                                <input type="radio" name="flightplan_id_OLD" value="0" class="card-input-element" required placeholder="flighttransport" id="flight-transport"/>
    
                                                <div class="card card-default card-input">
                                                    <div class="card-header">
                                                        <div class="card-title">Met het vliegtuig</div>
                                                        <div class="card-title-icon">
                                                            <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 959.192 959.192" xml:space="preserve">
                                                                <path d="M923.777,2.34l-101.5,46.2c-6.5,3-12.5,7.1-17.6,12.2l-165.4,165.5l-569.6-68.3c-10.3-1.2-20.7,2.3-28,9.7l-31.7,31.7
                                                                    c-16.8,16.8-11.6,45.2,10.1,54.9l408.2,183l-117.2,117.2h-204.7c-9,0-17.6,3.6-24,9.899l-17.1,17.2c-17,17-11.4,45.7,10.6,55.101
                                                                    l172.7,74l74,172.699c9.4,22,38.2,27.601,55.101,10.601l17.199-17.2c6.4-6.4,9.9-15,9.9-24v-204.7l117.2-117.2l183,408.301
                                                                    c9.7,21.699,38.1,26.899,54.899,10.1l31.7-31.7c7.4-7.4,10.9-17.7,9.7-28l-68.4-569.6l165.5-165.5c5.101-5.1,9.2-11,12.2-17.6
                                                                    l46.2-101.5C966.478,14.44,944.877-7.26,923.777,2.34z" fill="#0093cb"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="card-body-descr">
                                                            <p class="extras-sub-label">Selecteer deze optie als je een vliegtuigvlucht wilt nemen</p>
                                                        </div>
    
                                                        <div id="flights_container" style="display:none;"></div>

                                                    </div>
                                                </div>
    
                                            </label>
    
                                        </div>


                                        <!--  -->
    

    
                                    </div>
    
                                </form>  
                                
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button id="transport_step_btn" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="88" data-toggle="" data-target="#form_section8" data-source="#form_section7" aria-expanded="true" aria-controls="form_section8">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="transport_step_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>           

                            </div>
                        </div>
                    </div>
                    
                    <!-- #08 Verzekeringen -->
                    <div class="card">
                        <div class="card-header" id="heading8">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section8" aria-expanded="false" aria-controls="form_section8">
                                <span class="steps body-18 regular-400 numb">08</span>
                                Verzekeringen
                            </button>
                            </h2>
                        </div>
                        <div id="form_section8" class="collapse" aria-labelledby="heading8" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>

                                <form name="insurance_form" id="insurance_form" method="POST">
                                    <input type="hidden" id="total_insurance_price" value="0.00">  

                                    <div class="row">
                                        <div class="col-12">
                                            <p class="form-label-blue">Zorgeloos reizen!</p>
                                            <p class="body-14 regular-400 text-black"></p>
                                            <p class="body-14 regular-400 text-black" style="display:block"></p>
                                        </div>
                                        <div class="col-12 table-responsive overflow-y-clip">
                                            <table class="table insurance-table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="insurance-table-headings">Type verzekering</th>
                                                        <th class="insurance-table-headings">Prijs</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="insurances_container">
 
                                                    <tfoot class="verzekering-table-footer">
                                                        <tr>
                                                            <td class="tfoot-col-left-label insurance-totals">Totaal</td>
                                                            <td class="tfoot-col-right-amount insurance-totals">€ <span id="total_insurance">0,00</span></td>
                                                        </tr>
                                                    </tfoot>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="modal fade insurance-modal" id="travelinsurance" tabindex="-1" role="dialog" aria-labelledby="travelinsuranceTitle" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="modal-title body-20 medium-500 align-self-center" id="travelinsuranceTitle">Kortlopende comfort
                                                            reisverzekering</div>
                                                        <button type="button" class="close align-self-center" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body-16 medium-500 mb-1 gray-500">Waar dient deze verzekering voor?</div>
                                                        <p class="body-14 regular-400 gray-900">
                                                            Je bent verzekerd voor hulp voor personen. Als jij of een andere verzekerde hulp nodig hebt U bent verzekerd voor hulp voor personen. Als u of een andere verzekerde hulp nodig heeft door ziekte, ongeval, of overlijden van uw zelf, uw reisgenoot of directe familie. Ook bij een natuurramp. 24 uur per dag beschikking tot de Europeesche Hulplijn. Standaard is het dekkingsgebied Europa Voor een wereld dekking wordt later een toeslag (€ 1,30 p.p.p.d.) berekend.</p>
                                                        <ul class="body-14 regular-400 gray-900 ">
                                                            <li> Inclusief geneeskundige kosten</li>
                                                            <li>Inclusief dekking Bagage, max. 3000,- EUR</li>
                                                            <li>Reis Rechtsbijstand</li>
                                                            <li>Inclusief dekking ongevallen</li>
                                                        </ul>
                                                        <p class="body-14 regular-400 gray-900 mb-0">In de <a class="theme-primary">polisvoorwaarden</a> staat uitgebreid waarvoor iemand wel en niet is verzekerd.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade insurance-modal" id="cancellationinsurance" tabindex="-1" role="dialog" aria-labelledby="cancellationinsuranceTitle" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="modal-title body-20 medium-500 align-self-center" id="cancellationinsuranceTitle">Annuleringsverzekering</div>
                                                        <button type="button" class="close align-self-center" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body-16 medium-500 mb-1 gray-500">Waar dient deze verzekering voor?</div>
                                                        <p class="body-14 regular-400 gray-900">Ondanks dat wij het niet hopen, is het altijd mogelijk dat u door omstandigheden genoodzaakt bent om de reis te annuleren. Loopreizen.nl biedt u de mogelijkheid om een kortlopende annuleringsverzekering (5,5% van de reissom excl. assurantiebelasting) af te sluiten bij Europeesche Verzekeringen.</p>
                                                        <p class="body-14 regular-400 gray-900">U bent verzekerd voor de annuleringskosten vanaf het moment dat u deze verzekering hebt gesloten. Onder anderen voor:</p>
                                                        <ul class="body-14 regular-400 gray-900">
                                                            <li>Kosteloze annulering bij annulering om redenen genoemd in voorwaarden;</li>
                                                            <li>Vergoeding van ongebruikte reisdagen bij ziekenhuisopname;</li>
                                                            <li>Bij overlijden, een ongeval, ernstige ziekte of onverwachte, noodzakelijke, medische behandeling;</li>
                                                            <li>Bij een nieuwe baan nadat u werkloos bent geweest.</li>
                                                        </ul>
                                                        <p class="body-14 regular-400 gray-900 mb-0">In de <a class="theme-primary">polisvoorwaarden</a> staat uitgebreid waarvoor iemand wel en niet is verzekerd.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade insurance-modal" id="injuryinsurance" tabindex="-1" role="dialog" aria-labelledby="injuryinsuranceTitle" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <div class="modal-title body-20 medium-500 align-self-center" id="injuryinsuranceTitle">Blessureverzekering</div>
                                                        <button type="button" class="close align-self-center" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="body-16 medium-500 mb-1 gray-500">Waar dient deze verzekering voor?</div>
                                                        <p class="body-14 regular-400 gray-900">De blessureverzekering kan alleen worden afgesloten indien er ook een annuleringsverzekering is afgesloten. De blessureverzekering (1% extra van de reissom excl. assurantiebelasting) dekt de annuleringskosten bij annulering wegens medisch aantoonbaar lichamelijk letsel, ontstaan tussen de boeking van de reis en het moment van vertrek. In het geval van een blessure dient u dit aan te tonen met een medische verklaring.</p>
                                                        <p class="body-14 regular-400 gray-900 mb-0">In de <a class="theme-primary">polisvoorwaarden</a> staat uitgebreid waarvoor iemand wel en niet is verzekerd.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>  
                                
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button id="insurances_step_btn" class="btn btn-link btn-block btn-form-step text-left" type="button" data-percent="100" data-toggle="collapse" data-target="#form_section9" data-source="#form_section8" aria-expanded="true" aria-controls="form_section9">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            <span id="insurances_step_txt">Ga door</span>
                                        </button>   

                                    </div>                   
                                </div>                                

                            </div>
                        </div>
                        </div>
                    
                        <!-- #09 Samenvatting & betaling -->
                        <div class="card summary-card-bx">
                        <div class="card-header" id="heading9">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="" data-target="#form_section9" aria-expanded="false" aria-controls="form_section9">
                                <span class="steps body-18 regular-400 numb">09</span>
                                Samenvatting & betaling
                            </button>
                            </h2>
                        </div>
                        <div id="form_section9" class="collapse" aria-labelledby="heading9" data-parent="#booking_form">
                            <div class="card-body">

                                <div class="card summary-card">
                                    <!--
                                        <div class="card-body">
                                             <div class="row visiter-xs-100">
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Aantal reizegers</button>
                                                </div>
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Bezoekersinformatie</button>
                                                </div>
                                            </div>
                                            <div class="row visiter-xs-100">
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Startbewijzen</button>
                                                </div>
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Hotel</button>
                                                </div>
                                            </div>
                                            <div class="row visiter-xs-100">
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Datums</button>
                                                </div>
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Extra's</button>
                                                </div>
                                            </div>
                                            <div class="row visiter-xs-100">
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Transport</button>
                                                </div>
                                                <div class="col-sm-6 col-6 box-padding-mob">
                                                    <button type="button" class="btn btn-light summary-model">Verzekering</button>
                                                </div>
                                            </div>
                                        </div>
                                    -->
                                   
                                    <div class="row">
                                            <div class="col-12 my-3" id="summary_data">
                                                <div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Volwassene(n)</p>
                                                            <span id="summary_adults_count">0</span>    
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Kinderen</p>
                                                            <span id="summary_children_count">0</span>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Baby's</p>
                                                            <span id="summary_children_under_3_count">0</span>
                                                        </div>                                                    
                                                    </div>
                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">02</span><span class="summ-heading">Bezoekersinformatie</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Hoofdboeker</p>
                                                            <span class="summary-sub-headings-txt"></span> <span id="booking_visitor_title_div"></span>&nbsp;<span id="booking_visitor_name_div"></span><br>   
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Contactgegevens</p>
                                                            <div class="d-flex">
                                                                <div class="mr-2">
                                                                    <!-- <i class="fa-solid fa-location-dot"></i> -->
                                                                </div>
                                                                <div class="address"><span id="booking_visitor_address_div"></span><br></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Geboortedatum &amp; nationaliteit</p>
                                                            <span id="booking_visitor_birthdate_div"></span>
                                                        </div>                                                    
                                                    </div>

                                                    <div id="extra_runners">

                                                    </div>

                                                    <div class="row form-fields-rows thuisblijver-row">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p class="summary-table-head-subs">Thuisblijver</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p class="summary-table-head-subs">Contactgegevens</p>
                                                    </div>
                                                    </div>

                                                    <div class="row form-fields-rows thuisblijver-row">
                                                        <div class="col">
                                                        
                                                            <span id="booking_stayathome_title_div"></span>&nbsp;<span id="booking_stayathome_name_div"></span><br>   
                                                        </div>
                                                        <div class="col">
                                                            <div class="d-flex">
                                                                <!-- <div class="mr-2">
                                                                    <i class="fa-solid fa-location-dot"></i>
                                                                </div> -->
                                                                <div class="address"><span id="booking_stayathome_address_div"></span><br></div>
                                                            </div>
                                                        </div>
                                                        <div class="col"></div>
                                                        <!-- <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <span id="booking_stayathome_birthdate_div">
                                                        </div>                                                     -->
                                                    </div>

                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">03</span><span class="summ-heading">Startbewijzen</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Challenge</p>
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Aantal</p>
                                                        </div>
                                                        <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Prijs per stuk</p>
                                                        </div>
                                                         <div class="col-md-4 col-lg-4 col-xl-4 strtbewijz-col4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_bibs_div">
                                                    </div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading">Datums</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                                            <p class="summary-table-head-subs">Vertrek</p>
                                                            <span id="summary_departure_date" class="summary-body-txt">-</span>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Aankomst</p>
                                                            <span id="summary_arrival_date" class="summary-body-txt">-</span>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">05</span><span class="summ-heading">Hotel</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows" style="display:flex;flex-direction:column;justify-content:flex-start;align-content:flex-start;">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Hotel naam: <span id="summary_hotel_name" class="summary-body-txt">-</span></p>
                                                            <p class="summary-table-head-subs">Aantal nachten: <span id="summary_hotel_nights" class="summary-body-txt">-</span></p>
                                                        </div>
                                                        <div id="hotel-room-details" style="display:flex;flex-direction:column;justify-content:flex-start;align-content:flex-start;width:95%;"></div>
                                                    </div>

                                                </div>

                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">06</span><span class="summ-heading">Extra's</span></h3>
                                                </div>
                                                <div class="col-12 mob-hide">
                                                    <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Extra's van hotel</h4>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows hotel-extras">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Opties</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Personen</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_extra_div">
                                                    </div>

                                                </div>
                                                <div class="col-12 mt-3 mob-hide">
                                                    <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Extra's buiten het hotel</h4>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows hotel-extras">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Opties</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Personen</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_nonextra_div">
                                                    </div>


                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">07</span><span class="summ-heading">Vervoer</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">
                                                    <div id="flight-holder">
                                                        <div class="row form-fields-rows">
                                                            <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Heenvlucht</h4>
                                                        </div>
                                                        <div class="row form-fields-rows">
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs summary-table-head-subs">Vlucht</p>
                                                                <span id="summary_go_flight_name" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vertrek</p>
                                                                <span id="summary_go_departure" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Aankomst</p>
                                                                <span id="summary_go_arrival" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Reisklasse</p>
                                                                <span id="summary_go_travel_classe" class="summary-body-txt">-</span>
                                                            </div>
                                                        </div>

                                                        <div class="row form-fields-rows">
                                                            <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs" style="margin-top:20px;">Retourvlucht</h4>
                                                        </div>
                                                        <div class="row form-fields-rows">
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vlucht</p>
                                                                <span id="summary_return_flight_name" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Vertrek</p>
                                                                <span id="summary_return_departure" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Aankomst</p>
                                                                <span id="summary_return_arrival" class="summary-body-txt">-</span>
                                                            </div>
                                                            <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                                                                <p class="summary-table-head-subs">Reisklasse</p>
                                                                <span id="summary_return_travel_classe" class="summary-body-txt">-</span>
                                                            </div>
                                                        </div>
                                                        <div class="row summ-flight-deets-row">
                                                            <!-- <p class="summary-table-head-subs">Reisklasse</p> -->
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Aantal stoelen: <span id="summary_flight_seats"></span></p></div>
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Prijs per stoel: <span id="summary_flight_price"></span></p></div>
                                                            <div class="col summ-flight-deets"><p class="summary-body-txt">Prijs: <span id="summary_flight_total_price"></span></p></div>
                                                            <div class="col summ-flight-deets"></div>
                                                        </div>
                                                    </div>

                                                    <div id="summary_flight_div">
                                                    </div>


                                                </div>
                                                <div class="col-12 my-3 mob-hide summ-head-box">
                                                    <h3 class="form-label-blue"><span class="badge badge-highlight">08</span><span class="summ-heading">Verzekeringen</span></h3>
                                                </div>
                                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                    <div class="row form-fields-rows">
                                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                                            <p class="summary-table-head-subs">Verzekering</p>
                                                        </div>
                                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                                            <p class="summary-table-head-subs">Prijs</p>
                                                        </div>
                                                    </div>

                                                    <div id="summary_insurance_div">
                                                    </div>
    
                                                </div>
                                                <div class="col-12 my-3 box-padding-mob">
                                                    <h3 class="form-label-blue overigekost"><span class="summ-heading">Overige kosten<span class="summ-heading"></h3>
                                                </div>
                                                <div class="col-12">
                                                    <div class="row mb-1">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            SGR fee
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            <span class="summary-sub-headings-txt">+ €</span> <span id="booking_sgr_fee_div"></span> <span class="summary-sub-headings-txt">per persoon</span>
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                                <span>Totaal: €<span style="margin-left:1px;" id="booking_sgr_fee_total"></span></span>
                                                            
                                                            <span id="booking_sgr_fee_total"></span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-1">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            Administratiekosten verzekering
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            + <span id="booking_insurance_fee_div"></span> <span class="summary-sub-headings-txt">% per verzekering</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs">
                                                            Calamiteitenfonds
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">
                                                            <span class="summary-sub-headings-txt">+ €</span> <span id="booking_calamity_fund_div"></span> per 9 personen
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt">    
                                                            <span class="">Totaal: €</span> <span style="margin-left:1px;" id="booking_calamity_fund_total"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                                <div class="col-12">
                                                    <!-- <div class="col-8 col-sm-8 col-md-8 col-xl-8" style="width:40%;display: flex; flex-direction: column;align-items:flex-start;justify-content:flex-start">
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Verzekering<span id="insurance_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14x;">Calamiteitenfonds<span id="calamity_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">SGR fee<span id="sgrfee_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Boeking<span id="booking_summary" style="margin-left: 50px"></span></p>
                                                    </div> -->
                                                    <div class="row mb-2">
                                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black" style="font-size:18px;font-weight:bold;">
                                                            Totaal
                                                        </div>
                                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary">
                                                            <span id="summary_total_booking" style="font-size:17px;font-weight:bold;">0.00</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="box-padding-mob col-12 col-md-10 col-xl-10 booking-special-notes">
                                                <div class="form-group">
                                                    <label class="form-label summary-table-head-subs">Een speciaal bericht of notitie</label>
                                                    <textarea rows="3" placeholder="Vul hier uw bericht in..." name="special_message" id="special_message" type="text" class="form-control"></textarea>
                                                </div>
                                            </div> 
                                            <!-- <div class="box-padding-mob col-12 col-md-12">
                                                <div class="d-flex summary-create">
                                                    <div class="custom-checkbox">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="create_account" id="create_account" class="custom-control-input form-input-checkbox">
                                                            <label title="" for="create_account" class="custom-control-label"></label>
                                                        </div>
                                                        <label class="form-check-label">
                                                            <span class="checkbox-label ml-1">Een account aanmaken</span><i class="fa-solid fa-circle-info"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div> -->
                                            <div class="box-padding-mob col-12 col-xl-10 col-sm-10">
                                                <!--<div class="form-group">
                                                    <label class="form-label">E-mailadres</label>
                                                    <input type="email" placeholder="E-mail" name="email" value="" class="form-control">
                                                    <div class="invalid-feedback"></div>
                                                </div>-->
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-8 col-xl-8 col-12 mt-3 d-flex">
                                                <button type="submit" class="btn btn-link btn-block btn-form-step text-left" data-source="" data-target="">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                        <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                            <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                            <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                        </g>
                                                    </svg>
                                                    Rond de boeking af en betaal
                                                </button>   
                                            </div>
                                        </div>
                                </div>   

                            </div>
                        </div>
                    </div>
                    
                </div>

            </div>
        </div>
		<div class="col-sm-4 booking_rightcol sticky-parent">
                <!-- [Right column] -->
                <div class="booking-price sticky-column">
                    <div class="price"><span>Prijs</span>
                        <div class="price-value"><span id="total_booking">&euro; 0,00</span></div>
                    </div>
                    <!-- <div id="event_names">
                        <div class="row form-fields-rows">
                            <div class="col-md-6 col-lg-4 col-xl-4">
                                <p>Startbewijzen</p>
                            </div>
                            <div class="col-md-6 col-lg-8 col-xl-8">
                                <p>Aantal</p>
                            </div>
                        </div>
                        <div id="selected_events"></div>
                    </div> -->
                    <button type="submit" class="theme-btn btn btn-primary btn-form-step rightcol-submit-btn" data-source="" data-target="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                            <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                            </g>
                        </svg>Rond de boeking af & betaal
                    </button>
                </div>   
                <button id="sendmail" style="margin-top: 50px">Send Mail</button>         
            </div>
		
    </div>
</div>
                       