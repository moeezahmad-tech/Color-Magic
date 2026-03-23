<?php
$hex = $_GET['hex'] ?? '000000'; // Get the hex from the URL
// Here you would pull palette data from your /data/ folder based on $hex
?>
<!DOCTYPE html>
<html>
<head>
    <title>#<?php echo $hex; ?> Color Hex | Palettes & Schemes - Color Magic</title>
    <meta name="description" content="Get professional color palettes and matching schemes for hex code #<?php echo $hex; ?>. Perfect for graphic designers.">
</head>
<body style="background-color: #<?php echo $hex; ?>;">
    <h1>Details for Color #<?php echo $hex; ?></h1>
    </body>
</html>