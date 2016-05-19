<!DOCTYPE HTML>
<html>

<head>
	<title>Practice</title>
	<meta charset="UTF-8">
</head>	
	
	
<body>


<form action="order-submit.php" method="post">
	<div>
	List of items available -->
	
	<select name="items">
		
		<?php foreach (glob ("items/*.JPG") as $file):
		
 			$item = basename($file,".JPG"); ?>
 			
 			<option value="<?= $item ?>"><?= $item ?></option>
		
		
		<?php endforeach; ?>
			 
	
		</select>
	</div>
	<div>	
		
	Quantity --> 
	
	<input type="text" maxlength="2" value="3" name="qty"/>
	</div>
	<input type="checkbox" name="CheckBox" value="a"/>
	<input type="checkbox" name="CheckBox" value="b"/>
	<input type="checkbox" name="CheckBox" value="c"/>
	<input type="checkbox" name="CheckBox" value="d"/>
	<input type="file" name="Hello" />
	
	<div>
	<input type="submit" name="order" value="order"/>
	<input type="reset"/>
	</div>
	
		
	
	
	
</form>
	
	
	
</body>	
	
	
	
	
</html>

