<?php

if (!$request=file_get_contents('php://input'))

{

echo "Invalid input";

}
else

{
//if the $resultcode is something else other than zero, the transaction will be rejected. This is useful because you can check the customer from a database and if they are not found, alter this value to reject the transaction
$resultcode=0;
$resultdesc="Valid Account. Accept payment";

$soapresponse='

<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment">

<soapenv:Header/>

<soapenv:Body>
<c2b:C2BPaymentValidationResult>

<ResultCode>'.$resultcode.'</ResultCode>
<ResultDesc>'.$resultdesc.'</ResultDesc>
<ThirdPartyTransID>0</ThirdPartyTransID>

</c2b:C2BPaymentValidationResult>

</soapenv:Body>

</soapenv:Envelope>

';

echo $soapresponse;

}
?>
