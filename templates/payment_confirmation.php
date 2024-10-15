<?php
    define("GEMS_PLUGIN_DIR", plugin_dir_path(__FILE__));

    $eventkey = get_query_var( 'event', 'abc123XYZ456' );
    //$eventkey = '64xcbb86c14fb2';
?>

<script>
    jQuery(document).ready(function($) {
        var eventkey = '<?php echo $eventkey; ?>';
        var merchant_key = '<?php echo get_option('gems_merchant_key'); ?>';
        var api_endpoint = '<?php echo get_option('gems_api_endpoint'); ?>';
        var img_endpoint = '<?php echo get_option('gems_img_endpoint'); ?>';

        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);

        const bookingId = urlParams.get('booking_id');
        const merchantEmail = urlParams.get('merchant_email');
        const eventKey = urlParams.get('event_key');
        // if(bookingId == null || merchantEmail == null){
        //     // redirect back to home page
        //     window.location.href = '/';
        // }

        console.log('queryString', queryString);
        console.log('bookingId', bookingId);

        var data = {
            booking_id: bookingId,
            merchant_email: merchantEmail
        };

        function checkPaymentStatus() {
            $.ajax({
                url: '<?php echo $data->api_endpoint; ?>/booking/process-payment',
                type: 'GET',
                headers: {
                    'e-key': eventKey,
                    'merchant-key': '<?php echo $data->merchant_key; ?>'
                },
                data: data,
                success: function(response) {
                    // Handle success response
                    console.log('response', response);
                    if(response.successful){
                        
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
        // checkPaymentStatus();
    });
</script>

<div class="payment-confirmation">
    <!-- payment succeed code here -->
	
	<div class="row">
		<div class="container thankyoumsg_container">
			<h2 class="thankyouheading">
				Bedankt!
			</h2>
			<p class="thankyoumsg-success-content">
				Je betaling is gelukt! Hieronder vind je de bevestiging van je boeking met de boekingsgegevens voor je reis. Er is ook een e-mail verzonden naar het door jou opgegeven e-mailadres.
			</p>
		</div>
	</div>
	
	<div class="row booking-details-confirmation-sect">
		
		
		<!-- SUMMARY TEXT TO GO HERE -->
		
		<div class="row">
          <div class="col-12 my-3" id="summary_data">
            <div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
              <h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
            </div>
            <div class="col-12 table-responsive overflow-y-clip mob-hide">
              <div class="row form-fields-rows">
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Volwassene(n)</p>
                  <span id="summary_adults_count">0</span> </div>
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Kinderen</p>
                  <span id="summary_children_count">0</span> </div>
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Baby's</p>
                  <span id="summary_children_under_3_count">0</span> </div>
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
                    <div class="address"><span id="booking_visitor_address_div"></span><br>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Geboortedatum &amp; nationaliteit</p>
                  <span id="booking_visitor_birthdate_div"></span> </div>
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
                <div class="col"> <span id="booking_stayathome_title_div"></span>&nbsp;<span id="booking_stayathome_name_div"></span><br>
                </div>
                <div class="col">
                  <div class="d-flex">
                    <!-- <div class="mr-2">
                                                                    <i class="fa-solid fa-location-dot"></i>
                                                                </div> -->
                    <div class="address"><span id="booking_stayathome_address_div"></span><br>
                    </div>
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
              <div id="summary_bibs_div"></div>
            </div>
            <div class="col-12 my-3 mob-hide summ-head-box">
              <h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading">Datums</span></h3>
            </div>
            <div class="col-12 table-responsive overflow-y-clip mob-hide">
              <div class="row form-fields-rows">
                <div class="col-md-6 col-lg-8 col-xl-8">
                  <p class="summary-table-head-subs">Vertrek</p>
                  <span id="summary_departure_date" class="summary-body-txt">-</span> </div>
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Aankomst</p>
                  <span id="summary_arrival_date" class="summary-body-txt">-</span> </div>
              </div>
            </div>
            <div class="col-12 my-3 mob-hide summ-head-box">
              <h3 class="form-label-blue"><span class="badge badge-highlight">05</span><span class="summ-heading">Hotel</span></h3>
            </div>
            <div class="col-12 table-responsive overflow-y-clip mob-hide">
              <div class="row form-fields-rows" style="display:flex;flex-direction:column;justify-content:flex-start;align-content:flex-start;">
                <div class="col-md-6 col-lg-4 col-xl-4">
                  <p class="summary-table-head-subs">Hotel naam: <span id="summary_hotel_name" class="summary-body-txt">-</span></p>
                  <p class="summary-table-head-subs">Aantal Nachten: <span id="summary_hotel_nights" class="summary-body-txt">-</span></p>
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
              <div id="summary_extra_div"></div>
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
              <div id="summary_nonextra_div"></div>
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
                    <span id="summary_go_flight_name" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Vertrek</p>
                    <span id="summary_go_departure" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Aankomst</p>
                    <span id="summary_go_arrival" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Reisklasse</p>
                    <span id="summary_go_travel_classe" class="summary-body-txt">-</span> </div>
                </div>
                <div class="row form-fields-rows">
                  <h4 class="body-14  regular-400 gray-1 mb-1 summary-table-head-subs">Retourvlucht</h4>
                </div>
                <div class="row form-fields-rows">
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Vlucht</p>
                    <span id="summary_return_flight_name" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Vertrek</p>
                    <span id="summary_return_departure" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Aankomst</p>
                    <span id="summary_return_arrival" class="summary-body-txt">-</span> </div>
                  <div class="col-md-6 col-lg-4 col-xl-4 strtbewijz-col4">
                    <p class="summary-table-head-subs">Reisklasse</p>
                    <span id="summary_return_travel_classe" class="summary-body-txt">-</span> </div>
                </div>
                <div class="row summ-flight-deets-row">
                  <!-- <p class="summary-table-head-subs">Reisklasse</p> -->
                  <div class="col summ-flight-deets">
                    <p class="summary-body-txt">Aantal stoelen: <span id="summary_flight_seats"></span></p>
                  </div>
                  <div class="col summ-flight-deets">
                    <p class="summary-body-txt">Prijs per stoel: <span id="summary_flight_price"></span></p>
                  </div>
                  <div class="col summ-flight-deets">
                    <p class="summary-body-txt">Prijs: <span id="summary_flight_total_price"></span></p>
                  </div>
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
              <div id="summary_insurance_div"></div>
            </div>
            <div class="col-12 my-3 box-padding-mob">
              <h3 class="form-label-blue overigekost"><span class="summ-heading">Overige kosten</span></h3>
            </div>
            <div class="col-12">
              <div class="row mb-1">
                <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs"> SGR fee </div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt"> <span class="summary-sub-headings-txt">+ €</span> <span id="booking_sgr_fee_div"></span> <span class="summary-sub-headings-txt">per persoon</span> </div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt"> <span>Totaal: €<span style="margin-left:1px;" id="booking_sgr_fee_total"></span></span> <span id="booking_sgr_fee_total"></span> </div>
              </div>
              <div class="row mb-1">
                <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs"> Administratiekosten verzekering </div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt"> + <span id="booking_insurance_fee_div"></span> <span class="summary-sub-headings-txt">% per verzekering</span> </div>
              </div>
              <div class="row">
                <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-table-head-subs"> Calamiteitenfonds </div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt"> <span class="summary-sub-headings-txt">+ €</span> <span id="booking_calamity_fund_div"></span> per 9 personen </div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6 summary-body-txt"> <span class="">Totaal: €</span> <span style="margin-left:1px;" id="booking_calamity_fund_total"></span> </div>
              </div>
            </div>
<!--             <div class="col-12">
              <hr>
            </div> -->
            <div class="col-12">
              <!-- <div class="col-8 col-sm-8 col-md-8 col-xl-8" style="width:40%;display: flex; flex-direction: column;align-items:flex-start;justify-content:flex-start">
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Verzekering<span id="insurance_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14x;">Calamiteitenfonds<span id="calamity_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">SGR fee<span id="sgrfee_summary" style="margin-left: 50px"></span></p>
                                                        <p style="width:100%;display:flex;flex-direction:row;justify-content:space-between;align-items:center;margin-bottom:0px;text-align:left;font-size:14px;">Boeking<span id="booking_summary" style="margin-left: 50px"></span></p>
                                                    </div> -->
              <div class="row mb-2 total-price-container">
                <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black"><span class="final-total-text">Totaal (betaald)</span></div>
                <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary"> <span id="summary_total_booking" class="final-total-text">0.00</span></div>
              </div>
            </div>
<!--             <div class="col-12">
              <hr>
            </div> -->
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
          
        </div>
		
		
	</div>

    <!-- payment failed code here -->
	
	<div class="row">
		<div class="container thankyoumsg_container failed-payment">
			<h2 class="thankyouheading">
				Oeps! Er ging iets mis!
			</h2>
			<p class="thankyoumsg-success-content">
				Er is iets misgegaan en je betaling is mislukt. Een van onze medewerkers zal binnenkort contact met je opnemen om je zo snel mogelijk te helpen met de volgende stappen om je reis te boeken en op weg te gaan naar je volgende hardloopevenement.
			</p>
			<a href="mailto:info@loopreizen.nl" target="_blank">
			<button type="button" class="mailusbtn">
				Contact opnemen
			</button></a>
		</div>
	</div>
	
</div>