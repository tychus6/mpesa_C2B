<?php

date_default_timezone_set('Africa/Nairobi');

if (!$request=file_get_contents('php://input'))

{

echo "Invalid input";

exit();

}

$host="localhost";

$username="mlab";

$password="Mlab123!@#";

$database="mpesatito";
//Begin Connect to database

$con = mysqli_connect($host,$username,$password);

if(!$con)
{
echo "<response><error><message>".mysqli_error($con)."</message></error></response>";
exit();
}

//end make connections

//connect to database

if (!mysqli_select_db($con,$database ))

{

echo mysqli_error($con);

exit();

}

//end connect to database

$clean_xml = str_ireplace(['soapenv:', 'soap:', 'c2b:', 'ns1:' ], '', $request);
$xml = simplexml_load_string($clean_xml);

$firstname="";

$middlename="";

$lastname="";

$billrefno="";

foreach ($xml->xpath('//C2BPaymentConfirmationRequest') as $item)
{

$transactiontype=mysqli_real_escape_string($con,$item->TransType);
$transid=mysqli_real_escape_string($con,$item->TransID);
$transtime=mysqli_real_escape_string($con,$item->TransTime);
$transamount=mysqli_real_escape_string($con,$item->TransAmount);
$businessshortcode=mysqli_real_escape_string($con,$item->BusinessShortCode);

$billrefno=$item->BillRefNumber;
$billrefno=mysqli_real_escape_string($con,trim($item->BillRefNumber));

$invoiceno=mysqli_real_escape_string($con,$item->InvoiceNumber);

$msisdn=mysqli_real_escape_string($con,$item->MSISDN);

$orgaccountbalance=mysqli_real_escape_string($con,$item->OrgAccountBalance);

foreach($item->KYCInfo as $kycinfo)

{

if ($kycinfo->KYCName=='[Personal Details][First Name]' )

{
$firstname=mysqli_real_escape_string($con,$kycinfo->KYCValue);
}

if ($kycinfo->KYCName=='[Personal Details][Middle Name]' )

{
$middlename=mysqli_real_escape_string($con,$kycinfo->KYCValue);
}

if ($kycinfo->KYCName=='[Personal Details][Last Name]' )

{
$lastname=mysqli_real_escape_string($con, $kycinfo->KYCValue);
}

}

}
//save values to database

//save record

$sql="INSERT INTO mtest
(
TransactionType,
TransID,
TransTime,
TransAmount,
BusinessShortCode,
BillRefNumber,
InvoiceNumber,
MSISDN,
First_Name,
Middle_Name,
Last_Name,
OrgAccountBalance

)
VALUES
(

'$transactiontype',

'$transid',

'$transtime',

'$transamount',

'$businessshortcode',

'$billrefno',

'$invoiceno',

'$msisdn',

'$firstname',

'$middlename',

'$lastname',

'$orgaccountbalance'

)";

if (!mysqli_query($con,$sql))

{
echo mysqli_error($con);
}

else

{
//record saving was successful

echo '

<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> <soapenv:Header/>
<soapenv:Body>

<c2b:C2BPaymentConfirmationResult>Payment received succesfully</c2b:C2BPaymentConfirmationResult>

</soapenv:Body>
</soapenv:Envelope>

';

}
mysqli_close($con);

?>
