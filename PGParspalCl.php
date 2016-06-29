<?php
/**
 *@author Sayyed Jamal Ghasemi
 *@author Sayyed Jamal Ghasemi <jamal13647850@gmail.com>
 *@version 1.0.0
 *
 */
namespace pgsavis;
class PGParspalCl {
    private $vars = array();
    /**
     * Parspal Wsdl link
     *
     * @var string
     */
    private $WSDL = "http://merchant.parspal.com/WebService.asmx?wsdl";
    private $sandbox="http://sandbox.parspal.com/WebService.asmx?wsdl";
    /**
     * Soap Client
     */
    private $client;
    function __construct($param) {
        if (isset($param['sandbox'])) {
            $this->client = new SoapClient($this->sandbox);
        }
        else{
            $this->client = new SoapClient($this->WSDL);
        }
        if (isset($param['table_prefix'])){
            $this->vars['table_prefix']=$param['table_prefix'];
            $this->vars['table_name']=$param['table_prefix'].$param['table_name'];
            $pgvpayments_table='CREATE TABLE IF NOT EXISTS `'.$param['table_prefix'].$param['table_name'].'` (
              `ID` int(10) NOT NULL AUTO_INCREMENT,
              `user_ID` int(10) NOT NULL,
              `post_ID` int(10) NOT NULL,
              `paymenter_ip` varchar(50) NOT NULL,
              `payment_date` timestamp NOT NULL,
              `payment_cost` int(15) NOT NULL,
              `refNumber` varchar(15) NOT NULL,
              `payment_agancy` varchar(20) DEFAULT NULL,
              `Payments_desc` TEXT DEFAULT NULL,
              `status` tinyint(1) NOT NULL,
              PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' ;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($pgvpayments_table);
        }
        else{
            $message=__('Table prefix not defined','vai');
            echo "<script type='text/javascript'>alert('$message');</script>";
            exit();
        }
        if (isset($param['MerchentID'])){
            $this->vars['MerchentID']=$param['MerchentID'];
        }
        else{
            $message=__('MerchentID not defined','vai');
            echo "<script type='text/javascript'>alert('$message');</script>";
            exit();
        }
        if (isset($param['Password'])){
            $this->vars['Password']=$param['Password'];
        }
        else{
            $message=__('Password not defined','vai');
            echo "<script type='text/javascript'>alert('$message');</script>";
            exit();
        }
        if (isset($param['ReturnPath'])){
            $this->vars['ReturnPath']=$param['ReturnPath'];
        }
        else{
            $message=__('ReturnPath not defined','vai');
            echo "<script type='text/javascript'>alert('$message');</script>";
            exit();
        }
        if (isset($param['Domain'])){
            $this->vars['Domain']=$param['Domain'];
        }
        else{
            $this->vars['Domain']='PG-Domain';
        }
        $this->register();
    }
    function __set($name, $value) {
        $this->vars[$name] = $value ;
    }
    function __get($name) {
        return $this->vars[$name];
    }
    function __call($name, $arguments) {

    }

    function register_shortcodes(){
        add_shortcode('drhspayment',array($this,'payment_form'));
        add_filter('widget_text', array($this,'do_shortcode'));
    }
    function register(){
        add_action( 'init', array($this,'register_shortcodes'));
    }
    /**
     * Request for payment transactions
     *
     * @param  Not param
     * @return Status request
     */
    public function Request() {
        $res = $this->client->RequestPayment(array(
            "MerchantID" => $this->vars['MerchentID'],
            "Password" => $this->vars['Password'],
            "Price" => $this->vars['Price'],
            "ReturnPath" => $this->vars['ReturnPath'],
            "ResNumber" => $this->vars['ResNumber'],
            "Description" =>$this->vars['Description'],
            "Paymenter" => $this->vars['Paymenter'],
            "Email" => $this->vars['Email'],
            "Mobile" => $this->vars['Mobile']
        ));

        $PayPath = $res->RequestPaymentResult->PaymentPath;
        $Status = $res->RequestPaymentResult->ResultStatus;

        if($Status == 'Succeed') {
            //header("Location: $PayPath");
            echo "<meta http-equiv='Refresh' content='0;URL=$PayPath'>";
        } else {
            return $Status;
        }

    }

    /**
     * Verify Payment
     *
     * @param  Not param
     * @return Status verify
     */
    public function Verify() {
        if(isset($_POST['status']) && $_POST['status'] == 100) {
            $Status = $_POST['status'];
            $this->vars['RefNumber'] = $_POST['refnumber'];
            $this->vars['ResNumber'] = $_POST['resnumber'];
            $res = $this->client->VerifyPayment(array(
                "MerchantID" => $this->vars['MerchentID'],
                "Password" => $this->vars['Password'],
                "Price" => $this->vars['Price'],
                "RefNum" => $this->vars['RefNumber']
            ));
            $Status = $res->verifyPaymentResult->ResultStatus;
            $this->PayPrice = $res->verifyPaymentResult->PayementedPrice;
            global $wpdb;
            $db=$wpdb->update( $this->vars['table_name'], array('status' =>1),
                array('refNumber' =>$this->vars['ResNumber']));
            return $Status;

        }
    }

    function manage_payment(){
        if($_POST['submit_payment']) {
            if($_POST['payer_name'] && $_POST['payer_email'] && $_POST['payer_mobile'] && $_POST['payer_price'] && $_POST['description_payment']) {
                $this->vars['Price'] = $_POST['payer_price'];
                $this->vars['ResNumber'] = preg_replace("/[^0-9]/", "", uniqid());
                $this->vars['Description'] = $_POST['description_payment'];
                $this->vars['Paymenter'] = $_POST['payer_name'];
                $this->vars['Email'] = $_POST['payer_email'];
                $this->vars['Mobile'] = $_POST['payer_mobile'];
                $this->vars['user_ID'] = $_POST['userid'];
                $this->vars['post_ID'] = $_POST['postid'];
                $this->vars['paymenter_ip'] = $this->get_client_ip();
                $this->vars['payment_date'] = date('Y/m/d H:i:s');
                $this->vars['status'] =0;
                global $wpdb;
                $wpdb->insert( $this->vars['table_name'],array(
                    'user_ID' => $this->vars['user_ID'],
                    'post_ID' => $this->vars['post_ID'],
                    'paymenter_ip' => $this->vars['paymenter_ip'],
                    'payment_date' => $this->vars['payment_date'],
                    'payment_cost' => $this->vars['Price'],
                    'refNumber' => $this->vars['ResNumber'],
                    'payment_agancy' => '',
                    'Payments_desc' => $this->vars['Description'],
                    'status' => $this->vars['status']
                ) );
                if($this->Request()) {
                    switch($this->Request()) {
                        case 'Ready':
                            echo '<p class="error-payment">' . __('Error! No action has been.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'GetwayUnverify':
                            echo '<p class="error-payment">' . __('Error! Your port is disabled.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'GetwayIsExpired':
                            echo '<p class="error-payment">' . __('Error! Your port is invalid.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'GetwayIsBlocked':
                            echo '<p class="error-payment">' . __('Error! Your port is blocked.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'GetwayInvalidInfo':
                            echo '<p class="error-payment">' . __('Error! Your user code or password is incorrect.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'UserNotActive':
                            echo '<p class="error-payment">' . __('Error! User is inactive.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'InvalidServerIP':
                            echo '<p class="error-payment">' . __('Error! IP server is invalid.', $this->vars['Domain']) . '</p>';
                            break;
                        case 'Failed':
                            echo '<p class="error-payment">' . __('Error! Operation fails.', $this->vars['Domain']) . '</p>';
                            break;
                    }
                }
                else {
                    add_option('user_price_' . $this->vars['ResNumber'], $_POST['payer_price']);
                    update_option('user_price_' . $this->vars['ResNumber'], $_POST['payer_price']);
                }
            }
            else {
                echo '<p class="error-payment">' . __('Error! Please Complate all field.', $this->vars['Domain']) . '</p>';
            }
        }
        else{
            $this->vars['Price'] = get_option('user_price_' . $_POST['resnumber']);
            switch($this->Verify()) {
                case 'Ready':
                    echo '<p class="error-payment">' . __('Error! No action has been.', $this->vars['Domain']) . '</p>';
                    continue;
                case 'NotMatchMoney':
                    echo '<p class="error-payment">' . __('Error! Paid the amount requested is not equa.', $this->vars['Domain']) . '</p>';
                    continue;
                case 'Verifyed':
                    echo '<p class="error-payment">' . __('Error! Has already been paid.', $this->vars['Domain']) . '</p>';
                    continue;
                case 'InvalidRef':
                    echo '<p class="error-payment">' . __('Error! Receipt number is not acceptable.', $this->vars['Domain']) . '</p>';
                    continue;
                case 'success':
                    echo '<p class="success-payment">' . sprintf(__('Transaction was successful. <br /> Your tracking number: %s <br /> Payment price : %s <br /> Your Order ID: %s',$this->vars['Domain']), $this->vars['RefNumber'], number_format($this->vars['PayPrice'], 0, '.', ''), $this->vars['ResNumber']) . '</p>';
                    echo __('Please Keep It. Your Request will be answered soon.',$this->vars['Domain']);
                    continue;
            }

            delete_option('user_price_' . $this->vars['ResNumber']);
        }
    }

    function payment_form(){
        $current_user = wp_get_current_user();
?>
        <form action="" method="post">

            <input type="hidden" name="postid" id="postid" value="<?php echo $_GET['pid'] ?>">
            <input type="hidden" name="userid" value="<?php echo get_current_user_id() ?>">
            <input type="hidden" name="payer_name" value="<?php echo $current_user->display_name ?>">
            <input type="hidden" name="payer_email" value="<?php echo $current_user->user_email ?>">
            <input type="hidden" name="payer_mobile" value="<?php echo '000000000000' ?>">
            <label for="payer_price">Price:</label>
            <input type="text" name="payer_price" readonly value="<?php echo '100'; ?>">
            <input type="hidden" name="description_payment" value="<?php echo $current_user->display_name ?>">
            <input type="submit" name="submit_payment" value="submit_payment">
        </form>
        <?php
        $this->manage_payment();


    }
    function get_client_ip() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    function is_Paid($userid,$postID ){
        global $wpdb;
        $res=$wpdb->get_results("SELECT * FROM {$this->vars['table_name']} WHERE user_ID ={$userid} and post_ID={$postID} and status=1");

        return $res;
    }
    /**
     * Send debug code to the Javascript console
     */
    function debug_to_console($data) {
        if(is_array($data) || is_object($data))
        {
            echo("<script>console.log('PHP: ".json_encode($data)."');</script>");
        } else {
            echo("<script>console.log('PHP: ".$data."');</script>");
        }
    }
}