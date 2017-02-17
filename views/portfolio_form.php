<head>
<link rel="stylesheet" type="text/css" href="portfolio_style.css">
</head>
<form action="portfolio.php" method="post">
<fieldset>
<div class = "port-group">
    Cash <input class="form-control" name="cash" placeholder="Amount" type="text"> &nbsp
    Date <input class="form-control" name="date" placeholder="Date" type="date">&nbsp &nbsp &nbsp
    <input  value="Add to portfolio" type="button">&nbsp 
    <input  value="Cancel" type="button">
</div>
<br>
<div>
Add Symbol <input class="form-control" name="symbol" type="text"> &nbsp &nbsp &nbsp
<input  value="Add to portfolio" type="button"> &nbsp  
<input  value="+Add transaction data" type="button"> &nbsp 

</div>

<br>

<div>
<table>
<tr align="left">
    <th style="width:100px">Type </th>
    <th style="width:150px">Date </th>
    <th style="width:100px"> Shares</th>
    <th style="width:100px"> Price </th>
    <th style="width:100px"> Commission </th>
    <th> Notes </th>
</tr>
</table>
</div>

<div>
<tr>
    <input class="input-control" name="Type" type="text"> 
    <input class="date-control" name="Date" type="date">
    <input class="input-control" name="Shares" type="text">
    <input class="input-control" name="Price" type="text">
    <input class="input-control" name="Commision" type="text">
    <input class="notes-control" name="Notes" type="text">
</tr>
</div>
<br>
   <input name="check_cash" type="checkbox"> Deduct from Cash


</fieldset>
</form>




 
