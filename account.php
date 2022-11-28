<?php
session_start();
error_reporting(1);
include 'header.php';
include('connect.php');



 date_default_timezone_set('Africa/Lagos');
 $current_date = date('Y-m-d');
if(isset($_GET["login"]))
{
 header("location:../admission/index.php");
}

if(isset($_POST["btnsubmit"]))
{

//Get application ID
function applicationID(){
$string = (uniqid(rand(), true));
return substr($string, 0,5);
}
	
$applicationID = "ADM/".date("Y")."/".applicationID();		
$fullname = mysqli_real_escape_string($conn,$_POST['txtfullname']);
$sex = mysqli_real_escape_string($conn,$_POST['cmdsex']);
$phone = mysqli_real_escape_string($conn,$_POST['txtphone']);
$email = mysqli_real_escape_string($conn,$_POST['txtemail']);
$password = mysqli_real_escape_string($conn,$_POST['password']);
$cpassword = mysqli_real_escape_string($conn,$_POST['cpassword']);


  if($password == $cpassword){
   
   // Password Validation 
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $spec_char = preg_match('@[^\w]@', $password);
    if(!$uppercase || !$lowercase || !$number || !$spec_char || strlen($password) < 8){
       echo '<script type="text/javascript">alert("Password is not Strong, it should be atleast not less than 8 character, 1special character 1, and 1 Number")</script>';
    }else{
$sql = "INSERT INTO account(fullname,sex,phone,email,password,date_admission,applicationID)VALUES('$fullname','$sex','$phone','$email','$password','$date_admission','$applicationID')";
$res_u = mysqli_query($conn, $sql);  

    if($res_u){
        echo '<script type="text/javascript">alert("Create Successfull go to Login/Apply to Continue")</script>';


        
$json_url = "http://api.ebulksms.com:8080/sendsms.json";
$xml_url = "http://api.ebulksms.com:8080/sendsms.xml";
$http_get_url = "http://api.ebulksms.com:8080/sendsms";
$username = '';
$apikey = '';
 
if (isset($_POST['button'])) {
    $username = 'salisusuleimanshamaki@out.com';
    $apikey = '38e2fa703f4e85e953850fb6e7debfdb8aa4807a';
    $sendername = substr('CAMTECH', 0, 11);
    $recipients = $phone;
    $message = 'Your account Has been created';
    $flash = 0;
    $message = substr('Your account Has been created', 0, 160);//Limit this message to one page.
    $Ebulksms = new Ebulksms();
 
#Use the next line for HTTP POST with JSON
    $result = $Ebulksms->useJSON($json_url, $username, $apikey, $flash, $sendername, $message, $recipients);
#Uncomment the next line and comment the one above if you want to use HTTP POST with XML
    //$result = $Ebulksms->useXML($xml_url, $username, $apikey, $flash, $sendername, $message, $recipients);
#Uncomment the next line and comment the ones above if you want to use simple HTTP GET
    //$result = $Ebulksms->useHTTPGet($http_get_url, $username, $apikey, $flash, $sendername, $message, $recipients);
}
 
class Ebulksms {
 
    public function useJSON($url, $username, $apikey, $flash, $sendername, $messagetext, $recipients) {
        $gsm = array();
        $country_code = '234';
        $arr_recipient = explode(',', $recipients);
        foreach ($arr_recipient as $recipient) {
            $mobilenumber = trim($recipient);
            if (substr($mobilenumber, 0, 1) == '0') {
                $mobilenumber = $country_code . substr($mobilenumber, 1);
            } elseif (substr($mobilenumber, 0, 1) == '+') {
                $mobilenumber = substr($mobilenumber, 1);
            }
            $generated_id = uniqid('int_', false);
            $generated_id = substr($generated_id, 0, 30);
            $gsm['gsm'][] = array('msidn' => $mobilenumber, 'msgid' => $generated_id);
        }
        $message = array(
            'sender' => $sendername,
            'messagetext' => $messagetext,
            'flash' => "{$flash}",
        );
 
        $request = array('SMS' => array(
                'auth' => array(
                    'username' => $username,
                    'apikey' => $apikey
                ),
                'message' => $message,
                'recipients' => $gsm
        ));
        $json_data = json_encode($request);
        if ($json_data) {
            $response = $this->doPostRequest($url, $json_data, array('Content-Type: application/json'));
            $result = json_decode($response);
            return $result->response->status;
        } else {
            return false;
        }
    }
 
    public function useXML($url, $username, $apikey, $flash, $sendername, $messagetext, $recipients) {
        $country_code = '234';
        $arr_recipient = explode(',', $recipients);
        $count = count($arr_recipient);
        $msg_ids = array();
        $recipients = '';
 
        $xml = new SimpleXMLElement('<SMS></SMS>');
        $auth = $xml->addChild('auth');
        $auth->addChild('username', $username);
        $auth->addChild('apikey', $apikey);
 
        $msg = $xml->addChild('message');
        $msg->addChild('sender', $sendername);
        $msg->addChild('messagetext', $messagetext);
        $msg->addChild('flash', $flash);
 
        $rcpt = $xml->addChild('recipients');
        for ($i = 0; $i < $count; $i++) {
            $generated_id = uniqid('int_', false);
            $generated_id = substr($generated_id, 0, 30);
            $mobilenumber = trim($arr_recipient[$i]);
            if (substr($mobilenumber, 0, 1) == '0') {
                $mobilenumber = $country_code . substr($mobilenumber, 1);
            } elseif (substr($mobilenumber, 0, 1) == '+') {
                $mobilenumber = substr($mobilenumber, 1);
            }
            $gsm = $rcpt->addChild('gsm');
            $gsm->addchild('msidn', $mobilenumber);
            $gsm->addchild('msgid', $generated_id);
        }
        $xmlrequest = $xml->asXML();
 
        if ($xmlrequest) {
            $result = $this->doPostRequest($url, $xmlrequest, array('Content-Type: application/xml'));
            $xmlresponse = new SimpleXMLElement($result);
            return $xmlresponse->status;
        }
        return false;
    }
 
//Function to connect to SMS sending server using HTTP GET
    public function useHTTPGet($url, $username, $apikey, $flash, $sendername, $messagetext, $recipients) {
        $query_str = http_build_query(array('username' => $username, 'apikey' => $apikey, 'sender' => $sendername, 'messagetext' => $messagetext, 'flash' => $flash, 'recipients' => $recipients));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "{$url}?{$query_str}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
        //return file_get_contents("{$url}?{$query_str}");
    }
 
//Function to connect to SMS sending server using HTTP POST
    private function doPostRequest($url, $arr_params, $headers = array('Content-Type: application/x-www-form-urlencoded')) {
        $response = array('code' => '', 'body' => '');
        $final_url_data = $arr_params;
        if (is_array($arr_params)) {
            $final_url_data = http_build_query($arr_params, '', '&');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $final_url_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  try{
            $response['body'] = curl_exec($ch);
            $response['code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if ($response['code'] != '200') {
                throw new Exception("Problem reading data from $url");
            }
            curl_close($ch);
  } catch(Exception $e){
      echo 'cURL error: ' . $e->getMessage();
  }
        return $response['body'];
    }
 
}




    }else{
        echo '<script type="text/javascript">alert("error occured")</script>';
    }
    }
}else{
    echo '<script type="text/javascript">alert("Password and Confirm Password Must Be The Same")</script>';
}
 
}

?>
<title>Create of Account| Online student admission system</title>
<?php if ($msg <> "") { ?>
  <style type="text/css">
<!--
.style1 {
	font-size: 12px;
	color: #FF0000;
}
}
-->
  </style>
  <div class="alert alert-dismissable alert-<?php echo $msgType; ?>">
    <button data-dismiss="alert" class="close" type="button">x</button>
    <p><?php echo $msg; ?></p>
  </div>
<?php } ?>
<p><h4><?php echo "<p> <font color=red font face='arial' size='3pt'>$msg_error</font> </p>"; ?></h4>  </p>
  <h4><?php echo "<p> <font color=green font face='arial' size='3pt'>$msg_success</font> </p>"; ?></h4>  </p>
<div class="container">
  <div class="row">
    <div class="col-lg-6">
      <div class="well contact-form-container">
        <form class="form-horizontal contactform" action="" method="post" name="f" >
          <fieldset>
	
                         <div class="form-group">
              <label class="col-lg-12 control-label" for="pass1">Fullname:
                <input type="text"  id="pass1" class="form-control" name="txtfullname" value="<?php if (isset($_POST['txtfullname']))?><?php echo $_POST['txtfullname']; ?>" required="">
              </label>
            </div>
			<div class="form-group">
              <label class="col-lg-12 control-label" for="pass1">Sex:
               <select name="cmdsex" id="gender" class="form-control" required="">
                                                    <option value=" ">--Select Gender--</option>
                                                     <option value="Male">Male</option>
                                                      <option value="Female">Female</option>
                                              </select>
              </label>
            </div>
			  <div class="form-group">
              <label class="col-lg-12 control-label" for="pass1">phone:
                <input type="number"  id="pass1" class="form-control" name="txtphone" value="<?php if (isset($_POST['txtphone']))?><?php echo $_POST['txtphone']; ?>" required="">
              </label>
            </div>
				  <div class="form-group">
              <label class="col-lg-12 control-label" for="uemail">Email:
             <input type="email" name="txtemail" class="form-control" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"  value="<?php if (isset($_POST['txtemail']))?><?php echo $_POST['txtemail']; ?>" required>
              </label>
            </div>
			 <div class="form-group">
              <label class="col-lg-12 control-label" for="pass1">Password:
                <input type="Password"  id="pass1" class="form-control" name="password" required="">
              </label>
            </div>
				<div class="form-group">
              <label class="col-lg-12 control-label" for="pass1">Confirm Password:
                <input type="Password"  id="pass1" class="form-control" name="cpassword" required="">
              </label>
            </div>

            <div style="height: 10px;clear: both"></div>

            <div class="form-group">
              <div class="col-lg-10">
                <button class="btn btn-primary" type="submit" name="btnsubmit">Create</button>
              </div>
              </div>

            </div>
          </fieldset>
        </form>
    </div>
  </div>
</div>
<p align="left">Already have an Account?<a href="apply/admission.php"><strong>Apply Here</a></p></strong>
<?ph
include('form.php');
?>
<p>
</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p data-v-6f398a90="">&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>