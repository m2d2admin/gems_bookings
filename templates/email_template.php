<?php
function bibs($bibs) {
    $temp = '';
    if(count($bibs) > 0){
        foreach ($bibs as $bib) {
            $temp .= '<div class="col-md-6 col-lg-8 col-xl-8">
                        <div class="bibs-div">
                            <p>'.$bib["bibs_name"].'</p>
                            <span>'.$bib["bibs_count"].'</span>
                        </div>
                    </div>';
        }
    }
    return $temp;
}
function extra_hotel($hotel_extras) {
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
    $footer = "";
    $footer_segments = explode(";", $email_settings["email_footer"]);
    foreach ($footer_segments as $segment) {
        $footer .= $segment."<br>";
    }
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
.bibs-div {display: flex;flex-direction: row;justify-content: space-between;align-items: center;}
.correspondent_number {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  flex-align: center;
  width: 98%;
  padding: 20px 0px 70px 20px;
  border-left: 5px solid #4fb9f3;
  background-color: #2c3034;
}
.correspondent_number p{
  margin: 0px;
  color: #4fb9f3;
}
.correspondent_number h2{
  margin: 0px;
  color: white;
}
</style>
    </head>
    <div class="card">
        <div class="row">
            <p>'.$email_settings["email_header"].'</p><br>
            <p>De reis die u gaat maken is '.$booking_details["event_name"].'</p><br><br>
            <div class="correspondent_number">
                <p>Uw correspondentienummer is</p>
                <h2>'.$booking_details["booking_code"].'</h2>
            </div><br>
            <div id="form_section9_content">
                '.urldecode($booking_details["summary"]).'
            </div>
            <p>'.str_replace(';', '<br>', $email_settings["email_footer"]).'</p>
        </div>
    </div>
</body></html>';
    return  $temp;
}
?>