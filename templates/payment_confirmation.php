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

        var data = {
            booking_id: bookingId,
            merchant_email: merchantEmail
        };

        function checkPaymentStatus() {
          var booking_summary = decodeURIComponent(localStorage.getItem('booking_summary'));
          $('#booking-summary').html(booking_summary);
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
                  if(response.success){
                      if(response.payment_status.payment.status == 'paid'){
                        $('.payment-success').show();
                        $('.payment-failed').hide();
                      }else{
                        $('.payment-success').hide();
                        $('.payment-failed').show();
                      }
                  }else{
                      $('.payment-success').hide();
                      $('.payment-failed').show();
                  }
              },
              error: function(xhr, status, error) {
                  // Handle error response
                  console.error('Fout bij het plaatsen van boekingsgegevens:', error);
                  $('.payment-success').hide();
                  $('.payment-failed').show();
              }
          });
        }
        if(bookingId == null || merchantEmail == null || eventKey == null){
            // redirect back to home page
            window.location.href = '/';
        }else{
            checkPaymentStatus();
        }
    });
</script>

<div class="payment-confirmation">
    <!-- payment succeed code here -->
	
	<div class="row payment-success" style="display:none;">
		<div class="container thankyoumsg_container">
			<h2 class="thankyouheading">
				Bedankt!
			</h2>
			<p class="thankyoumsg-success-content">
				Uw betaling is geslaagd! Hieronder vind u de bevestiging van uw boeking, inclusief alle reisgegevens. 
        We hebben ook een e-mail met deze informatie verstuurd naar het door u opgegeven e-mailadres.
			</p>
		</div>
	</div>
	
	<div class="row booking-details-confirmation-sect row payment-success" style="display:none;">
	<!-- SUMMARY TEXT TO GO HERE -->
    <div class="card">
      <div class="row">
          <div id="booking-summary">
          </div>
      </div>
    </div>
	</div>

  <!-- payment failed code here -->
	<div class="row payment-failed" style="display:none;">
		<div class="container thankyoumsg_container failed-payment">
			<h2 class="thankyouheading">
				Oeps! Er ging iets mis!
			</h2>
			<p class="thankyoumsg-success-content">
				Er is iets misgegaan en uw betaling is niet gelukt. Een van onze medewerkers zal binnenkort contact met u opnemen om u te assisteren met de vervolgstappen, 
        zodat uw reis geboekt kan worden en u zich kunt voorbereiden op uw volgende hardloopevenement.
			</p>
			<a href="mailto:info@loopreizen.nl" target="_blank">
			<button type="button" class="mailusbtn">
				Contact opnemen
			</button></a>
		</div>
	</div>
	
</div>