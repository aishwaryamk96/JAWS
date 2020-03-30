<?php
//echo "In view<pre>";
//print_r($GLOBALS["content"]);die;
    $ti = $GLOBALS["content"]['transaction_info']; $pi = $GLOBALS["content"]["paylink_info"];
    $us = $GLOBALS["content"]['transaction_info']['user_state'] ?? "";
    $t = psk_generate('payment_link', $GLOBALS["content"]['paylink_id'], 'paylink.confirm', strval(time()), "", "", false);
    $ru = $ti['return_url'].(strpos($ti['return_url'], "?") ? "&" : "?")."validate=".$t."&pg=razorpay";
    $states = array(
        'andhra-pradesh' => 'Andhra Pradesh',
        'arunachal-pradesh' => 'Arunachal Pradesh',
        'assam' => 'Assam',
        'bihar' => 'Bihar',
        'chhattisgarh' => 'Chhattisgarh',
        'goa' => 'Goa',
        'gujurat' => 'Gujurat',
        'haryana' => 'Haryana',
        'himachal-pradesh' => 'Himachal Pradesh',
        'jammu-and-kashmir' => 'Jammu & Kashmir',
        'jharkhand' => 'Jharkhand',
        'karnataka' => 'Karnataka',
        'kerala' => 'Kerala',
        'madhya-pradesh' => 'Madhya Pradesh',
        'maharashtra' => 'Maharashtra',
        'manipur' => 'Manipur',
        'meghalaya' => 'Meghalaya',
        'mizoram' => 'Mizoram',
        'nagaland' => 'Nagaland',
        'odisha' => 'Odisha',
        'punjab' => 'Punjab',
        'rajasthan' => 'Rajasthan',
        'sikkim' => 'Sikkim',
        'tamil-nadu' => 'Tamil Nadu',
        'telangana' => 'Telangana',
        'tripura' => 'Tripura',
        'uttar-pradesh' => 'Uttar Pradesh',
        'west-bengal' => 'West Bengal',
    );
    $territories = array(
        "andaman-and-nicobar-islands" => "Andaman & Nicobar Islands",
        "chandigarh" => "Chandigarh",
        "dadra-and-nagar-haveli" => "Dadra & Nagar Haveli",
        "daman-and-diu" => "Daman & Diu",
        "delhi" => "The Government of NCT of Delhi",
        "lakshadweep" => "Lakshadweep",
        "puducherry" => "Puducherry",
    );
    /* $others = array(
        "outside-india" => "Outside India"
    ); */

    // $GLOBALS['jaws_exec_live'] = false;
    $show_ebs = true;
    if($pi["receipt_type"] == "pgpdm" || $pi["receipt_type"] == "ipba"){
        $show_ebs = false;
    }

    $currency = $ti['currency'];

    $show_outside_india = false;
    if($currency === 'usd') {
        $show_outside_india = true;
    }

    setlocale(LC_MONETARY, 'en_IN');
    
    //JA-120 changes
    $rpayAccPlag = $GLOBALS["content"]['gateway_info']['rpay_acc_flag'];
    if($rpayAccPlag == 1 || $rpayAccPlag == true){
        $rpayKey = constant('RZPY_NEW_ACC_KEY_'.((APP_ENV == "prod") ? "LIVE" : "TEST")); 
    }else{
        $rpayKey = constant('JAWS_PAYMENT_GATEWAY_RZPY_KEY_'.($GLOBALS['jaws_exec_live'] ? "LIVE" : "TEST")); 
    }
   // echo $rpayAccPlag."---".$rpayKey;die;
    //JA-120 ends
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Jigsaw Academy - The Online School of Analytics">
        <title>Jigsaw Academy - Payment</title>
        <link rel="icon" type="image/png" href="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/favicon.png">
        <link rel='stylesheet' href='https://www.jigsawacademy.com/wp-content/themes/jigsaw/css/bootstrap.custom.css' />
        <link href='https://fonts.googleapis.com/css?family=Lato:400,300' rel='stylesheet' type='text/css'>
        <style>
            * { margin:0; padding:0; }
            .ol { width: 100vw; height: 100vh; position: absolute; z-index: -1; filter: blur(3px); background-color:rgba(200,205,205,0.9); background-image: url('<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/bkg.png'); }
            .cn { z-index: 2; height: 100vh; width: 100vw; position: relative; }
            .ct { width: auto; max-width: 300px; text-align: center; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); background-color: white; padding: 25px; box-shadow: 0px 0px 30px rgba(245,245,245,0.5); display: inline-table; }
            .tt,.nd{ font-size: 16px; font-family: 'Lato', sans-serif; margin-bottom: 10px; text-align: center; }
            .pg img{cursor:pointer;}
            .bs { display: block; transform: skew(-12deg); background-color: #FE761B; color: #ffffff; margin: 0 auto;padding: 7px 20px; border: none; margin-top: 15px; font-size: 16px; }
            .bs span{ transform: skew(12deg); display: inline-block; }
            .radio input{margin-right:15px;} .pg{text-align:left;}
            .ss{margin: 10px 0;font-size: 16px;display: flex; flex-direction: column;} .ss select{ padding: 10px; }
            .er{ color: red; display:none;font-size: 10px; line-height: 1; padding: 5px; text-align: center; }
        </style>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            const razorpay_options = {
                "key":"<?= $rpayKey ?>",
                "name":"Jigsaw Academy",
                "description":"<?php echo mb_strimwidth($ti['extra']['desc'], 0, 250, '...'); ?>",
                "image":"<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/favicon.png",

                /* "method": {
                    upi: true,
                    card: true, // normal retail payment no need to restrict payment method type.
                    wallet: true,
                    netbanking: true,
                    emi: true,
                }, */
                "modal":{
                    "backdropclose": false,
                    "escape": false,
                    "ondismiss": function(d){
                        console.log('closed razorpay payment popup');
                    }
                },
                "handler":function(r){
                    document.getElementById('rzpy_pay_id').value=r.razorpay_payment_id;
                    document.getElementById('rzpy_order_id').value=r.razorpay_order_id;
                    document.getElementById('rzpy_sig').value=r.razorpay_signature;
                    document.rzpy.submit();
                },
                "prefill":{
                    "name":"<?php echo $ti["name"]; ?>",
                    "email":"<?php echo $ti["email"];?>",
                    "contact":"<?php echo $ti["phone"]; ?>"
                },
                "notes":{
                    "address":""
                },
                "theme":{
                    "color":"#0096D9"
                },
                "hide_topbar": true,
                "amount":"<?php echo floatval($ti['sum']).'00'; /* added 00 as amount is in paise not in rupees */ ?>",
                "currency": '<?php echo strtoupper($currency); ?>',
                "order_id": ''
            };

            let data = {
                'amount' : '<?php echo floatval($ti['sum']).'00'; ?>',
                'currency' : '<?php echo strtoupper($currency); ?>',
                'receipt' : '<?php echo $ti['invoice_id']; ?>',
                'rpay_acc_flag' : '<?php echo $rpayAccPlag; ?>'
            };

            async function getOrderID(){
                let opts = {
                    method: 'POST',
                    body: JSON.stringify(data),
                };
                try {
                    let response = await fetch('<?php echo JAWS_PATH_WEB ?>/webapi/backend/dash/razorpay.order-create',opts);
                    let order = await response.json();
                    razorpay_options.order_id = order.id;
                    let razorpay = new Razorpay(razorpay_options);
                    razorpay.open();
                } catch (e) {
                    console.error(e);
                    alert('Oops! Some error occured. Please refresh the page and try again.');
                }
            }
        </script>
    </head>
    <body>
        <div class="ol"></div>
        <div class="cn">
            <div class="ct">
                <div class="tt">Amount: <b><?php echo strtoupper($currency) . " " . money_format("%!i",floatval($ti['sum'])); ?></b></div>
                <div class="tt">Available payment gateway options</div>
                <form method="post" action="" name="paymentGateway" class="pg">
                    <?php if($show_outside_india){ ?>
                        <input type="hidden" name="state" id="state" value="outside-india">
                    <?php } else { ?>
                    <div class="ss">
                        <select name="state" id="state">
                            <option value="">Select</option>
                            <optgroup label="States">
                                <?php foreach($states as $statekey => $state){ ?>
                                    <?php if( !empty($us) && $us == $statekey ) { ?>
                                        <option value="<?php echo $statekey ?>" selected="selected"><?php echo $state; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $statekey ?>"><?php echo $state; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </optgroup>
                            <optgroup label="Union Territories">
                                <?php foreach($territories as $territorykey => $territory){ ?>
                                    <?php if( !empty($us) && $us == $territorykey ) { ?>
                                        <option value="<?php echo $territorykey ?>" selected="selected"><?php echo $territory; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $territorykey ?>"><?php echo $territory; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </optgroup>
                        </select>
                        <p class="er">Please select your current residing State/Union Territory.</p>
                    </div>
                    <?php } ?>
                    <div class="radio">
                        <label><input type="radio" name="pg" value="rzpy" checked><img src="https://razorpay.com/assets/razorpay-logo-95e9447029.svg" width="150" height="32" alt="Pay with Razorpay" ></label>
                    </div>
                    <?php if($show_ebs){ ?>
                    <div class="radio">
                        <label><input type="radio" name="pg" value="ebs"><img src="<?php echo JAWS_PATH_WEB ?>/media/jaws/frontend/images/ebs-logo.jpg" width="50" height="50" alt="Pay with EBS" ></label>
                    </div>
                    <?php } ?>
                    <div class="nd"><p>Note: <b><?php echo $ti['email']; ?></b> is going to be used for communication and acknowledgement purposes.</p></div>
                    <button class="bs" type="button" onclick="javascript:submitForm(event);"><span>Proceed to Pay</span></button>
                </form>
            </div>
        </div>
        <script>function submitForm(e){
                if( document.getElementById('state').value.length == 0 || !document.getElementById('state') ) {
                    document.getElementsByClassName('er')[0].style.display = 'block';
                } else {
                    document.getElementById('user_state').value = document.getElementById('state').value;
                    if(document.querySelector('input[name=pg]:checked').value==='rzpy'){
                        getOrderID();
                        e.preventDefault();
                    }else{
                        document.paymentGateway.submit();
                    }
                }
            }
            document.addEventListener('DOMContentLoaded',function() {
                document.querySelector('select[name="state"]').onchange = changeEventHandler;
            },false);

            function changeEventHandler(event) {
                if(!event.target.value) { document.getElementsByClassName('er')[0].style.display = 'block'; }
                else { document.getElementsByClassName('er')[0].style.display = 'none'; }
            }
        </script>
        <form action="<?=$ru;?>" method="post" name='rzpy'>
            <input type="hidden" value="" name="state"                  id="user_state"     >
            <input type="hidden" value="" name="razorpay_payment_id"    id="rzpy_pay_id"    >
            <input type="hidden" value="" name="razorpay_order_id"      id="rzpy_order_id"  >
            <input type="hidden" value="" name="razorpay_signature"     id="rzpy_sig"       >
        </form>
    </body>
</html>