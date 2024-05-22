<?php
    define("GEMS_PLUGIN_DIR", plugin_dir_path(__FILE__));

    $eventkey = get_query_var( 'event', 'abc123XYZ456' );
    //$eventkey = '64cbb86c14fb2';
?>

<script>

    function validateEmail(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test( email );
    }

    function calculateInsurancePrice(adults_count, children_count, children_under_3_count, ins_price, ins_price_type, ins_price_per_participant, total_booking_price) {
 //console.log(adults_count);
 //console.log(children_count);
 //console.log(children_under_3_count);
 //console.log(ins_price);
 //console.log(ins_price_type);
 //console.log(ins_price_per_participant);
 //console.log(total_booking_price);
 //console.log('-----------------');
 
        var this_ins_price = 0,
            nights = 1; // bcoz for insurance need day count
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
				emailErrorDiv.html('Invalid email format');
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
				emailErrorDiv.html('Invalid email format');
			} else if (email !== confirm_email) {
                emailErrorDiv.html('Emails do not match');
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

            required_fields.each(function() { 
                if ($(this).val() !== '' && $(this).val() !== '0') {
                    completed_fields++;
                }
            });

            progress = (completed_fields / total_fields) * 100; 
            progress = Math.round(progress);
            $('#progress-bar').css('width', progress + '%').html(progress + '%');
        }

        // Update summaries stepss
        function updateSummaryStep1() {
            $('#summary_adults_count').html( $('#adults_count').val() );
            $('#summary_children_count').html( $('#children_count').val());
            $('#summary_children_under_3_count').html( $('#children_under_3_count').val() );
            $('#travellers_amount').val( parseInt($('#adults_count').val()) + parseInt($('#children_count').val()) + parseInt($('#children_under_3_count').val()) );	

        }

        function updateSummaryStep2() {
            var options = { year: 'numeric', month: 'short', day: 'numeric' },
                birthdate_visitor = new Date( $('#gl_dateofbirth').val() ),
                birthdate_stayathome = new Date( $('#sah_dateofbirth').val() ),
                title_visitor = $('#gl_title').select2('data'),
                title_stayathome = $('#sah_title').select2('data'),
                country_visitor = $('#gl_country').select2('data'),
                country_stayathome = $('#sah_country').select2('data');

            $('#booking_visitor_title_div').html( title_visitor[0].text );  
            $('#booking_visitor_name_div').html( $('#gl_first_name').val() + ' ' + $('#gl_middle_name').val() + ' ' + $('#gl_last_name').val() );
            $('#booking_visitor_address_div').html( $('#gl_street').val() + ' ' + $('#gl_house_number').val() + ', ' + $('#gl_residence').val() );
            $('#booking_visitor_birthdate_div').html( birthdate_visitor.toLocaleDateString("nl-NL", options)  + ' | ' + country_visitor[0].text );

            $('#booking_stayathome_title_div').html( title_stayathome[0].text );  
            $('#booking_stayathome_name_div').html( $('#sah_first_name').val() + ' ' + $('#sah_middle_name').val() + ' ' + $('#sah_last_name').val() );
            $('#booking_stayathome_address_div').html( $('#sah_street').val() + ' ' + $('#sah_house_number').val() + ', ' + $('#sah_residence').val() );
            $('#booking_stayathome_birthdate_div').html( birthdate_stayathome.toLocaleDateString("nl-NL", options) + ' | ' + country_stayathome[0].text );

            updateProgressBar();
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
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${bibs_name}</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>${bibs_count}</p>
                        </div>
                    </div>`);
                }
            });

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
                            <p>&euro; ${extra_price.toFixed(2)}</p>
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
                            <p>&euro; ${extra_price.toFixed(2)}</p>
                        </div>
                    </div>`);
                }
                
            });

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
                    children_under_3_count = $("#children_under_3_count").val();

                total_booking_price = 
                    parseFloat($("#total_bibs_price").val()) +
                    parseFloat($("#total_extra_price").val()) +
                    parseFloat($("#total_nonextra_price").val()) +
                    parseFloat($("#total_room_price").val()) +
                    parseFloat($("#total_flight_departure_price").val()) +
                    parseFloat($("#total_flight_arrival_price").val());

                this_ins_price = calculateInsurancePrice(adults_count, children_count, children_under_3_count, ins_price, ins_price_type, ins_price_per_participant, total_booking_price);

                if ($(this).is(":checked")) {

                    $price_label.html('&euro; ' + this_ins_price.toFixed(2));
                    $price_label.css('display', 'inline-block');

                    // Update summary
                    $('#summary_insurance_div').append(`<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>${ins_name}</p>
                        </div>
                        <div class="col-md-6 col-lg-8 col-xl-8">
                            <p>&euro; ${this_ins_price}</p>
                        </div>
                    </div>`);

                    total_insurance_price += this_ins_price;

                } else {
                    $price_label.css('display', 'none');
                }

            });
            $('#total_insurance_price').val(total_insurance_price.toFixed(2));
            $('#total_insurance').html('&euro; ' + total_insurance_price.toFixed(2));
            getTotals();
            updateProgressBar();
        }

        $(document).on('blur', '#form_section2 input, #form_section2 select', function(e) {
            updateSummaryStep2();
        });

        $(document).on('click', '.hotel-labels input', function(e) {
            var hotel_id = $(this).data('hotel_name'),
                hotel_name = $(this).data('hotel_name'),
                hotel_rating = $(this).data('rating'),
                hotel_photo = $(this).data('photo'),
                hotel_max_persons_per_room = $(this).data('max_persons_per_room'),
                hotel_price_from = $(this).data('price_from');

            $('#summary_hotel_name').html(hotel_name);      
        });

        $(document).on('click', '.insurance-options', function(e) {

            updateSummaryStep8();
        });

        // Check requited fields before moving to next step
        $(document).on('click', '.btn-form-step', function(e) {
            var stepType     = $(this).attr('type'), 
                sourceStep   = $(this).data('source'),
                targetStep   = $(this).data('target'),
                errorMessage = '';
            if (stepType === 'submit') {
                postBookingDetails();
            } else {
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
                    
                    if ( fieldName != "bibs_count[]" &&  fieldName != "hotel_room_count[]" && fieldType === 'number' && (fieldValue <= 0 || fieldValue == "" )) {
                        errorMessage += 'Please enter at least 1 number for ' + fieldTitle + '.<br/>';
                    } else if (fieldType === 'text' && fieldValue == "") {
                        errorMessage += 'Please fill out ' + fieldTitle + '.<br/>';
                    } else if (fieldType === 'textarea' && fieldValue == "") {
                        errorMessage += 'Please fill out ' + fieldTitle + '.<br/>';
                    } else if (fieldType === 'select-one') {
                        if (!fieldValue) {
                            errorMessage += 'Please select an option for ' + fieldTitle + '.<br/>';
                        }
                    } else if (fieldType === 'checkbox' || fieldType === 'radio') {
                        var fieldGroup = $(this).attr('name');
                        if ($('input[name="' + fieldGroup + '"]:checked').length === 0) {
                            errorMessage += 'Please choose an option for ' + fieldTitle + '.<br/>';
                        }
                    }

                });

                $( sourceStep + ' .error-message').html(errorMessage);

                if (errorMessage !== "") {
                    //alert('Please fill out all required fields.');

                    //e.stopPropagation();
                    $(sourceStep).addClass('show');
                    $(targetStep).removeClass('show');
                } else {
                    updateSummaryStep1();
                }

            }

        });

        // Increasing and decreasing the quantity (travellers, bibs, hotel rooms)
        $(document).on('click', '.plus-minus-input .button', function() {
            var $input = $(this).closest('.plus-minus-input').find('.input-group-field'),
            //  fieldName = $(this).data('field'),
                type = $(this).data('quantity'),
                currentValue = parseInt($input.val(), 10);
    
            if (!isNaN(currentValue)) {
                if (type === 'minus') {
                    if (currentValue > 0) {
                        $input.val(currentValue - 1);
                    }
                } else if (type === 'plus') {
                    $input.val(currentValue + 1);
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

            var travelers = parseInt(adults_count) + parseInt(children_count) + parseInt(children_under_3_count) - 1;
 
            if (parseInt(travelers) > 1) {
                addTravellerToForm(travelers);
            } else if (parseInt(travelers) < 2) {
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
            getTotals();

        });
    
        function removeTravellerToForm() {
            $('#runners_div .runner_info').last().remove();
        }

        function addTravellerToForm(i = 1) {
    
            var htmlToAdd = `
                <div class="runner_info">
                    <div class="row">
                        <div class="col-12">
                            <div class="visitor">
                                <div class="align-self-center">
                                    <p class="caption theme-color-secondary mb-0 form-label-blue">TRAVELLER #${i}</p>
                                </div>
                                <div class="mt-2">
                                    <div class="custom-control custom-checkbox">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" name="traveller_is_runner[]" id="traveller_is_runner_${i}" class="custom-control-input form-input-checkbox">
                                            <label title="" for="traveller_is_runner_${i}" class="custom-control-label"></label>
                                        </div>
                                        <label class="form-label">
                                            <span class="checkbox-label ml-2">Is TRAVELLER #${i} een hardloper?</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row form-fields-rows">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label field-label">Titel <span class="required">*</span></label>
                                <select placeholder="Titel" data-placeholder="Titel" name="v_title[]" id="v_title_${i}" class="pl-2 form-control form-select" required>
                                    <option value="dhr.">dhr.</option>
                                    <option value="mevr.">mevr.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">&nbsp;</div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                                <input class="form-control" type="date" id="v_dob_${i}" name="v_dob[]" placeholder="Date of birth" required>
                            </div>
                        </div>
                    </div>
                    <div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <label class="form-label field-label">Voornaam (volgens paspoort) <span class="required">*</span></label>
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
                                <label class="form-label field-label">Achternaam (volgens paspoort) <span class="required">*</span></label>
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
        }

        // Recalculate the total price when the number of travellers changes
        $(document).on("change",".flight-departure",function(){
            //alert(this.value);
            var travelers_amount = $('#travellers_amount').val(),
                departure_price = $(this).data('price');
            $('#total_flight_departure_price').val(departure_price * travelers_amount);

            getTotals();
        });

        // Recalculate the total price when the number of travellers changes
        $(document).on("change",".flight-arrival",function(){
            //alert(this.value);
            var travelers_amount = $('#travellers_amount').val(),
                arrival_price = $(this).data('price');
            $('#total_flight_arrival_price').val(arrival_price * travelers_amount);

            getTotals();
        });

        function getTotals() {
            var total_booking = parseFloat($('#total_bibs_price').val()) + 
                parseFloat($('#total_room_price').val()) + 
                parseFloat($('#total_extra_price').val()) + 
                parseFloat($('#total_nonextra_price').val()) + 
                parseFloat($('#total_flight_departure_price').val()) + 
                parseFloat($('#total_flight_arrival_price').val()) +
                parseFloat($('#total_insurance_price').val());
            $('#total_booking').html('&euro; ' + total_booking.toFixed(2));
        }

        // AJAX request to fetch countries list
        $.ajax({
            url: '<?php echo $data->api_endpoint; ?>/countries-list/',
            type: 'GET',
            success: function(response) {

                // Check if response is valid
                if (response && response.type === 'success') {
                    var countries = response.data;

                    // Populate select boxes with countries 
                    $.each(countries, function(index, country) {
                        // Append options to each select box

                        $('select[name="gl_country"]').append('<option value="' + country.id + '">' + country.name + '</option>');
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
            headers: {
                'e-key': '<?php echo $eventkey;?>',
                'merchant-key': '<?php echo $data->merchant_key; ?>'
            },
            success: function(response) {
                // Check if the response type is success
                if (response.type === 'success') {
                    // Access the event_settings object from the response data
                    var eventSettings = response.data.event_settings;

                    var booking_start_date = eventSettings.start_date;
                    var booking_end_date = eventSettings.end_date;
                    var booking_sgr_fee = eventSettings.sgr_fee;
                    var booking_insurance_fee = eventSettings.insurance_fee;
                    var booking_calamity_fund = eventSettings.calamity_fund;

                    var booking_date_info = booking_start_date + ' - ' + booking_end_date;
 
                    var start_date = $('#booking_start_date').val();
                    var end_date = $('#booking_end_date').val();

                    $("#booking_start_date").val(booking_start_date);
                    $("#booking_end_date").val(booking_end_date);

                    $("#booking_sgr_fee").val(booking_sgr_fee);
                    $("#booking_calamity_fund").val(booking_calamity_fund);
                    $("#booking_insurance_fee").val(booking_insurance_fee);

                    $("#booking_sgr_fee_div").html(booking_sgr_fee);
                    $("#booking_calamity_fund_div").html(booking_calamity_fund);
                    $("#booking_insurance_fee_div").html(booking_insurance_fee);

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
                    // Iterate over the data array in the response
                    $.each(response.data, function(index, item) {
                        // Generate the HTML content for each item

 
                            // Generate the HTML content for each item
                            bibs_html += `<div class="col-md-4 col-lg-4 col-sm-4 bibs-item">
                                <label class="hotel-labels">
                                    <div class="card card-default card-input">
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
                                    </div>
                                </label>

                                <div class="input-group plus-minus-input">
                                    <div class="input-group-button">
                                        <span class="button hollow circle value-button-room minus_room" data-bib-id="${item.id}" data-quantity="minus" data-field="quantity">
                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                        </span>
                                    </div>
                                    <input type="hidden" name="bibs_id[${item.id}]" id="bibs_id_${item.id}" value="${item.id}">
                                    <input placeholder="Bib" class="input-group-field bibs_count number" type="number" name="bibs_count[${item.id}]" id="bibs_count_${item.id}" value="0" required data-event-date="${item.event_date}" data-max-qty="${item.quantity}" data-bibs_name="${item.challenge_name}" data-price="${item.single_ticket_price}" data-bibs_count="0">
                                    <div class="input-group-button">												
                                        <span class="button hollow circle value-button-room  plus_room" data-bib-id="${item.id}" data-quantity="plus" data-field="quantity">
                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                        </span>											
                                    </div>
                                </div>

                            </div>`;                        

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

                            // Generate the HTML content for each item
                            hotels_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards">
                                <label class="hotel-labels">
                                    <input type="radio" class="card-input-element" name="hotel_id" value="${item.id}" data-hotel_name="${item.name}" data-rating="${item.rating}" data-photo="${item.photo_1}" data-max_persons_per_room="${item.max_persons_per_room}" data-price_from="${item.price_from}" />
                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header">
                                            <div class="card-title">${item.name}</div>
                                            <div class="card-title-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="43.548" viewBox="0 0 60 43.548">
                                                    <path id="hotel" d="M56.614,43.009l-1.935-1.631V30.231h1.935Zm5.308-16.064-3.15-7.36a.975.975,0,0,0-.89-.585H53.411a.963.963,0,0,0-.885.585l-3.15,7.36a.969.969,0,0,0,.89,1.35H61.032a.969.969,0,0,0,.89-1.35ZM16.661,38.161V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035H32.14V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035h4.984V33.1a5.344,5.344,0,0,0-5.337-5.337h-26.1A5.344,5.344,0,0,0,11.677,33.1v5.061ZM54.5,46.29h3.01l-.9-.755-1.935-1.626L50.14,40.1H9.989L2.615,46.29ZM6.355,59.3H4.419V61.58a.971.971,0,0,0,.968.968H10.1a.971.971,0,0,0,.968-.968V59.3ZM51,59.3H49.061V61.58a.971.971,0,0,0,.968.968h4.713a.971.971,0,0,0,.968-.968V59.3ZM3.935,48.226H2V56.4a.971.971,0,0,0,.968.968H57.161a.971.971,0,0,0,.968-.968V48.226Z" transform="translate(-2 -19)" fill="#0093cb" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">Max. ${item.max_persons_per_room} personen per kamer</div>
                                            <div class="card-body-price-heading">
                                                <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                                <div class="card-body-price">&#8364; ${item.price_from}</div>
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
                        // Iterate over the data array in the response
                        $.each(response.data, function(index, item) {

                            // Generate the HTML content for each item
                            hotel_rooms_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards-rooms  booking-card">

                                <label class="hotel-rooms-labels">
                                    <input type="radio" name="hotel_room_id" value="${item.hotel_room_id}" selected checked class="_card-input-element" />

                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header">
                                            <div class="card-title">${item.name}</div>
                                            <div class="card-title-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="60" height="43.548" viewBox="0 0 60 43.548">
                                                    <path id="hotel" d="M56.614,43.009l-1.935-1.631V30.231h1.935Zm5.308-16.064-3.15-7.36a.975.975,0,0,0-.89-.585H53.411a.963.963,0,0,0-.885.585l-3.15,7.36a.969.969,0,0,0,.89,1.35H61.032a.969.969,0,0,0,.89-1.35ZM16.661,38.161V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035H32.14V37.126a2.6,2.6,0,0,1,2.594-2.594h6.14a2.6,2.6,0,0,1,2.594,2.594v1.035h4.984V33.1a5.344,5.344,0,0,0-5.337-5.337h-26.1A5.344,5.344,0,0,0,11.677,33.1v5.061ZM54.5,46.29h3.01l-.9-.755-1.935-1.626L50.14,40.1H9.989L2.615,46.29ZM6.355,59.3H4.419V61.58a.971.971,0,0,0,.968.968H10.1a.971.971,0,0,0,.968-.968V59.3ZM51,59.3H49.061V61.58a.971.971,0,0,0,.968.968h4.713a.971.971,0,0,0,.968-.968V59.3ZM3.935,48.226H2V56.4a.971.971,0,0,0,.968.968H57.161a.971.971,0,0,0,.968-.968V48.226Z" transform="translate(-2 -19)" fill="#0093cb" />
                                                </svg>

                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price">&#8364; ${item.price}</div>
                                            </div>
                                            <div class="card-body-hotel-room-qty-sel rooms-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow circle value-button-room minus_room" data-bib-id="${item.id}" data-quantity="minus" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="hotel_room[]" id="hotel_room_${item.hotel_room_id}" value="${item.id}">
                                                    <input placeholder="Hotelroom" class="input-group-field rooms_count number" type="number" name="hotel_room_count[]" id="hotel_room_count_${item.id}" value="0" required data-room_name="${item.name}" data-quantity="${item.quantity}" data-price="${item.price}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow circle value-button-room plus_room" data-bib-id="${item.id}" data-quantity="plus" data-field="quantity">
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
                    // Iterate over the data array in the response
                    $.each(response.data, function(index, item) {

                        var extras_description = item.extras_description ?? '-';

                        if(item.related_product_category == 1){ //1 = hotel extras
                            
                            hotel_extras_html += `<div class="col-md-4 col-lg-4 col-sm-4 col-radio-btn-cards-rooms booking-card">
                                <label class="hotel-rooms-labels">
                                    <input type="radio" name="product" selected checked class="card-input-element" />
                                    <div class="card card-default card-input">
                                        <div class="card-header hotels-details-header">
                                            <div class="card-title">${item.name}</div>											
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">${extras_description}</div>
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price">&#8364; ${item.price}</div>
                                            </div> 
                                            <div class="card-body-hotel-room-qty-sel hotelextra-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow circle value-button-room minus_room" data-hotel_extras_id="${item.id}" data-quantity="minus" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="hotel_extras[]" id="hotel_extras_${item.id}" value="${item.id}">
                                                    <input placeholder="Extra hotel" class="input-group-field extra_count number" type="number" name="extras_count[]" id="extras_count_${item.id}" value="0" required data-extras_name="${item.name}" data-price="${item.price}" data-related_product_category="${item.related_product_category}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow circle value-button-room plus_room" data-hotel_extras_id="${item.id}" data-quantity="plus" data-field="quantity">
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
                                        <div class="card-header hotels-details-header">
                                            <div class="card-title">${item.name}</div>											
                                        </div>
                                        <div class="card-body">
                                            <div class="card-body-descr">${extras_description}</div>
                                            <div class="card-body-price-heading">
                                            <div class="card-body-price-vanaf-sub">Prijs vanaf</div>
                                            <div class="card-body-price">&#8364; ${item.price}</div>
                                            </div> 
                                            <div class="card-body-hotel-room-qty-sel nonhotelextra-item">
                                                <div class="input-group plus-minus-input">
                                                    <div class="input-group-button">
                                                        <span class="button hollow circle value-button-room minus_room" data-hotel_extras_id="${item.id}" data-quantity="minus" data-field="quantity">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </span>
                                                    </div>
                                                    <input type="hidden" name="nonhotel_extras[]" id="hotel_extras_${item.id}" value="${item.id}">
                                                    <input placeholder="Extra non hotel" class="input-group-field nonextra_count number" type="number" name="extras_count[]" id="extras_count_${item.id}" value="0" required data-extras_name="${item.name}" data-total_quantity="${item.total_quantity}" data-total_quantity="${item.total_quantity}" data-price="${item.price}" data-related_product_category="${item.related_product_category}">
                                                    <div class="input-group-button">												
                                                        <span class="button hollow circle value-button-room  plus_room" data-hotel_extras_id="${item.id}" data-quantity="plus" data-field="quantity">
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
                        $.each(j.flight_info, function(index, item) {
                            // Access flight info details
                            var options = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' };
                            var flight_departure_date = new Date(item.departure_date),
                                flight_arrival_date = new Date(item.arrival_date);
                            
                            if(item.route == 'D') {  //departure
            
                                // Construct HTML for flight info
                                flightInfoHtml += `<div class="flight-details-box-row">
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
                                    <div class="flight-seat-select-box">
                                        <h5 class="heenvlucht-details">Reisklasse</h5>
                                        <select placeholder="Reisklasse" data-placeholder="Reisklasse" name="flight-seat-flight-one" id="flight-seat-flight-one" class="form-select flight-departure">
                                            <option value="" data-price="0.00" disabled selected>Reisklasse</option>                                            
                                            <option value="Economy" data-price="${item.economy_ticket_price}">Economy class - &#8364; ${item.economy_ticket_price}</option>
                                            <option value="Comfort" data-price="${item.comfort_ticket_price}">Comfort class - &#8364; ${item.comfort_ticket_price}</option>
                                            <option value="Business" data-price="${item.business_ticket_price}">Business class - &#8364; ${item.business_ticket_price}</option>
                                        </select>
    
                                    </div>
                                </div>`;
            
                            }else if(item.route == 'H') {   //arrival
            
                                // Construct HTML for flight info
                                returnflightInfoHtml += `<div class="flight-details-box-row">
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
                                    <div class="flight-seat-select-box">
                                        <h5 class="heenvlucht-details">Reisklasse</h5>
                                        <select placeholder="Reisklasse" data-placeholder="Reisklasse" name="flight-seat-flight-one" id="flight-seat-flight-one" class="form-select flight-arrival">
                                            <option value="" data-price="0.00" disabled selected>Reisklasse</option> 
                                            <option value="Economy" data-price="${item.economy_ticket_price}">Economy class - &#8364; ${item.economy_ticket_price}</option>
                                            <option value="Comfort" data-price="${item.comfort_ticket_price}">Comfort class - &#8364; ${item.comfort_ticket_price}</option>
                                            <option value="Business" data-price="${item.business_ticket_price}">Business class - &#8364; ${item.business_ticket_price}</option>
                                        </select>
                                    </div>
                                </div>`
    
                            }
            
                        });
            
                        // Construct HTML for flight plan
                        var flightPlanHtml = `
                        <div class="col vervoer-radio-btn-group">
                            <label class="vervoer-radio-btn-label">
                                <input type="radio" name="transport[]" id="transport_${index}" value="${index}" data-fp_id="${j.fp_id}" data-planName="${j.plan_name}" data-price="${j.price}" data-standard_luggage_weight="${j.standard_luggage_weight}" data-customer_pays_for_hand_luggage="${j.customer_pays_for_hand_luggage}" data-hand_luggage_price="${j.hand_luggage_price}" data-customer_book_extra_luggage="${j.customer_book_extra_luggage}" data-extra_luggage_weight="${j.extra_luggage_weight}" data-extra_luggage_price="${j.extra_luggage_price}" class="card-input-element" />
                                <div class="card card-default card-input">
                                    <div class="card-header">
                                        <div class="card-title">${j.plan_name}</div>
                                        <div class="card-title-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M21 16V14L13 9V3.5C13 2.67 12.33 2 11.5 2C10.67 2 10 2.67 10 3.5V9L2 14V16L10 13.5V19L8 20.5V22L11.5 21L15 22V20.5L13 19V13.5L21 16Z" fill="#0093cb"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="heenvlucht-details">
                                            <h5>Heenvlucht</h5>
                                        </div>` + flightInfoHtml + `
                                        
                                        <div class="heenvlucht-details">
                                            <h5>Retourvlucht</h5>
                                        </div>` + returnflightInfoHtml + `
            
                                        <div class="flight-baggage">
                                            <h5>Bagage</h5>
                                        </div>
                                        <div class="flight-details-box-row">
                                            <div class="col-flight-baggage-details">
                                                <div class="flight-details-bagage-weight">Maximaal ${j.extra_luggage_weight} kg.</div>
                                                <div class="extra-bagage-check">
                                                    <input class="extra-bagage-check form-input-checkbox" type="checkbox" id="extra_bagage_${j.fp_id}" name="extra_bagage_${j.fp_id}" data-price="${j.extra_luggage_price}" />
                                                    <label for="extra-bagage" class="extra-bagage-check-label">Extra bagage</label>
                                                </div>
                                            </div>
                                        </div>
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

                        total_booking_price = 
                            parseFloat($("#total_bibs_price").val()) +
                            parseFloat($("#total_extra_price").val()) +
                            parseFloat($("#total_nonextra_price").val()) +
                            parseFloat($("#total_room_price").val()) +
                            parseFloat($("#total_flight_departure_price").val()) +
                            parseFloat($("#total_flight_arrival_price").val());

                        this_ins_price = calculateInsurancePrice(adults_count, children_count, children_under_3, ins_price, ins_price_type, ins_price_per_participant, total_booking_price);

                        var isOptionChecked	= '';
                        if(parseInt(item.default_ticked) == 1){
                            isOptionChecked	= ' checked="checked"';
                            isPriceHidden =  '';
                            total_insurance += parseFloat(item.price);

                            $('#summary_insurance_div').append(`<div class="row form-fields-rows">
                                <div class="col-md-6 col-lg-4 col-xl-4">
                                    <p>${item.insurance_name}</p>
                                </div>
                                <div class="col-md-6 col-lg-8 col-xl-8">
                                    <p>${this_ins_price}</p>
                                </div>
                            </div>`);


                        } else {
                            isPriceHidden =  'style="display:none;"';
                        }
                        // Construct HTML for flight plan
                        insuranceHtml += `<tr class="type-verzekering-body">
                            <td class="type-verzekering-col">
                                <div class="insurance-options-check">
                                    <input class="insurance-options form-input-checkbox" type="checkbox" name="insurance_id[${index + 1}]" data-name="${item.insurance_name}" ${isOptionChecked} data-price="${item.price}" data-price_type="${item.price_type}" data-price_per_participant="${item.price_per_participant}" value="1" />
                                    <label for="extra-bagage" class="insurancetype-check-label">${item.insurance_name}</label>
                                </div>
                                <div class="verzekering-description-box">
                                    <p>${item.information}</p>
                                </div>
                            </td>
                            <td class="verzekering-optie-col">
                                <span id="insurance_option${index + 1}" class="insurance_option" ${isPriceHidden}>&euro; ${item.price}</span>
                            </td>
                        </tr>`;
                    });
 
                    // Append all insurance HTML to container
                    $('#insurances_container').html(insuranceHtml);
                    $('#total_insurance_price').val(total_insurance.toFixed(2));
                    $('#total_insurance').html('&euro; ' + total_insurance.toFixed(2));

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

        function mailBookingData(bookingData){
            var url = "<?php echo admin_url('admin-ajax.php'); ?>";
            $.ajax({
                method: "POST",
                dataType: "json",
                url: url,
                data: { action: 'mail_booking_details', bookingData: bookingData },
                success: function(data) {
                    var result = JSON.parse(data);
                    console.log('booking details mailed successfully!');
                },
                error: function(xhr, status, error) {
                    if(xhr.status == 200)
                        alert('booking details mailed successfully!');
                    else
                        alert('Error sending email');
                    console.error('Error sending email:', error);
                }
            });
        }

        // Function to post booking details
        function postBookingDetails() {
            // Capture values of fields in variables
            var adultsCount 				= $('#adults_count').val();
            var childrenCount 				= $('#children_count').val();
            var childrenUnder3Count 		= $('#children_under_3_count').val();
        
            var arrivalDate 				= $('#arrival_date').val();
            var departureDate 				= $('#departure_date').val();
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
            var bookingPrice 				= $('#booking_price').val();
        
        /*
        - gl_is_runner
        - gl_title
        - gl_first_name
        - gl_middle_name
        - gl_last_name
        - gl_dob
        - gl_country
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
            data += '&hotel_id=' + hotelId;
            data += '&' + roomtypeFormData;
            data += '&' + extrasFormData;
            data += '&flight_plan_id=' + flightPlanId;
            data += '&' + insuranceFormData;
            data += '&special_message=' + specialMessage;
            data += '&booking_price=' + bookingPrice;
            
            var options = { year: 'numeric', month: 'short', day: 'numeric' };
            var birthdate_visitor = new Date( $('#gl_dateofbirth').val() );
            var country_visitor = $('#gl_country').select2('data');
            var title_stayathome = $('#sah_title').select2('data');
            var country_visitor = $('#gl_country').select2('data');
            var country_stayathome = $('#sah_country').select2('data');
            var bibs = []
            var birthdate_stayathome = new Date( $('#sah_dateofbirth').val() );
            $( '#form_section3 input.bibs_count' ).each(function(){
                bibs.push($(this).data)
            })
            var start_date = $('#booking_start_date').val();
            var end_date = $('#booking_end_date').val();
            var eventSettings = response.data.event_settings;
            var booking_start_date = eventSettings.start_date;
            var booking_end_date = eventSettings.end_date;
            var booking_sgr_fee = eventSettings.sgr_fee;
            var booking_insurance_fee = eventSettings.insurance_fee;
            var booking_calamity_fund = eventSettings.calamity_fund;
            var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' },
                        options_summary = { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' },
                        start_date = new Date(booking_start_date),
                        end_date = new Date(booking_end_date);

            var booking_date_info = booking_start_date + ' - ' + booking_end_date;

            var bookingData = {
                adults_count: $('#adults_count').val(),
                children_count: $('#children_count').val(),
                children_under_3_count: $('#children_under_3_count').val(),
                visitor_address: $('#gl_street').val() + ' ' + $('#gl_house_number').val() + ', ' + $('#gl_residence').val(),
                gl_title: $('#gl_title').select2('data')[0].text,
                birthdate_visitor: birthdate_visitor.toLocaleDateString("nl-NL", options)  + ' | ' + country_visitor[0].text
                country_stayathome: title_stayathome[0].text,
                booking_stayhome_name: $('#sah_first_name').val() + ' ' + $('#sah_middle_name').val() + ' ' + $('#sah_last_name').val(),
                booking_stayathome_address_div: $('#sah_street').val() + ' ' + $('#sah_house_number').val() + ', ' + $('#sah_residence').val(),
                booking_stayathome_birthdate_div: birthdate_stayathome.toLocaleDateString("nl-NL", options) + ' | ' + country_stayathome[0].text,
                summary_bibs: bibs,
                summary_departure_date: $('#summary_departure_date').text(start_date.toLocaleDateString("nl-NL", options_summary)),
                summary_arrival_date: $('#summary_arrival_date').text(end_date.toLocaleDateString("nl-NL", options_summary)),
            }
            console.log('bookingData', bookingData);

            // Ajax call to post data
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
                    console.log(response);
                    mailBookingData(bookingData);
                    alert('Booking details posted successfully!');
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.log('data', data);
                    console.error('Error posting booking details:', error);
                    if(xhr.status == 200)
                        mailBookingData(bookingData);
                    alert('Error posting booking details. Please try again.');
                }
            });
        }



    });
</script>

<div class="booking_section">
    <div class="container">
        <div class="row">
            <div class="col-12 sticky-progress">
                <div class="mt-4">
                    <div class="progress">
                        <div role="progressbar" id="progress-bar" class="progress-bar _bg-info _progress-bar-striped" style="width:0%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">0%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <!-- [Left column] -->
                    
                    <div class="accordion full-form-container" id="booking_form">
                        <!-- #01 Aantal bezoekers -->
                        <div class="card">
                            <div class="card-header" id="heading1">
                                <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#form_section1" aria-expanded="true" aria-controls="form_section1">
                                    <span class="steps body-18 regular-400">01</span>
                                    Aantal bezoekers
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
                                                    <div class="input-group plus-minus-input">
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
                                                    <div class="input-group plus-minus-input">
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
                                                    <label class="form-label form-label-blue">Kinderen</label>
                                                    <div class="input-group plus-minus-input">
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

                                            <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section2" data-source="#form_section1" aria-expanded="true" aria-controls="form_section2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                    <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                        <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                        <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                    </g>
                                                </svg>
                                                Ga door
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
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section2" aria-expanded="false" aria-controls="form_section2">
                                <span class="steps body-18 regular-400 numb">02</span>
                                Reizigers aanmaken
                            </button>
                            </h2>
                        </div>
                        <div id="form_section2" class="collapse" aria-labelledby="heading2" data-parent="#booking_form">
                            <div class="card-body">

                                <!-- Error Message Display -->
                                <div class="error-message text-danger"></div>
                                
                                <form name="gl_form" id="gl_form" method="POST">

                                    <input type="hidden" name="travellers_amount" id="travellers_amount" value="1">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="visitor">
                                                <div class="align-self-center">
                                                    <p class="caption theme-color-secondary mb-0 form-label-blue">Groepsleider</p>
                                                </div>

                                                <div class="mt-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="gl_is_runner" id="gl_is_runner" class="form-input-checkbox">
                                                            <label title="" for="gl_is_runner" class="custom-control-label"></label>
                                                        </div>
                                                        <label class="form-label">
                                                            <span class="checkbox-label ml-2">Is de groepsleider een hardloper?</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Titel <span class="required">*</span></label>
                                                <select placeholder="Titel" data-placeholder="Titel" name="gl_title" id="gl_title" class="pl-2 form-control form-select" required>
                                                    <option value="dhr.">dhr.</option>
                                                    <option value="mevr.">mevr.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4 col-12"></div>
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                                                <input class="form-control" type="date" id="gl_dateofbirth" name="dateofbirth" placeholder="Geboortedatum" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 col-xl-4 col-12">
                                            <div class="form-group">
                                                <label class="form-label field-label">Voornaam (volgens paspoort) <span class="required">*</span></label>
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
                                                <label class="form-label field-label">Achternaam (volgens paspoort) <span class="required">*</span></label>
                                                <input type="text" placeholder="Achternaam" name="gl_last_name" id="gl_last_name" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="runners_div">

                                    </div>

                                    <div class="row">
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

                                    <div class="row">
                                        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Postcode <span class="required">*</span></label>
                                                <input type="text" placeholder="Postcode" name="gl_postal_code" id="gl_postal_code" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                                            <div class="form-group">
                                                <label class="form-label field-label">Woonplaats <span class="required">*</span></label>
                                                <input type="text" placeholder="Woonplaats" name="gl_residence" id="gl_residence" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
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

                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                            <label class="field-label">Vast telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Vast telefoonnummer" name="gl_fixed_phone" id="gl_fixed_phone" class="form-control" required>
                                        </div>
                                        <div class="col-sm-6 col-sm-6 col-lg-6 col-12">
                                            <label class="field-label">Mobiel telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Mobiel telefoonnummer" name="gl_mobile" id="gl_mobile" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12 email-field">
                                            <label class="field-label">E-mailadres <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres" name="gl_email" id="gl_email" class="form-control email" required>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12 email-field">
                                            <label class="field-label">E-mailadres bevestigen <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres bevestigen" name="gl_email_confirm" id="gl_email_confirm" class="form-control confirm-email" required>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                    </div>

                                </form>

                                <form name="sah_form" id="sah_form" method="POST">
                                    <p class="caption theme-color-secondary mb-0 form-label-blue">Thuisblijversinformatie</p>
                                    <p class="body-text-regular">Deze persoon wordt gecontacteerd in geval van nood</p>

                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Titel <span class="required">*</span></label>
                                                <select placeholder="Titel" data-placeholder="Titel" name="sah_title" id="sah_title" class="pl-2 form-control form-select" required>
                                                    <option value="dhr.">dhr.</option>
                                                    <option value="mevr.">mevr.</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4"></div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Geboortedatum <span class="required">*</span> </label>
                                                <input class="form-control" type="date" id="sah_dateofbirth" name="sah_dateofbirth" placeholder="Geboortedatum" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Voornaam (volgens paspoort) <span class="required">*</span></label>
                                                <input type="text" placeholder="Voornaam" name="sah_first_name" id="sah_first_name" class="form-control" required>
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
                                                <label class="form-label field-label">Achternaam (volgens paspoort) <span class="required">*</span></label>
                                                <input type="text" placeholder="Achternaam" name="sah_last_name" id="sah_last_name" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-8">
                                            <div class="form-group">
                                                <label class="form-label field-label">Straat <span class="required">*</span></label>
                                                <input type="text" placeholder="Straat" name="sah_street" id="sah_street" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Huisnummer <span class="required">*</span></label>
                                                <input type="text" placeholder="Huisnummer" name="sah_house_number" id="sah_house_number" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-fields-rows">
                                        <div class="col-md-4 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Postcode <span class="required">*</span></label>
                                                <input type="text" placeholder="Postcode" name="sah_postal_code" id="sah_postal_code" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-lg-8 col-xl-8">
                                            <div class="form-group">
                                                <label class="form-label field-label">Woonplaats <span class="required">*</span></label>
                                                <input type="text" placeholder="Woonplaats" name="sah_residence" id="sah_residence" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-fields-rows">
                                        <div class="col-md-12 col-lg-4 col-xl-4">
                                            <div class="form-group">
                                                <label class="form-label field-label">Land <span class="required">*</span></label>
                                                <select placeholder="Land" data-placeholder="Land" class="form-control form-select" name="sah_country" id="sah_country" required>
                                                    <option value="">Land</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                            <label class="field-label">Vast telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Vast telefoonnummer" name="sah_fixed_phone" id="sah_fixed_phone" class="form-control" required>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                            <label class="field-label">Mobiel telefoonnummer <span class="required">*</span></label>
                                            <input type="tel" placeholder="Mobiel telefoonnummer" name="sah_mobile" id="sah_mobile" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                            <label class="field-label">E-mailadres <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres" name="sah_email" id="sah_email" class="form-control email" required>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-lg-6 col-12">
                                            <label class="field-label">E-mailadres bevestigen <span class="required">*</span></label>
                                            <input type="email" placeholder="E-mailadres bevestigen" name="sah_email_confirm" id="sah_email_confirm" class="form-control confirm-email" required>
                                            <div class="email-error text-danger"></div>
                                        </div>
                                    </div>

                                </form>

                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section3" data-source="#form_section2" aria-expanded="true" aria-controls="form_section3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
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
                                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section3" aria-expanded="false" aria-controls="form_section3">
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
                                        <input type="hidden" id="total_bibs_count" value="0">
                                        <input type="hidden" id="total_bibs_price" value="0.00">          

                                        <div class="row" id="bibs_div">										
                                            
                                        </div>
            
                                    </form>

                                    <div class="row">
                                        <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                            <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section4" data-source="#form_section3" aria-expanded="true" aria-controls="form_section4">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                    <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                        <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                        <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                    </g>
                                                </svg>
                                                Ga door
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
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section4" aria-expanded="false" aria-controls="form_section4">
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

                                    <input type="hidden" id="booking_start_date" name="booking_start_date" value="">
                                    <input type="hidden" id="booking_end_date" name="booking_end_date" value="">

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

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section5" data-source="#form_section4" aria-expanded="true" aria-controls="form_section5">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
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
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section5" aria-expanded="false" aria-controls="form_section5">
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
 
                                    <input type="hidden" id="total_room_count" value="0">
                                    <input type="hidden" id="total_room_price" value="0.00">                                     
                                    
                                    <div class="row radio-btn-grp-row" id="hotels_container">
        
                                    </div>

                                    <div class="selecteer-kamer-title">Selecteer kamers</div>
                                    <div class="row radio-btn-grp-row" id="hotel_rooms_container">

                                    </div>                                    
        
                                </form>
 
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section6" data-source="#form_section5" aria-expanded="true" aria-controls="form_section6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
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
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section6" aria-expanded="false" aria-controls="form_section6">
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
        
                                        <label class="card-title">Hotel extras</label>		
                                        <div class="radio-btn-grp-row" id="hotel_extras_details_div"></div>

                                        <label class="card-title">Non-Hotel extras</label>		
                                        <div class="radio-btn-grp-row" id="non_hotel_extras_details_div"></div>
        
                                    </div>
                                </form>   
                                
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section7" data-source="#form_section6" aria-expanded="true" aria-controls="form_section7">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
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
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section7" aria-expanded="false" aria-controls="form_section7">
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
                                    <input type="hidden" id="total_flight_departure_price" value="0.00">
                                    <input type="hidden" id="total_flight_arrival_price" value="0.00">                                        
                                    
                                    <div class="row">
    
                                        <div id="flights_container"></div>
    
                                        <div class="col vervoer-radio-btn-group">
    
                                            <label class="vervoer-radio-btn-label">
                                                <input type="radio" name="transport[]" class="card-input-element" />
    
                                                <div class="card card-default card-input">
                                                    <div class="card-header">
                                                        <div class="card-title">Eigen vervoer</div>
                                                        <div class="card-title-icon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="33.333" viewBox="0 0 50 33.333">
                                                                <path id="car" d="M65.732,109.956c-.312-.521-3.4-1.853-3.4-1.853.536-.277.9-.334.9-1.48,0-1.25-.006-1.667-.84-1.667H59.572c-.011-.025-.024-.051-.035-.077-1.825-3.985-2.07-4.993-4.792-6.349-3.651-1.816-10.5-1.907-13.179-1.907s-9.528.092-13.176,1.907c-2.725,1.354-2.657,2.051-4.792,6.349a.4.4,0,0,1-.042.077h-2.83c-.827,0-.833.417-.833,1.667,0,1.146.367,1.2.9,1.48a29.7,29.7,0,0,0-3.4,1.853c-.417.417-.833,3.333-.833,8.333s.417,10,.417,10h1.244c0,1.458.215,1.667.84,1.667H27.4c.625,0,.833-.208.833-1.667H54.9c0,1.458.208,1.667.833,1.667h8.542c.417,0,.625-.312.625-1.667h1.25s.417-5.1.417-10-.521-7.812-.833-8.333Zm-37.785,4.681a53.836,53.836,0,0,1-5.712.319c-2.127,0-2.2.136-2.35-1.192a7.517,7.517,0,0,1,.053-1.824l.066-.318h.313a14.366,14.366,0,0,1,4.641.706A10.208,10.208,0,0,1,28.09,113.9a1.5,1.5,0,0,1,.558.642Zm25.746,7.5-.46,1.152H29.9s.041-.064-.521-1.165c-.417-.815.1-1.335.928-1.631a34.66,34.66,0,0,1,11.259-2.2,29.5,29.5,0,0,1,11.3,2.2c.573.3,1.284.5.825,1.65ZM26.922,107.916a10.03,10.03,0,0,1-1.01.007c.272-.483.423-1.022.689-1.584.833-1.771,1.786-3.775,3.483-4.62,2.452-1.221,7.534-1.771,11.482-1.771s9.03.546,11.482,1.771c1.7.845,2.646,2.85,3.483,4.62.268.568.417,1.11.7,1.6-.208.011-.448,0-1.02-.02Zm36.221,5.845c-.223,1.3-.015,1.2-2.246,1.2a53.839,53.839,0,0,1-5.713-.319.447.447,0,0,1-.144-.74,9.776,9.776,0,0,1,3.134-1.569,13.5,13.5,0,0,1,4.7-.7.335.335,0,0,1,.322.313,7.3,7.3,0,0,1-.051,1.82Z" transform="translate(-16.565 -96.623)" fill="#00adef" />
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
    
                                    </div>
    
                                </form>  
                                
                                <div class="row">
                                    <div class="col-md-4 col-xl-4 col-12 mt-3 d-flex">

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section8" data-source="#form_section7" aria-expanded="true" aria-controls="form_section8">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
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
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section8" aria-expanded="false" aria-controls="form_section8">
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
                                            <p class="body-14 regular-400  text-black"></p>
                                            <p class="body-14 regular-400  text-black" style="display:block"></p>
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
                                                            <td class="tfoot-col-left-label">Totaal</td>
                                                            <td class="tfoot-col-right-amount"><span id="total_insurance">0.00</span></td>
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

                                        <button class="btn btn-link btn-block btn-form-step text-left" type="button" data-toggle="collapse" data-target="#form_section9" data-source="#form_section8" aria-expanded="true" aria-controls="form_section9">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                                                <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                                    <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                                    <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                                                </g>
                                            </svg>
                                            Ga door
                                        </button>   

                                    </div>                   
                                </div>                                

                            </div>
                        </div>
                        </div>
                    
                        <!-- #09 Samenvatting & betaling -->
                        <div class="card">
                        <div class="card-header" id="heading9">
                            <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#form_section9" aria-expanded="false" aria-controls="form_section9">
                                <span class="steps body-18 regular-400 numb">09</span>
                                /* The above code is a comment in PHP. Comments are used to add
                                explanations or notes to the code for better understanding. In this
                                case, the comment is a multi-line comment enclosed between /* and */
                                symbols. It is not executable code and is ignored by the PHP
                                interpreter. */
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
                                            <div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Volwassene(n)</p>
                                                        <span id="summary_adults_count">0</span>    
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Kinderen</p>
                                                        <span id="summary_children_count">0</span>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Baby's</p>
                                                        <span id="summary_children_under_3_count">0</span>
                                                    </div>                                                    
                                                </div>
                                            </div>
                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">02</span><span class="summ-heading"><!-- -->Bezoekersinformatie</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Naam</p>
                                                        Groepsleider: <span id="booking_visitor_title_div"></span>&nbsp;<span id="booking_visitor_name_div"></span><br>   
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Contactgegevens</p>
                                                        <div class="d-flex">
                                                            <div class="mr-2">
                                                                <i class="fa-solid fa-location-dot"></i>
                                                            </div>
                                                            <div class="address"><span id="booking_visitor_address_div"></span><br></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Geboortedatum &amp; Nationaliteit</p>
                                                        <span id="booking_visitor_birthdate_div">
                                                    </div>                                                    
                                                </div>

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Naam</p>
                                                        Thuisblijver: <span id="booking_stayathome_title_div"></span>&nbsp;<span id="booking_stayathome_name_div"></span><br>   
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Contactgegevens</p>
                                                        <div class="d-flex">
                                                            <div class="mr-2">
                                                                <i class="fa-solid fa-location-dot"></i>
                                                            </div>
                                                            <div class="address"><span id="booking_stayathome_address_div"></span><br></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Geboortedatum &amp; Nationaliteit</p>
                                                        <span id="booking_stayathome_birthdate_div">
                                                    </div>                                                    
                                                </div>

                                            </div>
                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">03</span><span class="summ-heading"><!-- -->Startbewijzen</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Challenge</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-8 col-xl-8">
                                                        <p>Aantal startbewijzen</p>
                                                    </div>
                                                </div>

                                                <div id="summary_bibs_div">
                                                </div>

                                            </div>

                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading"><!-- -->Datums</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-8 col-xl-8">
                                                        <p>Vertrek</p>
                                                        <span id="summary_departure_date">-</span>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Aankomst</p>
                                                        <span id="summary_arrival_date">-</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">05</span><span class="summ-heading">Hotel</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Hotel naam</p>
                                                        <span id="summary_hotel_name">-</span>
                                                    </div>
                                                    <div class="col-md-6 col-lg-8 col-xl-8">
                                                        <p>Prijs</p>
                                                        <span id="summary_room_price">-</span>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">06</span><span class="summ-heading">Extra's</span></h3>
                                            </div>
                                            <div class="col-12 mob-hide">
                                                <h4 class="body-14  regular-400 gray-1 mb-1">Extra's van hotel</h4>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Opties</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Personen</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Prijs</p>
                                                    </div>
                                                </div>

                                                <div id="summary_extra_div">
                                                </div>

                                            </div>
                                            <div class="col-12 mt-3 mob-hide">
                                                <h4 class="body-14  regular-400 gray-1 mb-1">Extra's buiten het hotel</h4>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Opties</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Personen</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Prijs</p>
                                                    </div>
                                                </div>

                                                <div id="summary_nonextra_div">
                                                </div>


                                            </div>
                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">07</span><span class="summ-heading"><!-- -->Transport</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Vlucht</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Vertrek</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Aankomst</p>
                                                    </div>
                                                </div>

                                                <div id="summary_flight_div">
                                                </div>


                                            </div>
                                            <div class="col-12 my-3 mob-hide summ-head-box">
                                                <h3 class="form-label-blue"><span class="badge badge-highlight">08</span><span class="summ-heading"><!-- -->Verzekering</span></h3>
                                            </div>
                                            <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                                <div class="row form-fields-rows">
                                                    <div class="col-md-6 col-lg-4 col-xl-4">
                                                        <p>Verzekering</p>
                                                    </div>
                                                    <div class="col-md-6 col-lg-8 col-xl-8">
                                                        <p>Prijs</p>
                                                    </div>
                                                </div>

                                                <div id="summary_insurance_div">
                                                </div>
 
                                            </div>
                                            <div class="col-12 my-3 box-padding-mob">
                                                <h3 class="form-label-blue">Overige kosten</h3>
                                            </div>
                                            <div class="col-12">
                                                <div class="row mb-1">
                                                    <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        SGR fee
                                                    </div>
                                                    <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        + € <span id="booking_sgr_fee_div"></span> per persoon
                                                    </div>
                                                </div>
                                                <div class="row mb-1">
                                                    <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        Administratiekosten verzekering
                                                    </div>
                                                    <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        + <span id="booking_insurance_fee_div"></span> % per verzekering
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        Calamiteitenfonds
                                                    </div>
                                                    <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                                        + € <span id="booking_calamity_fund_div"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="col-12">
                                                <div class="row mb-2">
                                                    <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black">
                                                        Totaal
                                                    </div>
                                                    <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary">
                                                        € <span>0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <hr>
                                            </div>
                                            <div class="box-padding-mob col-12 col-md-10 col-xl-10">
                                                <div class="form-group">
                                                    <label class="form-label">Een speciaal bericht of notitie</label>
                                                    <textarea rows="3" placeholder="Vul hier uw bericht in..." name="special_message" id="special_message" type="text" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="box-padding-mob col-12 col-md-12">
                                                <div class="d-flex summary-create">
                                                    <div class="custom-checkbox">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" name="create_account" id="create_account" class="custom-control-input form-input-checkbox">
                                                            <label title="" for="create_account" class="custom-control-label"></label>
                                                        </div>
                                                        <label class="form-check-label">
                                                            <span class="checkbox-label ml-1">Een account aanmaken</span><i class="fa-solid fa-circle-info"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="box-padding-mob col-12 col-xl-10 col-sm-10">
                                                <div class="form-group">
                                                    <label class="form-label">E-mailadres</label>
                                                    <input type="email" placeholder="E-mail" name="email" value="" class="form-control">
                                                    <div class="invalid-feedback"></div>
                                                </div>
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
            <div class="col-sm-4">
                <!-- [Right column] -->
                <div class="booking-price">
                    <div class="price"><span>Prijsindicatie</span>
                        <div class="price-value"><span id="total_booking">&euro; 0.00</span></div>
                    </div>
                    <button type="submit" class="theme-btn btn btn-primary btn-form-step" data-source="" data-target="">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16.667" height="16.871" viewBox="0 0 16.667 16.871">
                            <g id="remote-control-fast-forward-button" transform="translate(-2.767 0)">
                                <path id="Path_226" data-name="Path 226" d="M3.263,16.871a.5.5,0,0,1-.5-.5V.5A.5.5,0,0,1,3.581.114l9.527,7.939a.5.5,0,0,1,0,.762L3.581,16.756A.5.5,0,0,1,3.263,16.871Zm.5-15.316v13.76l8.256-6.88Z" transform="translate(0 0)" fill="#fff" />
                                <path id="Path_227" data-name="Path 227" d="M169.6,16.872a.5.5,0,0,1-.5-.5V13.917a.5.5,0,0,1,.992,0v1.4l8.256-6.88L170.1,1.556v1.4a.5.5,0,0,1-.992,0V.5a.5.5,0,0,1,.814-.381l9.527,7.939a.5.5,0,0,1,0,.762l-9.527,7.939A.5.5,0,0,1,169.6,16.872Z" transform="translate(-160.194 -0.001)" fill="#fff" />
                            </g>
                        </svg>Rond de boeking af & betaal
                    </button>
                </div>            
            </div>
        </div>
    </div>
</div>
                       