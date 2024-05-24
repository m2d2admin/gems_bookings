<?php
function bibs($bibs) {
    $temp = '';
    if(count($bibs) > 0){
        foreach ($bibs as $bib) {
            $temp .= '<div class="col-md-6 col-lg-8 col-xl-8">
                        <p>'.$bib["bibs_name"].'</p>
                        <span>'.$bib["bibs_count"].'</span>
                    </div>';
        }
    }
    return $temp;
}
function extra_hotel($hotel_extras){
    $temp = '';
    if(count($hotel_extras) > 0){
        foreach ($hotel_extras as $extra) {
            $temp .= '<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>'.$extra["extra_name"].'</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>'.$extra["extra_count"].'</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>&euro; '.${$extra['extra_price'].toFixed(2)}.'</p>
                        </div>
                    </div>';
        }
    }
    return $temp;
}
function non_extra_hotel($non_hotel_extras){
    $temp = '';
    if(count($non_hotel_extras) > 0){
        foreach ($non_hotel_extras as $extra) {
            $temp .= '<div class="row form-fields-rows">
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>'.$extra["extra_name"].'</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>'.$extra["extra_count"].'</p>
                        </div>
                        <div class="col-md-6 col-lg-4 col-xl-4">
                            <p>&euro; '.${$extra['extra_price'].toFixed(2)}.'</p>

                        </div>
                    </div>';
        }
    }
    return $temp;
}
function email_template($booking_details, $email_settings, $email, $name) {
    var_dump($booking_details);
$temp =
 '<html><body>
    <head>
        <style>
.row {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: space-between;
    background-color: #ffffff;
}
.form-fields-rows {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}
.card {
  position: relative;
  display: flex;
  flex-direction: column;
  min-width: 0;
  height: var(--bs-card-height);
  word-wrap: break-word;
  background-color: #ffffff;
  width: 100%;
}

#booking_form .card-header h2 button{display:flex;align-items:center;}
#booking_form .card-header{overflow:hidden;padding:0;}
#booking_form span.steps{margin-right:20px;}
#booking_form .card-header h2 button{padding:0 10px;background:none;height:52px;width:100%;}
.card-title-icon{display:flex;align-items:center;padding:0;width:25%;}
.card-title-icon svg{margin-right:5px;width:15px;}
.form-group.grey-blue-bg-box {background-color: #F0F4F7;padding: 10px;}
#booking_form .plus-minus-input input{padding:8px;height:40px;background-color:#fff;border: solid 1px #D5DEE2;margin:0 2px;width:20%;}
.plus-minus-input .input-group-button {height: 40px;border: solid 1px #D5DEE2;padding: 2px 0;background-color:#fff;}
.button.hollow.circle .fa {font-size: 10px;}
.card-body{background-color: #fff;border: solid 1px #D5DEE2;margin-bottom: 40px;}
button.btn.btn-link.btn-block.btn-form-step.text-left {margin-bottom: -25px;box-shadow:0px 3px 6px rgba(0,0,0,0.37);}
h2.mb-0{width:100%;}
.card-input .card-body {margin-bottom: 0px;}
div#bibs_div,div#hotels_container {column-gap: 10px;row-gap: 10px;margin:0 auto;margin-bottom:15px;}
.bibs-item {width: 32.4%;background-color: #F0F4F7;padding: 10px;}
.bibs-item .card.card-default.card-input {padding: 0;margin: 0;}
.card-header.hotels-details-header {border-bottom: none;min-height: 50px;}
.hotels-details-header .card-title {font-size: 16px;padding:0;line-height:24px;}
.bibs-item .input-group.plus-minus-input {margin-top: 20px;margin-bottom: 10px;}
.col-radio-btn-cards {width: 32.4%;padding:0;}
#booking_form .card{margin:0;}
#booking_form .card-header{border-radius:0;}
.card-header.hotels-details-header {padding: 10px !important;}
#booking_form .plus-minus-input .input-group-button .circle {padding: 10px 15px;height:auto;}
.plus-minus-input .input-group-button{display:flex;align-items:center;}
.flight-details-bagage-weight {font-weight: 700;font-size: 13px;margin-bottom: 15px;}
.extra-bagage-check-label{margin-left:15px;}
.col.vervoer-radio-btn-group{margin-bottom:15px;}
.vervoer-radio-btn-group .card-title-icon {justify-content: end;}
.vervoer-radio-btn-group .card-title-icon svg{width:20px !important;}
h3.form-label-blue {font-size: 18px;text-transform: uppercase;font-weight: 600;color: #0093cb;background-color: #ecf2fa;padding: 7px 7px;margin-bottom: 20px;}
.form-label-blue .badge {color:#c5e71c;font-weight:900;}
.summ-heading {margin-left: 10px;}
h4.body-14 {font-size: 16px;color: #00adef;}
.summary-card p {font-size: 14px;}
.summary-card .row.mb-2 {font-weight: bold;margin-bottom: 0px !important;}
.summary-card hr {border-color: #D5DEE2;opacity: 1;margin-top: 15px;margin-bottom:15px;}
.summary-card .d-flex.summary-create {margin-top: 20px;margin-bottom: 20px;}
i.fa-circle-info {margin-left: 20px;}
.other-costs .row, .total .row {
    width: 80%;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.other-costs .row .box-padding-mob{font-weight: bold;font-size: 14px;}
.total .row .box-padding-mob{font-weight: 700;font-size: 17px;}
</style>
    </head>
    <div class="card">
		<div class="row">
			<p>'.$email_settings["email_header"].'</p>
                                <div class="box-padding-mob col-12 mb-3 mob-hide summ-head-box">
                                    <h3 class="form-label-blue"><span class="badge badge-highlight">01</span><span class="summ-heading">bezoekers</span></h3>
                                </div>
                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                    <div class="row form-fields-rows">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Volwassene(n)</p>
                                            <span id="summary_adults_count">'.$booking_details["adults_count"].'</span>    
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Kinderen</p>
                                            <span id="summary_children_count">'.$booking_details["children_count"].'</span>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Baby\'s</p>
                                            <span id="summary_children_under_3_count">'.$booking_details["children_under_3_count"].'</span>
                                        </div>                                                    
                                    </div>
                                </div>
                                <div class="col-12 my-3 mob-hide summ-head-box">
                                    <h3 class="form-label-blue"><span class="badge badge-highlight">02</span><span class="summ-heading">Bezoekersinformatie</span></h3>
                                </div>
                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                    <div class="row form-fields-rows">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Naam</p>
                                            Groepsleider: <span id="booking_visitor_title_div">'.$booking_details["visitor_title"].'</span>&nbsp;<span id="booking_visitor_name_div">'.$booking_details["visitor_name"].'</span><br>   
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Contactgegevens</p>
                                            <div class="d-flex">
                                                <div class="mr-2">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                </div>
                                                <div class="address"><span id="booking_visitor_address_div">'.$booking_details["visitor_address"].'</span><br></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Geboortedatum &amp; Nationaliteit</p>
                                            <span id="booking_visitor_birthdate_div">'.$booking_details["birthdate_visitor"].'</span>
                                        </div>                                                    
                                    </div>

                                    <div class="row form-fields-rows">
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Naam</p>
                                            Thuisblijver: <span id="booking_stayathome_title_div">'.$booking_details["country_stayathome"].'</span>&nbsp;<span id="booking_stayathome_name_div">'.$booking_details["booking_stayhome_name"].'</span><br>   
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Contactgegevens</p>
                                            <div class="d-flex">
                                                <div class="mr-2">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                </div>
                                                <div class="address"><span id="booking_stayathome_address_div">'.$booking_details["booking_stayathome_address_div"].'</span><br></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Geboortedatum &amp; Nationaliteit</p>
                                            <span id="booking_stayathome_birthdate_div">'.$booking_details["booking_stayathome_birthdate_div"].'</span>
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
                                        '.bibs($booking_details["summary_bibs"]).'
                                    </div>

                                </div>

                                <div class="col-12 my-3 mob-hide summ-head-box">
                                    <h3 class="form-label-blue"><span class="badge badge-highlight">04</span><span class="summ-heading"><!-- -->Datums</span></h3>
                                </div>
                                <div class="col-12 table-responsive overflow-y-clip mob-hide">

                                    <div class="row form-fields-rows">
                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                            <p>Vertrek</p>
                                            <span id="summary_departure_date">'.$booking_details["departure_date"].'</span>
                                        </div>
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <p>Aankomst</p>
                                            <span id="summary_arrival_date">'.$booking_details["arrival_date"].'</span>
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
                                            <span id="summary_hotel_name">'.$booking_details["hotel_name"].'</span>
                                        </div>
                                        <div class="col-md-6 col-lg-8 col-xl-8">
                                            <p>Prijs</p>
                                            <span id="summary_room_price">'.$booking_details["hotel_price"].'</span>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-12 my-3 mob-hide summ-head-box">
                                    <h3 class="form-label-blue"><span class="badge badge-highlight">06</span><span class="summ-heading">Extra\'s</span></h3>
                                </div>
                                <div class="col-12 mob-hide">
                                    <h4 class="body-14  regular-400 gray-1 mb-1">Extra\'s van hotel</h4>
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
                                    <h4 class="body-14  regular-400 gray-1 mb-1">Extra\'s buiten het hotel</h4>
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
                                        '.$booking_details['flight'].'
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
                                    '.$booking_details['insurance'].'
                                    </div>

                                </div>
                                <div class="col-12 my-3 box-padding-mob">
                                    <h3 class="form-label-blue">Overige kosten</h3>
                                </div>
                                <div class="col-12 other-costs">
                                    <div class="row mb-1">
                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            SGR fee
                                        </div>
                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            + € <span id="booking_sgr_fee_div">'.$booking_details['sgr_fee'].'</span> per persoon
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            Administratiekosten verzekering
                                        </div>
                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            + <span id="booking_insurance_fee_div">'.$booking_details['insurance_fee'].'</span> % per verzekering
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            Calamiteitenfonds
                                        </div>
                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 body-14 medium-500 gray-6">
                                            + € <span id="booking_calamity_fund_div">'.$booking_details['calamity_fund'].'</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                </div>
                                <div class="col-12 total">
                                    <div class="row mb-2">
                                        <div class="box-padding-mob col-6 col-sm-7 col-md-6 col-xl-4 caption text-black">
                                            Totaal
                                        </div>
                                        <div class="box-padding-mob col-6 col-sm-5 col-md-6 col-xl-4 caption theme-primary">
                                            € <span>'.$booking_details['total_booking'].'</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                    </div>   

                </div>
            </div>
        </div>
        </div>
        
    </div>
    <p>'.$email_settings["email_footer"].'</p>
</div></body></html>';
    return  $temp;
}
?>