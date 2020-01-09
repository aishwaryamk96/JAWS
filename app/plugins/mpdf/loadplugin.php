<?php
    # mpdf v 7.1.1
    # https://mpdf.github.io/

    require_once __DIR__ . '/vendor/autoload.php';

    class PDFgen{

        protected $receiptNumGenerationRetries = 0;

        public function __construct($data){

            $this->export_path = '/var/www/live/html/jaws/app/views/misc/receipts/';
            $this->mpdf = new \Mpdf\Mpdf(['tempDir' => $this->export_path]);
            $this->filename = '';
            $this->subs_id = $data['subs_id'];
            $this->receipt_number = '';
            $this->installment = $data['instl'];
            $this->state = $data['state'] ?? "";
            $this->test_mode = !empty($data['test_mode']) ? $data['test_mode'] : false;
            $this->show_tax = !empty($data['show_tax']) ? $data['show_tax'] : true;

            $this->watermark = $data['watermark'] ?? false;
            $this->watermark_text = $data['watermark_text'] ?? 'PAID';

            $this->subs_details = db_query("SELECT * FROM `subs` WHERE `subs_id` =" . db_sanitize($this->subs_id));
            $this->subs_meta_details = db_query("SELECT * FROM `subs_meta` WHERE `subs_id` =" . db_sanitize($this->subs_id));
            $this->user_name = $data['name'];
            $this->user_email = $data['email'];
            $this->payment_details = db_query("SELECT * FROM `payment` WHERE `pay_id` =" . db_sanitize($this->subs_details[0]['pay_id']));
            $this->package_details = db_query("SELECT * FROM `package` WHERE `package_id` =" . db_sanitize($this->subs_details[0]['package_id']));
            $this->installment_details = db_query("SELECT * FROM `payment_instl` WHERE `instl_id` =" . db_sanitize($this->installment));

            // activity_create("ignore", "receipt", "success", "", "", "", "", "Receipt data" . json_encode($data), "logged");

            setlocale(LC_MONETARY, 'en_IN');

        }

        private function createPDF(){
            
            $this->generateReceiptNumber();

            $html = $this->getSubHeader() . 
                    '<br>' . 
                    $this->getDetails() . 
                    '<br>' .
                    $this->getPaymentDetails();

            // for arabic / chinese name characters below setting.
            $this->mpdf->autoScriptToLang = true;
            $this->mpdf->autoLangToFont = true;
            $this->mpdf->allow_charset_conversion = false;

            $this->mpdf->SetHTMLHeader($this->getHeader());
            $this->mpdf->SetHTMLFooter($this->getFooter());
            $this->mpdf->AddPage(
                        '', // L - landscape, P - portrait 
                        '', // E|O|even|odd|next-odd|next-even
                        '', // resetpagenum
                        '', // pagenumstyle
                        '', // suppress
                        10, // margin_left
                        10, // margin right
                        30, // margin top
                        10, // margin bottom
                        5,  // margin header
                        5   // margin footer
                    ); 
            
            if($this->watermark){
                $this->mpdf->SetWatermarkText($this->watermark_text, 0.1);
                $this->mpdf->showWatermarkText = true;
            }
            
            $this->mpdf->WriteHTML(utf8_encode($html));

            $this->generatePDFName();   // create name and assign path to it

            try{
                $this->mpdf->Output( $this->filename,'F');
                return $this->filename;
            }
            catch(Exception $e){
			    return $e->getMessage();
		    }
        }

        public function create_from_subs(){
            if( $this->payment_details[0]['currency'] === "inr" && empty($this->state) ){
                activity_create("critical", "receipt", "fail", "", "", "", "", "Receipt not generated !".$this->subs_id, "logged");
                return "";
            }
            return $this->createPDF();
        }

        public function deleteFileFromServer(){
            return $this->delete($this->filename);
        }

        protected function getHeader(){
            return '<table width="100%">
                        <tr>
                            <td>
                                <img src="https://www.jigsawacademy.com/emailer/receipts/jigsaw_horizontal_logo.png" height="65" alt="Jigsaw Academy">
                            </td>
                            <td style="font-size:20px;text-align: right;">
                                RECEIPT
                            </td>
                        </tr>
                    </table>';
        }

        protected function getFooter(){
            return '<table width="100%">
                        <tr>
                            <td style="border-top: 2px solid #5b84cb;text-align:center;font-size: 12px;">
                                CIN : U80301KA2011PTC056734
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px;">
                                Corporate Office: No.308, 100 Feet Road, Indiranagar First Stage, Bangalore-560038 <br>
                                Registered Office: E-10, Chaitanya Samarpan, Whitefield-Hoskote Main Road, Kannamangala, Bangalore – 560067
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px;">
                                Access is governed by terms as stated on <a style="text-decoration: none;" href="https://www.jigsawacademy.com/terms-conditions/"> Jigsaw website</a> and <a style="text-decoration: none;" href="https://jigsawacademy.net/terms_of_use">Jigsaw Learning Center</a>.
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 10px;">
                                If you have any questions concerning this receipt, please contact the address and phone number above. <br>
                                This is a computer-generated receipt. Signature is not required.
                            </td>
                        </tr>
                    </table>';
        }

        protected function getPaymentDetails(){
            return '<table cellspacing="0" cellpadding="0">
                        <tr>
                            <td style="font-size: 12px;">
                                Please make all cheques payable to <b><q>Jigsaw Academy Education Private Limited</q></b><br>
                                GST Registration Number: 29AACCJ5190K1Z5 <br>
                                Service Description: Commercial Training and Coaching Services <br>
                                SAC Code: 999293 <br>
                                CIN: U80301KA2011PTC056734 <br>
                                PAN number: AACCJ5190K
                            </td>
                    </tr>
                    </table>
                    <br>
                    <table cellspacing="0" cellpadding="0" style="font-size: 12px;">
                        <tr>
                            <td colspan="2">
                                <b>Payment Details</b>
                            </td>
                        </tr>
                        <tr>
                            <td>Beneficiary Name</td><td>: Jigsaw Academy Education Private Limited</td>
                        </tr>
                        <tr>
                            <td>Name of the Bank </td><td>: HDFC Bank Ltd. </td>
                        </tr>
                        <tr>
                            <td>Account Number</td><td>: 02862560001226</td>
                        </tr>
                        <tr>
                            <td>Nature of Account</td><td>: Current</td>
                        </tr>
                        <tr>
                            <td>Bank Branch</td><td>: Halasuru Branch</td>
                        </tr>
                        <tr>
                            <td>IFSC code for NEFT / RTGS &emsp;</td><td>: HDFC0000286</td>
                        </tr>
                    </table>';
        }

        protected function getSubHeader(){
            return '<table width="100%" cellspacing="0" cellpadding="10" style="border: 1px solid black; border-collapse: collapse;font-size: 12px;">
                    <tr>
                        <td style="border-right: 1px solid black;">
                            <b>Receipt from: </b> JIGSAW ACADEMY EDUCATION PVT LTD <br>
                            No.308, 2nd Floor, 100ft Main Road, <br>
                            Indiranagar, Bangalore-560038 <br>
                            Ph. No: +91-9008017000
                        </td>
                        <td style="text-align: right;">
                            <b>Date: </b>' . date("d F Y") . '<br>
                            <b>Receipt No: </b> ' . $this->receipt_number . '
                        </td>
                    </tr>
                </table>';
        }

        protected function generateReceiptNumber(){

            $this->receiptNumGenerationRetries++;
            
            $type = $this->payment_details[0]['type'];
            if($type === "corp"){
                $receipt_third_fourth_letter = "CR";
            } else if( $type === "pgpdm"){
                $receipt_third_fourth_letter = "UC";
            } else if( $type === "ipba"){
                $receipt_third_fourth_letter = "IM";
            } else {
                $receipt_third_fourth_letter = "RE";
            }
            
            if($this->payment_details[0]['currency'] === 'inr'){
                $receipt_fifth_letter = "I";
            } else {
                $receipt_fifth_letter = "F";
            }

            $installment_details = $this->installment_details[0];

            $installment_count = $this->payment_details[0]['instl_total'];
            $installment_number = $installment_details['instl_count'];

            $receipt_eighth_letter = ($installment_count < 2) ? '00' : ( ($installment_number <= 9) ? '0' . $installment_number : $installment_number );

            // find the latest receipt number being used
            $receipt_pattern = 'RJ'.$receipt_third_fourth_letter.$receipt_fifth_letter;
            $latest_receipt = db_query("SELECT i.receipt FROM payment_instl AS i INNER JOIN payment AS p ON p.pay_id = i.pay_id WHERE p.currency = " . db_sanitize($this->payment_details[0]['currency']) . " AND p.type = " . db_sanitize($type) . "  AND i.receipt LIKE " . db_sanitize($receipt_pattern."%") . " ORDER BY i.receipt DESC LIMIT 1;");

            if(!empty($latest_receipt)){
                $latest_receipt = substr($latest_receipt[0]['receipt'],5);
                $latest_receipt = explode("-",$latest_receipt);
                if( $installment_number < 2 ){
                    // increament only if different subs/package payment. will not increase for same subs/package different installment payment.
                    $receipt_sixth_letter = ++$latest_receipt[0];
                } else {
                    // for installments sixth letter will remain same as per previous/initial receipt number.
                    // find out the previous/initial receipt number.
                    $initial_receipt = db_query("SELECT receipt FROM `payment_instl` WHERE subs_id =" . db_sanitize($this->subs_id) . " AND pay_id =" . db_sanitize($this->payment_details[0]['pay_id']) . " AND status = 'paid' ORDER BY receipt DESC LIMIT 1");
                    if( !empty($initial_receipt[0]['receipt']) && !empty($initial_receipt) ){
                        $initial_receipt = substr($initial_receipt[0]['receipt'],5);
                        $initial_receipt = explode("-",$initial_receipt);
                        $receipt_sixth_letter = $initial_receipt[0]; 
                    } else {
                        $receipt_sixth_letter = ++$latest_receipt[0];
                    }
                }
            } else {
                // if no receipt with the pattern used before, latest receipt will be empty. fallback to default
                $receipt_sixth_letter = $this->defaultReceiptNumber($receipt_third_fourth_letter);
            }

            $receipt_number =  'R' .                                        // R - Receipt
                               'J' .                                        // J - Jigsaw Academy
                               $receipt_third_fourth_letter;                // RE – Retail, CR- Corporate Retail, UC – PGPDM, IM - IIM Indore's IPBA
            $receipt_number .= $receipt_fifth_letter;                       // I – For INR payments, F – for Foreign payments
            $receipt_number .= $receipt_sixth_letter;                       // 5 digit sequential receipt number
            $receipt_number .= '-' .                                        // -
                               $receipt_eighth_letter;                      // 00 for full payments, 01 – 09 for the respective instalments
            
            if($this->test_mode){
                // in test mode do not insert into db just return created receipt number and generate pdf
                $this->receipt_number = $receipt_number;
                return true;
            }

            // save the receipt number generated to maintain and find if is unique.
            if(db_exec("UPDATE `payment_instl` SET `receipt` = " . db_sanitize($receipt_number) . " WHERE `payment_instl`.`instl_id` =" . db_sanitize($this->installment)) === false) {

                if ($this->receiptNumGenerationRetries > 3) {
                    return false;
                }
                // receipt number generated already exist. re-generate receipt.
                $this->generateReceiptNumber();
                return;
            }
            $this->receipt_number = $receipt_number;
        }

        protected function generatePDFName(){
            $this->filename = $this->export_path . $this->receipt_number . '.pdf';
        }

        protected function formDetailTemplate($data){

            $html = '<table width="100%" cellspacing="0" cellpadding="10" style="border: 1px solid black; border-collapse: collapse;">
                <tr>
                    <td colspan="2">
                        <b>To :</b> <br>
                        ' . $data['name'] . '  ( ' . $data['email'] . ' )';
            
            if( $data['tax']){
                if(!empty($data['state'])){
                    $html .= ' <br> State : ' . $data['state'];
                }
            }
            
            $html .= '
                    </td>
                </tr>
                <tr>
                    <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">Description</td>
                    <td width="20%" style="border-top: 1px solid black;text-align: right;">Amount (' . $data['currency_symbol_word'] . ')</td>
                </tr>
                <tr>
                    <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">' . $data['course_names'] . (!empty($data['is_installment']) ? ' - <q>[ ' .$data['is_installment'] . ' Installment ]</q>' : "") . '</td>
                    <td width="20%" style="border-top: 1px solid black;text-align: right;">' . $data['currency_symbol'] . " " . $data['base_amount'] . '</td>
                </tr>';
                
            if( $data['tax']){
                if(!$data['tax_state']){
                    $html .= '
                            <tr>
                                <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">Integrated Goods and Service Tax (IGST) @ ' . $data['tax_rate'] . '</td>
                                <td width="20%" style="border-top: 1px solid black;text-align: right;">' . $data['currency_symbol'] . " " . $data['total_tax_amount'] . '</td>
                            </tr>
                            <tr>
                                <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">Total IGST</td>
                                <td width="20%" style="border-top: 1px solid black;text-align: right;">' . $data['currency_symbol'] . " " . $data['total_tax_amount'] . '</td>
                            </tr>';
                } else {
                    $html .= '
                            <tr>
                                <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">State Goods and Service Tax (SGST) @ ' . $data['tax_rate'] . '</td>
                                <td width="20%" style="border-top: 1px solid black;text-align: right;">' . $data['currency_symbol'] . " " . $data['tax_sgst'] . '</td>
                            </tr>
                            <tr>
                                <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">Central Goods and Service Tax (CGST) @ ' . $data['tax_rate'] . '</td>
                                <td width="20%" style="border-top: 1px solid black;text-align: right;">' . $data['currency_symbol'] . " " . $data['tax_cgst'] . '</td>
                            </tr>
                            <tr>
                                <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">Total GST</td>
                                <td width="20%" style="border-top: 1px solid black;text-align: right;">' .  $data['currency_symbol'] . " " . $data['total_tax_amount'] . '</td>
                            </tr>';
                }
            }

                
                
            $html .= '
                <tr>
                    <td width="80%" style="border-top: 1px solid black; border-right: 1px solid black;">
                        <b> ' . $data['amount_words'] . ' </b>
                    </td>
                    <td width="20%" style="border-top: 1px solid black;text-align: right;">
                        <b> ' . $data['amount'] . ' </b>
                    </td>
                </tr>
            </table>';

            return $html;
        }

        protected function getDetails(){

            // get packageid from subs
            // get payid from subs
            // price without taxes find from package table
            // for bundle and batch details look in subs_meta table.
            $subs_details = $this->subs_details[0];
            $subs_meta_details = $this->subs_meta_details[0];
            $payment_details = $this->payment_details[0];
            $package_details = $this->package_details[0];
            
            $course_names = "";
            // in subs table combo is course details.
            $courses = $subs_details['combo'];

            if(!empty($subs_meta_details['bundle_id'])){
                // if bundle_id is available use bundle name only and any other course added.
                $bundle_details = db_query("SELECT * FROM `course_bundle` WHERE `bundle_id` =" . db_sanitize($subs_meta_details['bundle_id']));
                $course_names = $bundle_details[0]['name'];
                $bundle_combo = explode(";",$bundle_details[0]['combo']); $courses_combo = explode(";",$courses);
                $other_courses = array_merge( array_diff($courses_combo, $bundle_combo), array_diff($bundle_combo, $courses_combo) ); // return courses not part of bundle combo https://stackoverflow.com/a/10077920/3007408
                if( !empty($other_courses) ){
                    $course_names .= " + ";
                    foreach( $other_courses as $course){
                        $course_id = explode(",",$course);
                        $course_details =  db_query("SELECT `name` FROM `course` WHERE `course_id` =" . db_sanitize($course_id[0]));
                        $course_names .= $course_details[0]['name'] . " + ";
                    }
                }
            } else {
                // get course names from courses table.
                $courses_explode_one = explode(";",$courses);
                foreach( $courses_explode_one as $course){
                    $course_id = explode(",",$course);
                    $course_details =  db_query("SELECT `name` FROM `course` WHERE `course_id` =" . db_sanitize($course_id[0]));
                    $course_names .= $course_details[0]['name'] . " + ";
                }
            }

            // in subs table combo free is complimentary course details.
            /* $courses_free = $subs_details['combo_free'];
            if(!empty($courses_free)){
                $courses_free_explode_one = explode(";",$courses_free);
                foreach( $courses_free_explode_one as $course){
                    $course_id = explode(",",$course);
                    $course_details =  db_query("SELECT `name` FROM `course` WHERE `course_id` =" . db_sanitize($course_id[0]));
                    $course_names .= $course_details[0]['name'] . " [Complimentary] + ";
                }
            } */
            
            $tax_amount = null; $base_amount = null; $is_installment = '';
            $installment_total = $this->installment_details[0]['instl_total'];

            if($installment_total < 2){
                if( empty($payment_details['sum_offered']) && empty($payment_details['tax_amount']) ){
                    if($package_details['creator_type'] === 'system'){
                        // in package table if creator_type is system then sum_basic is without taxes amount ( user did payment from website) sum_total = sum_offered is final amount with taxes
                        $tax_amount = $package_details['sum_total'] - $package_details['sum_basic'];
                        $base_amount = $package_details['sum_basic'];
                    } else {
                        // in package table if creator type is not system them sum_offered is without taxes amount and sum_total = sum_offered + taxes
                        $tax_amount = $package_details['sum_total'] - $package_details['sum_offered'];
                        $base_amount = $package_details['sum_offered'];
                    }
                } else {
                    $tax_amount = $payment_details['tax_amount']; 
                    $base_amount = $payment_details['sum_offered'];
                }
                // payment table sum_total is final amount paid by user
                $final_amount_paid_by_user_including_taxes_and_installments = $payment_details['sum_total'];
            } else {
                $base_amount = $this->installment_details[0]['sum'] / floatval('1.'.$package_details['tax']);
                $tax_amount =  $this->installment_details[0]['sum'] - $base_amount;
                // payment instl table sum is final amount paid by user in case of installment payments
                $final_amount_paid_by_user_including_taxes_and_installments = $this->installment_details[0]['sum'];
                // lazy way to create ordinal. is limited till 31. well installments cannot be more than 30 so should not be issue. i.e. generate 1 ( st, nd, rd, th ) format
                $is_installment = $this->installment_details[0]['instl_count'] . date("S", mktime(0, 0, 0, 0, $this->installment_details[0]['instl_count'], 0));
            }
            
            // get state details before proceed to payment.

            $currency = ($payment_details['currency'] === 'inr') ? true : false;
            $currency_symbol = ($currency) ? '&#8377;' : '$' ;
            $currency_symbol_word = ($currency) ? 'INR' : 'USD' ;            

            $course_names = trim($course_names, " +");
 
            $state = $this->state;

            $data = array();
            $data['name'] = ucwords(strtolower($this->user_name));
            $data['email'] = $this->user_email;
            $data['state'] = ucwords(strtolower(str_replace("-"," ",$this->state)));
            $data['course_names'] = $course_names;
            $data['is_installment'] = $is_installment;
            $data['currency_symbol_word'] = $currency_symbol_word;
            $data['currency_symbol'] = $currency_symbol;
            $data['tax'] = ($this->show_tax) ? ( ($currency) ? true : false ) : false;
            $data['tax_state'] = ( strtolower($this->state) == 'karnataka') ? true : false ;
            $data['tax_rate'] = ( ( strtolower($this->state) == 'karnataka') ? $package_details['tax']/2 : $package_details['tax'] ) . '%';
            $data['total_tax_amount'] = money_format('%!i', $tax_amount);
            $data['tax_cgst'] = money_format('%!i', ( strtolower($this->state) == 'karnataka') ? $tax_amount / 2 : 0 );
            $data['tax_sgst'] = money_format('%!i', ( strtolower($this->state) == 'karnataka') ? $tax_amount / 2 : 0 );
            $data['base_amount'] = money_format('%!i', $base_amount);
            $data['amount_words'] = ucwords( ( ($currency) ? $this->getINRinWords($final_amount_paid_by_user_including_taxes_and_installments) . " only" : $this->getUSDinWords($final_amount_paid_by_user_including_taxes_and_installments) ) );
            $data['amount'] = $currency_symbol . ' ' . money_format('%!i', $final_amount_paid_by_user_including_taxes_and_installments);

            return $this->formDetailTemplate($data);
        }

        private function getINRinWords( float $number){

            // taken from https://stackoverflow.com/a/25967687/3007408

            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null; $digits_length = strlen($no);
            $i = 0; $str = array();
            $words = array(0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
            $digits = array('', 'hundred','thousand','lakh', 'crore');
            while( $i < $digits_length ) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? '' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            $paise = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
            return ($Rupees ? $Rupees . 'Rupees ' : '') . $paise;
        }

        private function getUSDinWords( float $number){

            // modified from getINRinWords. will work till $999,999.99 only. need rework if million is needed.

            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null; 
            $digits_length = strlen($no);
            $i = 0; $str = array();
            $words = array(0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
                7 => 'seven', 8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve',
                13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
                16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
                19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
                40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
            $digits = array('', 'hundred','thousand','hundred', 'million');
            while( $i < $digits_length ) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);        
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = ''; $counter = count($str);
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
                } else $str[] = null;
            }
            
            $Dollars = implode('', array_reverse($str));
            $cents = ($decimal) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' cent' : '';
            return ($Dollars ? $Dollars . 'Dollars ' : '') . $cents;
        }
        
        private function defaultReceiptNumber($pattern){
            if( $pattern === "RE" && $this->payment_details[0]['currency'] === "inr" ){
                // $receipt_sixth_letter should be above 16000;
                return '16100';
            } else if( $pattern === "RE" && $this->payment_details[0]['currency'] === "usd" ){
                // $receipt_sixth_letter should be above 10000;
                return '10100';
            }  else if( $pattern === "CR"){
                // $receipt_sixth_letter should be above 10000;
                return '10000';
            }  else if( $pattern === "UC"){
                // $receipt_sixth_letter should be above 11000;
                return '11000';
            } else if( $pattern === "IM"){
                return '9000';
            }
        }

        private function delete($filename){
            if(file_exists($filename)){
                return unlink($filename);
            } else {
                return false;
            }
        }
    }

?>