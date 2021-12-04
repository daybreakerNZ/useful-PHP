<?php
/*
**    Topic:             		search criteria parser
**    Source :		Language Implementation paper AKL uni'
**    			Code translated from C to PHP 	
**    History:
**        20021030 - Day Five Consulting Ltd - Created
**
** Description:
**    Parses search criteria according to the following rules
**    E -> E and T
**    E -> E or T
**    E -> T
**    T -> not F
**    T -> F
**    F -> Id
**    F -> (E)
**
**    processes search criteria as lowercase.
*/
$rhs = array ("E and T","E or T","T","not F","F","Id","( E )" );
$lhs = array ("E","E","E","T","T","F","F");
 
function gettokens($str) {
    /* put the string into lowercase */
    $str = strtolower($str);
 
    /* make sure ( or ) get picked up as separate tokens */
    $str = str_replace("("," ( ",$str);
    $str = str_replace(")"," ) ",$str);
 
    /* get the actual tokens */
    $actualtokens = explode(" ",$str);
 
    /* trim spaces around tokens and discard those which have only spaces in them */
    $h=0;
    for ($i=0;$i<sizeof($actualtokens);$i++) {
        $actualtokens[$i]=trim($actualtokens[$i]);
        if ($actualtokens[$i] != "") {
            $nospacetokens[$h++] = $actualtokens[$i];
        }
    }
 
    /* now put together tokens which are actually one token e.g. upper hutt */
    $onetoken = "";
    $h=0;
    for ($i=0;$i<sizeof($nospacetokens);$i++) {
        $token = $nospacetokens[$i];
        switch ($token) {
            case ")" :
                if ($onetoken != "") {
                    $tokens[$h++] = $onetoken;
                    $onetoken = "";
                }
                $tokens[$h++] = $token;
                break;
 
            case "(" :
                if ($onetoken != "") {
                    $tokens[$h++] = $onetoken;
                    $onetoken = "";
                }
                $tokens[$h++] = $token;
                break;
 
            case "and" :
                if ($onetoken != "") {
                    $tokens[$h++] = $onetoken;
                    $onetoken = "";
                }
                $tokens[$h++] = $token;
                break;
 
            case "or" :
                if ($onetoken != "") {
                    $tokens[$h++] = $onetoken;
                    $onetoken = "";
                }
                $tokens[$h++] = $token;
                break;
 
            case "not" :
                if ($onetoken != "") {
                    $tokens[$h++] = $onetoken;
                    $onetoken = "";
                }
                $tokens[$h++] = $token;
                break;
 
            default :
                if ($onetoken == "") {
                    $onetoken = $token;
                }
                else {
                    $onetoken = $onetoken." ".$token;
                }
                break;
        }
    }
    if ($onetoken != "") {
        $tokens[$h++] = $onetoken;
        $onetoken = "";
    }
    return $tokens;
}
 
function checkwithrules ($tokens) {
    global $rhs;
    global $lhs;
 
    $i=0;
    $stack="";
    while ($i<sizeof($tokens)) {
        $token = $tokens[$i];
        switch ($token) {
            case "and" :
            case "or"  :
            case "not" :
            case "("   :
            case ")"   :
                if ($stack == "") {
                    $stack = $token;
                }
                else {
                    $stack = $stack." ".$token;
                }
                    /* go through the rules */
                    $j=0;
                    while ( $j<sizeof($rhs) ) {
                        $len = strlen($rhs[$j]);
                        $lenstack = strlen($stack);
                        if ($lenstack < $len) {
                            $j++;
                            continue;
                        }
                        $str = substr($stack,$lenstack - $len,$len);
                        // echo "<br>stack=".$stack.",str=".$str.",rhs[j]=".$rhs[$j];
                        if ( $str == $rhs[$j] ) {
                            $stack = substr($stack,0,$lenstack - $len);
                            $stack = $stack.$lhs[$j];
                            $j=0;
                        }
                        else {
                            $j++;
                        }
                    }
                break;
 
            default :
                if ($stack == "") {
                    $stack = "Id";
                }
                else {
                    $stack = $stack." "."Id";
                }
             
                    /* go through the rules */
                    $j=0;
                    while ( $j<sizeof($rhs) ) {
                        $len = strlen($rhs[$j]);
                        $lenstack = strlen($stack);
                        if ($lenstack < $len) {
                            $j++;
                            continue;
                        }
                        $str = substr($stack,$lenstack - $len,$len);
                        // echo "<br>stack=".$stack.",str=".$str.",rhs[j]=".$rhs[$j];
                        if ( $str == $rhs[$j] ) {
                            $stack = substr($stack,0,$lenstack - $len);
                            $stack = $stack.$lhs[$j];
                            $j=0;
                        }
                        else {
                            $j++;
                        }
                    }
                break;
        }
 
        $i++;
    }
//    echo "<br>Stack = '".$stack."'";
    if ($stack != "E") {
        return false;
    }
 
    return true;
}

