<?php


$plugin_info = array(
						'pi_name'			=> 'PB Australia Post Lookup',
						'pi_version'		=> '1.0',
						'pi_author'			=> 'Paul Beardsell',
						'pi_author_url'		=> 'http://www.lime29.com/',
						'pi_description'	=> 'Looks up the estimated delivery costs of item',
						'pi_usage'			=> PB_auspost::usage()
					);


class PB_auspost{
	
	var $return_data;
	
	function PB_auspost(){
		
		if (session_id() == "") 
		{
			session_start(); 
		}
		
		if(($_SESSION['pb_del_postcode']) == ""){
			$_SESSION['pb_del_postcode'] = "3000";
		}

		return false;
		
	} 
	
	
	
	function delivery_lookup(){
		
		if (session_id() == "") 
		{
			session_start(); 
		}
		
		if(($_SESSION['pb_del_postcode']) == ""){
			$_SESSION['pb_del_postcode'] = "3000";
		}
		
		
		global $TMPL, $OUT;
		$out = "";
		/*
		$var_pickup = 3001;
		$var_destination = 3000;
		$var_country = "AU";
		$var_weight = 60000; //Grams
		$var_service = "Standard";		
		$var_length = 200; //mm
		$var_width = 200; //mm
		$var_height = 200; //mm
		$var_quantity = 1;
		*/
		
		
		$var_pickup = $TMPL->fetch_param('pickup_postcode');
		$var_country = $TMPL->fetch_param('country');
		$var_weight = $TMPL->fetch_param('weight');
		$var_service = $TMPL->fetch_param('service_type');
		$var_length = $TMPL->fetch_param('length');
		$var_width = $TMPL->fetch_param('width');
		$var_height = $TMPL->fetch_param('height');
		$var_quantity = $TMPL->fetch_param('quantity');
		$currency_symbol = $TMPL->fetch_param('currency_symbol');
		
		/* Set some defaults*/
		
		if($var_country == ""){
			$var_country = "AU";
		}
		if($var_service == ""){
			$var_service = "Standard";
		}
		if($currency_symbol == ""){
			$currency_symbol = "$";
		}
		
		
		$del_info = 'Pickup_Postcode='.$var_pickup;
		$del_info .= '&Destination_Postcode='.$_SESSION['pb_del_postcode'];
		$del_info .= '&Country='.$var_country;
		$del_info .= '&Weight='.$var_weight;
		$del_info .= '&Service_Type='.$var_service;
		$del_info .= '&Length='.$var_length;
		$del_info .= '&Width='.$var_width;
		$del_info .= '&Height='.$var_height;
		$del_info .= '&Quantity='.$var_quantity;

		
		$info=file('http://drc.edeliver.com.au/ratecalc.asp?'.$del_info);
		
		$return_msg = split("=", $info[2]);
		
		$return_info = split("=", $info[0]);
	

		if($return_info[1] == 0){

			$return_info = split("=", $info[2]);
			return $return_info[1];
		}
		else{
			$return_info = number_format($return_info[1], 2, '.', '');
			return $return_info;
		}
		
		
	}
	
	function postcode(){
		if (session_id() == "") 
		{
			session_start(); 
		}
		
		return $_SESSION['pb_del_postcode'];
		
	}
	
	function set_postcode(){
		
		global $TMPL, $OUT;
		
		if (session_id() == "") 
		{
			session_start(); 
		}
		
		$_SESSION['pb_del_postcode'] = $TMPL->fetch_param('destination_postcode');
		
		return $_SESSION['pb_del_postcode'];
		
	}
	
	

	function usage()
	   {
	   ob_start(); 
	   ?>

	   	A value must be provided for each data entity.  
		In some cases this may require dummy data to be sent.  (Eg a dummy parcel weight and dimensions when only a delivery time estimate is required).  
		-----------------------------------------------
		Variables
		-----------------------------------------------
		pickup_postcode	
		-----------------------------------------------
		Postcode of the pick-up address 
		Eg 5065. 	
		Typically defaulted to the postcode where goods are warehoused.	
		string: 4 chars
		
		destination_postcode	
		-----------------------------------------------
		Postcode of the delivery destination 
		Eg. 7015. 	
		Typically entered by the Merchant’s customer.
		A postcode is required for international destinations, but is not used in the calculation of the delivery charge.	
		string: 4 chars
		
		country	
		-----------------------------------------------
		Country of the delivery destination. 
		Designated by two character code. 
		Eg “AU” = Australia.	
		Typically the Consumer selects a country from a list of destinations and the web site automatically sends the matching code.  If Merchant only ships to destinations in Australia, typically web site is programmed to send code “AU” with every data set.	
		string: 2 chars 

		Refer “Country Codes” section in AusPostSuppliedIntegration.doc for a list of countries and their codes.
		
		service_type
		-----------------------------------------------
		The type of delivery service required.  	
		Typically entered by the Consumer from a selection list. Options are :
		DOMESTIC
		“Standard” = Regular Parcels
		“Express” = Express Parcels
		INTERNATIONAL
		“Air” = Air Mail
		 “Sea” = Sea Mail
		“ECI_D” = Express Courier International Document
		“ECI_M” = Express Courier International Merchandise
		“EPI” = Express Post International	
		
		DOMESTIC VALUES
		'STANDARD'
		'EXPRESS'
		INTERNATIONAL VALUES
		'AIR'
		'SEA'
		 ‘ECI_D’
		‘ECI_M’
		‘EPI’
		
		weight
		-----------------------------------------------
		Packed weight of each parcel in grams (g).	
		Typically sourced from the product and added automatically to calculate the total weight & dimensions.
		Allowance must be made for the weight & dimensions of packing material used in addition to that of the items being shipped.	
		
		Integer
		
		length	
		-----------------------------------------------
		Packed length of parcel in millimetres (mm).	
		integer
		
		width	
		-----------------------------------------------
		Packed width of parcel in millimetres (mm).	
		integer
		
		height
		-----------------------------------------------
		Packed height of parcel in millimetres (mm).
		integer
		
		quantity
		-----------------------------------------------
		Number of identical parcels for which the delivery charges are to be estimated.	Typically calculated by the Merchant’s web site.  Usually defaulted to 1 (single parcel).	
		integer
		

	   <?php
	   $buffer = ob_get_contents();

	   ob_end_clean(); 

	   return $buffer;
	   }
	
}






?>