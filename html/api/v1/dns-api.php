<?php

# Required request parameters:
#
# key=<valid key>
# action=<add|delete|update>
# record=<a|aaaa|cname|txt>
# name=<string>
# data=<ip|string>
#
# Optional request parameters:
#
# all=<true|false>
# multiple=<true|false>

# 20190725, jtingiris

$Debug=false;

$DNS_API_Keys=array(
    "88340132-e073-41f4-ad42-b90794dde4c1", // dns-interface
);

# convert $_REQUEST keys to lowercase
$_REQUEST_lower=array();
if (isset($_REQUEST)) {
    if (is_array($_REQUEST)) {
        $_REQUEST_lower=array_change_key_case($_REQUEST, CASE_LOWER);
    }
}

#
# Functions
#

/*
 * evaluate $_REQUEST key given; return true if the key matches one of $DNS_API_Keys
 */
function dnsAuthorizedKeys() {
    global $_REQUEST_lower, $DNS_API_Keys;

    if (!empty($DNS_API_Keys) && is_array($DNS_API_Keys)) {
        if (isset($_REQUEST_lower['key'])) {
            $api_request_key=trim($_REQUEST_lower['key']);
            if (!is_null($api_request_key) && $api_request_key != "") {
                foreach ($DNS_API_Keys as $dns_api_key) {
                    $dns_api_key=trim($dns_api_key);
                    if ($api_request_key == $dns_api_key) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

/*
 * output http header, response output, response code, and then exit()
 */
function dnsResponseOutput($response_output=null, $response_code=null) {

    $response_output=trim($response_output);

    if (is_null($response_code) || !is_integer($response_code)) {
        if (is_null($response_output)) {
            $response_code=204;
        } else {
            $response_code=200;
        }
    }

    if (strlen($response_output) > 0) {
        $response_output.="\n";
    }

    http_response_code($response_code);

    header("Content-length: " . strlen($response_output));
    header('Content-Type: text/plain; charset=UTF-8');

    echo $response_output;

    exit();
}

#
# Main
#

$Response_Output=null;

# must be authorized, or exit()
if (!dnsAuthorizedKeys()) {
    dnsResponseOutput("unauthorized",401);
}

# must have dns tool
$Dns_Exec=realpath(dirname(__FILE__)."/../../bin/dns");
if (empty($Dns_Exec) || !is_readable($Dns_Exec) || !is_executable($Dns_Exec)) {
    dnsResponseOutput("dependent dns file not found executable",503);
}

# get debug

$Request_Debug=null;
if (isset($_REQUEST_lower['debug'])) {
    $Request_Debug=$_REQUEST_lower['debug'];
}
if (!is_bool($Request_Debug)) {
    if (!is_null($Request_Debug) && $Request_Debug != "") {
        if (strtolower($Request_Debug) == "true" || strtolower($Request_Debug) == "yes") {
            $Request_Debug=true;
        } else {
            $Request_Debug=false;
        }
    } else {
        $Request_Debug=false;
    }
}

if (!$Debug) {
    $Debug=$Request_Debug;
}

# get action

$Request_Action=null;
if (isset($_REQUEST_lower['action'])) {
    $Request_Action=$_REQUEST_lower['action'];
}

if (empty($Request_Action)) {
    dnsResponseOutput("action required",503);
}

# get record

$Request_Record=null;
if (isset($_REQUEST_lower['record'])) {
    $Request_Record=$_REQUEST_lower['record'];
}

if (empty($Request_Record)) {
    dnsResponseOutput("record required",503);
}

# get all

$Request_All=null;
if (isset($_REQUEST_lower['all'])) {
    $Request_All=$_REQUEST_lower['all'];
}
if (!is_bool($Request_All)) {
    if (!is_null($Request_All) && $Request_All != "") {
        if (strtolower($Request_All) == "true" || strtolower($Request_All) == "yes") {
            $Request_All=true;
        } else {
            $Request_All=false;
        }
    } else {
        $Request_All=false;
    }
}

# get multiple

$Request_Multiple=null;
if (isset($_REQUEST_lower['multiple'])) {
    $Request_Multiple=$_REQUEST_lower['multiple'];
}
if (!is_bool($Request_Multiple)) {
    if (!is_null($Request_Multiple) && $Request_Multiple != "") {
        if (strtolower($Request_Multiple) == "true" || strtolower($Request_Multiple) == "yes") {
            $Request_Multiple=true;
        } else {
            $Request_Multiple=false;
        }
    } else {
        $Request_Multiple=false;
    }
}

# get name

$Request_Name=null;
if (isset($_REQUEST_lower['name'])) {
    $Request_Name=$_REQUEST_lower['name'];
}

if (empty($Request_Name)) {
    dnsResponseOutput("name required",503);
}

# get data

$Request_Data=null;
if (isset($_REQUEST_lower['data'])) {
    $Request_Data=$_REQUEST_lower['data'];
}

if (empty($Request_Data)) {
    dnsResponseOutput("data required",503);
}

$Response_Output.="Action = $Request_Action";
$Response_Output.="\n";
$Response_Output.="Record = $Request_Record";
$Response_Output.="\n";
$Response_Output.="Name = $Request_Name";
$Response_Output.="\n";
$Response_Output.="Data = $Request_Data";
$Response_Output.="\n";
$Response_Output.="Multiple = $Request_Multiple";
$Response_Output.="\n";
if ($Debug === true) {
    $Response_Output.="Debug = $Debug";
    $Response_Output.="\n";
}

$Dns_Command="$Dns_Exec $Request_Action $Request_Record $Request_Name $Request_Data";
if ($Request_All === true) {
    $Dns_Command.=" --all";
}
if ($Request_Multiple === true) {
    $Dns_Command.=" --multiple";
}
$Dns_Command_Output_Lines=array();
$Dns_Command_RC=1;

$Dns_Command_Last_Line=exec($Dns_Command,$Dns_Command_Output_Lines,$Dns_Command_RC);

$Response_Output.="\n";
$Response_Output.=gethostname().":$Dns_Command # (RC=$Dns_Command_RC)";
$Response_Output.="\n";
$Response_Output.="\n";

// only successful output, unless debug is enabled (hides error/usage message)
if ($Dns_Command_RC === 0 || $Debug === true) {
    foreach ($Dns_Command_Output_Lines as $Dns_Command_Output_Line) {
        $Response_Output.="$Dns_Command_Output_Line";
        $Response_Output.="\n";
    }
}

if ($Dns_Command_RC === 0) {
    $Response_Output="SUCCESS\n\n".$Response_Output;
} else {
    $Response_Output="FAILED\n\n".$Response_Output;
}

dnsResponseOutput($Response_Output);

?>
