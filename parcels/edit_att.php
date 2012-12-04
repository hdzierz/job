<html>
<head>
<title>Add Date</title>
<script language="JavaScript">
<!--

function load(date) {
	
	window.dialogArguments.date = date;
    window.close();
}

// -->
</script>
</head>
<body>
	<form>
		<input type="text" name="date" id="date" />
		<input type="button" value="Load" onClick="load(this.form.date.value)">
	</form>
</body>
</html>