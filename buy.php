<!--  Name: Divendar Murtadak   
	  URL: http://omega.uta.edu/~dum4166/buy.php
 -->
<?php
echo '<html><head><title>Buy Products</title></head>';
echo '<body text-align="center">';

session_start ();

if (! isset ( $_SESSION ['cart'] )) {
	$_SESSION ['cart'] = array ();
}

if (! isset ( $_SESSION ['total'] )) {
	$_SESSION ['total'] = 0;
}

if (isset ( $_REQUEST ['buy'] )) {
	$productId = reqInt ( 'buy' );
	$_SESSION ['cart'] [$productId] = $productId;
}

if (isset ( $_REQUEST ['delete'] )) {
	$productId = req ( 'delete' );
	unset ( $_SESSION ['cart'] [$productId] );
}

if (isset ( $_REQUEST ['clear'] ) && $_REQUEST ['clear'] == 1) {
	$_SESSION ['cart'] = array ();
	$_SESSION ['total'] = 0;
}
function req($key, $default = null) {
	return isset ( $_REQUEST [$key] ) ? trim ( $_REQUEST [$key] ) : $default;
}
function reqInt($key, $default = null) {
	$n = reqNumber ( $key, $default );
	if (! is_null ( $n )) {
		return intval ( $n );
	}
	
	return $default;
}
function reqNumber($key, $default = null) {
	$n = req ( $key, $default );
	if (is_numeric ( $n )) {
		return $n;
	}
	return $default;
}
function reqArray($key, $default = array()) {
	$value = req ( $key );
	
	if (is_null ( $value )) {
		return $default;
	}
	
	if (! is_array ( $value )) {
		return array (
				$value 
		);
	}
	
	return $value;
}
function searchRequest($key) {
	$trimStr = $key;
	$conCatTrimStr = str_replace ( " ", "+", $trimStr );
	error_reporting ( E_ALL );
	ini_set ( 'display_errors', 'On' );
	$xmlstr = file_get_contents ( 'http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&keyword=' . $conCatTrimStr );
	$xml = new SimpleXMLElement ( $xmlstr );
	echo '<table border="1" style="width:700px" bgcolor="#FFFFFF">';
	foreach ( $xml->categories->category as $category )
		foreach ( $category->items->product as $product )
			echo nl2br ( '<tr id=' . $product ['id'] . '><td><a href="buy.php?buy=' . $product ['id'] . '"><img src="' . $product->images->image->sourceURL . '"/></a></td><td>' . $product->name->asXML () . '</td><td>' . $product->minPrice->asXML () . '$</td></tr>' );
	echo '</table>';
}

echo '<h3>Shopping Basket:</h3>';
echo '<table border="1" style="width:700px" bgcolor="#FFFFFF">';

$total = 0;
foreach ( $_SESSION ['cart'] as $key => $value ) {
	$xmlstr = file_get_contents ( 'http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&productId=' . $value );
	$xml = new SimpleXMLElement ( $xmlstr );
	foreach ( $xml->categories->category as $category ) {
		foreach ( $category->items->product as $product ) {
			$minPrice = $product->minPrice->asXML ();
			$minPrice = floatval ( strip_tags ( $minPrice ) );
			$total = $total + $minPrice;
			echo nl2br ( '<tr id=' . $product ['id'] . '><td><a href="buy.php?buy=' . $product ['id'] . '"><img src="' . $product->images->image->sourceURL . '"/></a></td><td>' . $product->name->asXML () . '</td><td>' . $product->minPrice->asXML () . '$</td><td><a href="buy.php?delete=' . $product ['id'] . '">Delete</a></td></tr>' );
		}
	}
}
$_SESSION ['total'] = $total;

echo '</table>';
echo '<table border="1">';
echo '</table>';
echo '<p>';
echo 'Total: ' . $_SESSION ['total'];
echo '</p><form method="GET">';
echo '<input name="clear" value="1" type="hidden">';
echo '<input value="Empty Basket" type="submit">';
echo '</form>';
echo '<p>';
echo '</p><form method="GET">';
echo '<fieldset><legend>Find products:</legend>';
echo '<label>Search for items: <input name="search" type="text"><label>';
echo '<input value="Search" type="submit">';
echo '</label></label></fieldset>';
echo '</form>';

if (isset ( $_REQUEST ['search'] )) {
	$searchStr = req ( 'search' );
	searchRequest ( $searchStr );
}

?>