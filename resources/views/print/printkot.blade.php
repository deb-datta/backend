<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BILL</title>
    <link href="labels.css" rel="stylesheet" type="text/css" >
    <style>
    body {
        width: 3in;
        margin: 0in .05in;
		font-family: 'Arial','Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
		
        }
    .label{
        /* Avery 5160 labels -- CSS and HTML by MM at Boulder Information Services */<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ANN</title>
    
    <style>
    .invoice-box {
        max-width: 3in;
        margin: auto;
        padding: 0.05in;
        font-size: 16px;
        line-height: 24px;
        font-family: 'Arial','Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        max-width: 3in;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 0px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(3) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #000;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }
    
    
	table tr.heading td {
        background:none;
        border-bottom: 1px solid #000;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #000;
    }
    
    .invoice-box table tr.item.last td {
        border-bottom: none;
    }
    
    .invoice-box table tr.total td:nth-child(2) {
	
        border-top: 1px solid #000;
        font-weight: bold;
    }
    
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            display: block;
            text-align: right;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(1) {
        text-align: left;
    }
	.text-right{
		text-align: right;
	}
    </style>
</head>

<body style="width:2.7in" onload="window.print();window.close()">
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2" style="text-align:center;font-size:15px">
                   
                                
								<b>NERDY BEAN COFFEE HAUS -K.O.T.</b>
								<b>BIN: 000785888</b>
                            
                </td>
            </tr>
            
            <tr class="item">
                            <td>
								&nbsp;
							</td>
							<td >
								<div style="text-align:right;"> <?=$orderinfo->datetime;?> </div>
							</td>
            </tr>
			<tr class="item">
                            <td>
								<div style="text-align:left;">Type :<?=strtoupper($orderinfo->ordertype);?> </div>
							</td>
							<td >
								<div style="text-align:right;">TTN: <?=$orderinfo->tabletokennos;?> </div>
							</td>
            </tr>
			<tr class="item">
                            <td colspan="2"  >&nbsp;
							</td>
            </tr>
			<tr  style="border-top: 1px solid #000;">
							<td>
								<div style="text-align:left;">ON:<?=$orderinfo->id;?> </div>
							</td>
                            <td  >
								<div style="text-align:right;">SP: <?=strtoupper($orderinfo->username);?></div>
							</td>
            </tr>
			<tr  style="border-top: 1px solid #000;">
							<td colspan="2">
                                <?php if($discountinfo){ ?>
								<div style="text-align:left;">For: <?= $discountinfo->name;?></div><?php } ?>
							</td>
            </tr>
            
           </table> 
           <table cellpadding="0" cellspacing="0" style="width:2.7in;border-collapse:collapse; ">
            <tr class="heading" style="border-collapse:collapse;border-bottom: 1px solid black;">
                <td style="text-align: left;">
                    <b>Item</b>
                </td>
                
                <td style="text-align: right;">
                    <b>Qty.</b>
                </td>
                
            </tr>
            
            
            <?php for($i=0;$i<sizeof($mainmnue);$i++){ ?>    
            <tr >
                <td colspan="3" style="text-align: left;">
                    <?=$mainmnue[$i]->name;?> (<?=$mainmnue[$i]->size;?>) <?php if(sizeof($mainmnue[$i]->toppings)){?> with <?php for($j=0;$j<sizeof($mainmnue[$i]->toppings);$j++){ if($j>0){echo ", ";} echo $mainmnue[$i]->toppings[$j]->toppingname; ?><?php }?><?php }?>
                    <?php if(($mainmnue[$i]->offrtype=='complement')){?><b> Free </b><?php }?>
                </td>
                
                
            </tr>
			<tr style="border-collapse:collapse;border-bottom: 1px solid black;">
                <td >
                    &nbsp;--
                </td>
                <td style="text-align: right;" >
                   <b> <?=$mainmnue[$i]->qnites;?> </b>
                </td>
                
            </tr>
			<?php } ?>
			
        </table>
		
<h4 style="font-size: 0.8em;font-weight:bold; color: #000;text-align: right;"> Powered by  - Indexer </h4>

    </div>
</body>
</html>