<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$ORDER_ID = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
if (!is_array($arOrder))
	$arOrder = CSaleOrder::GetByID($ORDER_ID);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Коммерческое предложение</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?=LANG_CHARSET?>">
<style>
	table { border-collapse: collapse; }
	table.acc td { border: 1pt solid #000000; padding: 0pt 3pt; line-height: 21pt; }
	table.it td { border: 1pt solid #000000; padding: 0pt 3pt; }
	table.sign td { font-weight: bold; vertical-align: bottom; }
</style>
</head>

<?

if ($_REQUEST['BLANK'] == 'Y')
	$blank = true;

$pageWidth  = 595.28;
$pageHeight = 841.89;

$background = '#ffffff';
if (CSalePaySystemAction::GetParamValue('BACKGROUND', false))
{
	$path = CSalePaySystemAction::GetParamValue('BACKGROUND', false);
	if (intval($path) > 0)
	{
		if ($arFile = CFile::GetFileArray($path))
			$path = $arFile['SRC'];
	}

	$backgroundStyle = CSalePaySystemAction::GetParamValue('BACKGROUND_STYLE', false);
	if (!in_array($backgroundStyle, array('none', 'tile', 'stretch')))
		$backgroundStyle = 'none';

	if ($path)
	{
		switch ($backgroundStyle)
		{
			case 'none':
				$background = "url('" . $path . "') 0 0 no-repeat";
				break;
			case 'tile':
				$background = "url('" . $path . "') 0 0 repeat";
				break;
			case 'stretch':
				$background = sprintf(
					"url('%s') 0 0 repeat-y; background-size: %.02fpt %.02fpt",
					$path, $pageWidth, $pageHeight
				);
				break;
		}
	}
}

$margin = array(
	'top' => intval(CSalePaySystemAction::GetParamValue('MARGIN_TOP', false) ?: 15) * 72/25.4,
	'right' => intval(CSalePaySystemAction::GetParamValue('MARGIN_RIGHT', false) ?: 15) * 72/25.4,
	'bottom' => intval(CSalePaySystemAction::GetParamValue('MARGIN_BOTTOM', false) ?: 15) * 72/25.4,
	'left' => intval(CSalePaySystemAction::GetParamValue('MARGIN_LEFT', false) ?: 20) * 72/25.4
);

$width = $pageWidth - $margin['left'] - $margin['right'];

?>

<body style="margin: 0pt; padding: 0pt;"<? if ($_REQUEST['PRINT'] == 'Y') { ?> onload="setTimeout(window.print, 0);"<? } ?>>

<div style="margin: 0pt; padding: <?=join('pt ', $margin); ?>pt; width: <?=$width; ?>pt; background: <?=$background; ?>">
<table width="100%" style="padding: 0pt; vertical-align: top;">
	<tr>
		<td style="padding-right: 5pt; padding-bottom: 5pt;">
			<?
			$pathToLogo = CSalePaySystemAction::GetParamValue("PATH_TO_LOGO", false);
			if ($pathToLogo)
			{
				$imgParams = CFile::_GetImgParams(CSalePaySystemAction::GetParamValue('PATH_TO_LOGO', false));
				$imgWidth = $imgParams['WIDTH'] * 96 / (intval(CSalePaySystemAction::GetParamValue('LOGO_DPI', false)) ?: 96);
				?><img src="<?=$imgParams['SRC']; ?>" width="<?=$imgWidth; ?>" /><?
			}
			unset($pathToLogo);
			?>
		</td>
		<td></td>
		<td align="right" style="vertical-align: top;">
			<b><?=CSalePaySystemAction::GetParamValue("SELLER_NAME", false); ?></b>
			<?
			$sellerAddr = CSalePaySystemAction::GetParamValue("SELLER_ADDRESS", false);
			if ($sellerAddr)
			{
				if (is_array($sellerAddr))
					$sellerAddr = implode(', ', $sellerAddr);
				?><br><b><?= $sellerAddr ?></b><?
			}
			unset($sellerAddr);
			$sellerPhone = CSalePaySystemAction::GetParamValue("SELLER_PHONE", false);
			if ($sellerPhone)
			{
				?><br><b><?=sprintf("Тел.: %s", $sellerPhone); ?></b><?
			}
			unset($sellerPhone);
			?>
		</td>
	</tr>
</table>
<?if (CSalePaySystemAction::GetParamValue("SELLER_BANK", false))
{
	$sellerBank = sprintf(
		"%s %s",
		CSalePaySystemAction::GetParamValue("SELLER_BANK", false),
		CSalePaySystemAction::GetParamValue("SELLER_BCITY", false)
	);
	$sellerRs = CSalePaySystemAction::GetParamValue("SELLER_RS", false);
}
else
{
	$rsPattern = '/\s*\d{10,100}\s*/';

	$sellerBank = trim(preg_replace($rsPattern, ' ', CSalePaySystemAction::GetParamValue("SELLER_RS", false)));

	preg_match($rsPattern, CSalePaySystemAction::GetParamValue("SELLER_RS", false), $matches);
	$sellerRs = trim($matches[0]);
}?>
<br>
<table width="100%">
	<colgroup>
		<col width="50%">
		<col width="0">
		<col width="50%">
	</colgroup>
	<tr>
		<td></td>
		<td style="font-size: 1.5em; font-weight: bold; text-align: center;"><nobr><?=sprintf(
			"КОММЕРЧЕСКОЕ ПРЕДЛОЖЕНИЕ № %s от %s",
			htmlspecialcharsbx($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"]),
			CSalePaySystemAction::GetParamValue("DATE_INSERT", false)
		); ?></nobr></td>
		<td></td>
	</tr>
<? if (CSalePaySystemAction::GetParamValue("ORDER_SUBJECT", false)) { ?>
	<tr>
		<td></td>
		<td><?=CSalePaySystemAction::GetParamValue("ORDER_SUBJECT", false); ?></td>
		<td></td>
	</tr>
<? } ?>
<? if (CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false)) { ?>
	<tr>
		<td></td>
		<td><?=sprintf(
			"Срок действия %s",
			ConvertDateTime(CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false), FORMAT_DATE)
				?: CSalePaySystemAction::GetParamValue("DATE_PAY_BEFORE", false)
		); ?></td>
		<td></td>
	</tr>
<? } ?>
</table>
<br>
<br>
<?$userFields = array();
for($i = 1; $i <= 5; $i++)
{
	$fildValue = CSalePaySystemAction::GetParamValue("USER_FIELD_{$i}", false);
	if($fildValue)
	{
		$userFields[] = $fildValue;
	}
}?>
<?if (CSalePaySystemAction::GetParamValue("COMMENT1", false)
	|| CSalePaySystemAction::GetParamValue("COMMENT2", false)
	|| !empty($userFields)) { ?>
<b>Условия и комментарии</b>
<br>
	<? if (CSalePaySystemAction::GetParamValue("COMMENT1", false)) { ?>
	<?=nl2br(HTMLToTxt(preg_replace(
		array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
		htmlspecialcharsback(CSalePaySystemAction::GetParamValue("COMMENT1", false))
	), '', array(), 0)); ?>
	<br>
	<br>
	<? } ?>
	<? if (CSalePaySystemAction::GetParamValue("COMMENT2", false)) { ?>
	<?=nl2br(HTMLToTxt(preg_replace(
		array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
		htmlspecialcharsback(CSalePaySystemAction::GetParamValue("COMMENT2", false))
	), '', array(), 0)); ?>
	<br>
	<br>
	<? } ?>
	<?foreach($userFields as &$userField){?>
		<?=nl2br(HTMLToTxt(preg_replace(
				array('#</div>\s*<div[^>]*>#i', '#</?div>#i'), array('<br>', '<br>'),
				htmlspecialcharsback($userField)
			), '', array(), 0));?>
		<br>
		<br>
	<?}
	unset($userField);?>
<? } ?>

<br>
<br>
<?
$arBasketItems = CSalePaySystemAction::GetParamValue("BASKET_ITEMS", false);
if(!is_array($arBasketItems))
	$arBasketItems = array();

$vat = 0;
if (!empty($arBasketItems))
{
	$arBasketItems = getMeasures($arBasketItems);

	$arCells = array();
	$arProps = array();

	$n = 0;
	$sum = 0.00;
	$bShowDiscount = false;
	foreach($arBasketItems as &$arBasket)
	{
		$productName = $arBasket["NAME"];
		if ($productName == "OrderDelivery")
			$productName = "Доставка";
		else if ($productName == "OrderDiscount")
			$productName = "Скидка";

		// discount
		$discountValue = '0%';
		$discountSum = 0.0;
		$discountIsSet = false;
		if (is_array($arBasket['CRM_PR_FIELDS']))
		{
			if (isset($arBasket['CRM_PR_FIELDS']['DISCOUNT_TYPE_ID'])
				&& isset($arBasket['CRM_PR_FIELDS']['DISCOUNT_RATE'])
				&& isset($arBasket['CRM_PR_FIELDS']['DISCOUNT_SUM']))
			{
				if ($arBasket['CRM_PR_FIELDS']['DISCOUNT_TYPE_ID'] === \Bitrix\Crm\Discount::PERCENTAGE)
				{
					$discountValue = round(doubleval($arBasket['CRM_PR_FIELDS']['DISCOUNT_RATE']), 2).'%';
					$discountSum = round(doubleval($arBasket['CRM_PR_FIELDS']['DISCOUNT_SUM']), 2);
					$discountIsSet = true;
				}
				else if ($arBasket['CRM_PR_FIELDS']['DISCOUNT_TYPE_ID'] === \Bitrix\Crm\Discount::MONETARY)
				{
					$discountSum = round(doubleval($arBasket['CRM_PR_FIELDS']['DISCOUNT_SUM']), 2);
					$discountValue = SaleFormatCurrency($discountSum, $arBasket["CURRENCY"], false);
					$discountIsSet = true;
				}
			}
		}
		if ($discountIsSet && $discountSum > 0)
			$bShowDiscount = true;
		unset($discountIsSet);

		if ($bShowDiscount
			&& isset($arBasket['CRM_PR_FIELDS']['TAX_INCLUDED'])
			&& isset($arBasket['CRM_PR_FIELDS']['PRICE_NETTO'])
			&& isset($arBasket['CRM_PR_FIELDS']['PRICE_BRUTTO']))
		{
			if ($arBasket['CRM_PR_FIELDS']['TAX_INCLUDED'] === 'Y')
				$unitPrice = $arBasket['CRM_PR_FIELDS']["PRICE_BRUTTO"];
			else
				$unitPrice = $arBasket['CRM_PR_FIELDS']["PRICE_NETTO"];
		}
		else
		{
			$unitPrice = $arBasket["PRICE"];
		}
		$arCells[++$n] = array(
			1 => $n,
			htmlspecialcharsbx($productName),
			roundEx($arBasket["QUANTITY"], SALE_VALUE_PRECISION),
			$arBasket["MEASURE_NAME"] ? htmlspecialcharsbx($arBasket["MEASURE_NAME"]) : 'шт.',
			SaleFormatCurrency($unitPrice, $arBasket["CURRENCY"], true),
			$discountValue,
			roundEx($arBasket["VAT_RATE"]*100, SALE_VALUE_PRECISION) . "%",
			SaleFormatCurrency($arBasket["PRICE"] * $arBasket["QUANTITY"], $arBasket["CURRENCY"], true)
		);

		if(isset($arBasket["PROPS"]) && is_array($arBasket["PROPS"]))
		{
			$arProps[$n] = array();
			foreach ($arBasket["PROPS"] as $vv)
				$arProps[$n][] = htmlspecialcharsbx(sprintf("%s: %s", $vv["NAME"], $vv["VALUE"]));
		}

		$sum += doubleval($arBasket["PRICE"] * $arBasket["QUANTITY"]);
		$vat = max($vat, $arBasket["VAT_RATE"]);
	}
	unset($arBasket);

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]) > 0)
	{
		$arDelivery_tmp = CSaleDelivery::GetByID($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DELIVERY_ID"]);

		$sDeliveryItem = "Доставка";
		if (strlen($arDelivery_tmp["NAME"]) > 0)
			$sDeliveryItem .= sprintf(" (%s)", $arDelivery_tmp["NAME"]);
		$arCells[++$n] = array(
			1 => $n,
			htmlspecialcharsbx($sDeliveryItem),
			1,
			'',
			SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			),
			'',
			roundEx($vat*100, SALE_VALUE_PRECISION) . "%",
			SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			)
		);

		$sum += doubleval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE_DELIVERY"]);
	}

	$items = $n;

	if ($sum < $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["PRICE"])
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			null,
			"Подытог:",
			SaleFormatCurrency($sum, $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], true)
		);
	}

	$arTaxList = CSalePaySystemAction::GetParamValue("TAX_LIST", false);
	if(!is_array($arTaxList))
	{
		$dbTaxList = CSaleOrderTax::GetList(
			array("APPLY_ORDER" => "ASC"),
			array("ORDER_ID" => $ORDER_ID)
		);

		$arTaxList = array();
		while ($arTaxInfo = $dbTaxList->Fetch())
		{
			$arTaxList[] = $arTaxInfo;
		}
	}

	if(!empty($arTaxList))
	{
		foreach($arTaxList as &$arTaxInfo)
		{
			$arCells[++$n] = array(
				1 => null,
				null,
				null,
				null,
				null,
				null,
				htmlspecialcharsbx(sprintf(
					"%s%s%s:",
					($arTaxInfo["IS_IN_PRICE"] == "Y") ? "В том числе " : "",
					$arTaxInfo["TAX_NAME"],
					($vat <= 0 && $arTaxInfo["IS_PERCENT"] == "Y")
						? sprintf(' (%s%%)', roundEx($arTaxInfo["VALUE"],SALE_VALUE_PRECISION))
						: ""
				)),
				SaleFormatCurrency(
					$arTaxInfo["VALUE_MONEY"],
					$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
					true
				)
			);
		}
		unset($arTaxInfo);
	}
	else
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			null,
			htmlspecialcharsbx("НДС:"),
			htmlspecialcharsbx("Без НДС")
		);
	}

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"]) > 0)
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			null,
			"Уже оплачено:",
			SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SUM_PAID"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			)
		);
	}

	if (DoubleVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"]) > 0)
	{
		$arCells[++$n] = array(
			1 => null,
			null,
			null,
			null,
			null,
			null,
			"Скидка:",
			SaleFormatCurrency(
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DISCOUNT_VALUE"],
				$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
				true
			)
		);
	}

	$arCells[++$n] = array(
		1 => null,
		null,
		null,
		null,
		null,
		null,
		"Итого:",
		SaleFormatCurrency(
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
			$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
			true
		)
	);
}

$arCurFormat = CCurrencyLang::GetCurrencyFormat($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"]);
$currency = trim(str_replace('#', '', $arCurFormat['FORMAT_STRING']));
?>
<table class="it" width="100%">
	<tr>
		<td><nobr>№</nobr></td>
		<td><nobr>Наименование товара</nobr></td>
		<td><nobr>Кол-во</nobr></td>
		<td><nobr>Ед.</nobr></td>
		<td><nobr>Цена, <?=$currency; ?></nobr></td>
		<? if ($bShowDiscount) { ?>
		<td><nobr>Скидка</nobr></td>
		<? } ?>
		<? if ($vat > 0) { ?>
		<td><nobr>Ставка НДС</nobr></td>
		<? } ?>
		<td><nobr>Сумма, <?=$currency; ?></nobr></td>
	</tr>
<?

$rowsCnt = count($arCells);
for ($n = 1; $n <= $rowsCnt; $n++)
{
	$accumulated = 0;

?>
	<tr valign="top">
		<? if (!is_null($arCells[$n][1])) { ?>
		<td align="center"><?=$arCells[$n][1]; ?></td>
		<? } else {
			$accumulated++;
		} ?>
		<? if (!is_null($arCells[$n][2])) { ?>
		<td align="left"
			style="word-break: break-word; word-wrap: break-word; <? if ($accumulated) {?>border-width: 0pt 1pt 0pt 0pt; <? } ?>"
			<? if ($accumulated) { ?>colspan="<?=($accumulated+1); ?>"<? $accumulated = 0; } ?>>
			<?=$arCells[$n][2]; ?>
			<? if (isset($arProps[$n]) && is_array($arProps[$n])) { ?>
			<? foreach ($arProps[$n] as $property) { ?>
			<br>
			<small><?=$property; ?></small>
			<? } ?>
			<? } ?>
		</td>
		<? } else {
			$accumulated++;
		} ?>
		<? for ($i = 3; $i <= 8; $i++) { ?>
			<? if (!is_null($arCells[$n][$i])) { ?>
				<? if (($i !== 6 || $bShowDiscount) && ($i != 7 || $vat > 0) || is_null($arCells[$n][2])) { ?>
				<td align="right"
					<? if ($accumulated) { ?>
					style="border-width: 0pt 1pt 0pt 0pt"
					colspan="<?= ($accumulated + ($vat > 0) - !$bShowDiscount) ?>"
					<? $accumulated = 0; } ?>>
					<nobr><?=$arCells[$n][$i]; ?></nobr>
				</td>
				<? }
			} else {
				$accumulated++;
			}
		} ?>
	</tr>
<?

}

?>
</table>
<br>

<?=sprintf(
	"Всего наименований %s, на сумму %s",
	$items,
	SaleFormatCurrency(
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
		false
	)
); ?>
<br>

<b>
<?

if (in_array($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"], array("RUR", "RUB")))
{
	echo Number2Word_Rus($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]);
}
else
{
	echo SaleFormatCurrency(
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"],
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"],
		false
	);
}

$sellerInfo = array(
	'NAME' => CSalePaySystemAction::GetParamValue("SELLER_NAME", false),
	'ADDRESS' => CSalePaySystemAction::GetParamValue("SELLER_ADDRESS", false),
	'PHONE' => CSalePaySystemAction::GetParamValue("SELLER_PHONE", false),
	'INN' => CSalePaySystemAction::GetParamValue("SELLER_INN", false),
	'KPP' => CSalePaySystemAction::GetParamValue("SELLER_KPP", false),
	'RS' => CSalePaySystemAction::GetParamValue("SELLER_RS", false),
	'BANK' => CSalePaySystemAction::GetParamValue("SELLER_BANK", false),
	'BIK' => CSalePaySystemAction::GetParamValue("SELLER_BIK", false),
	'BANK_CITY' => CSalePaySystemAction::GetParamValue("SELLER_BCITY", false),
	'KS' => CSalePaySystemAction::GetParamValue("SELLER_KS", false),

);

$customerInfo = array(
	'NAME' => CSalePaySystemAction::GetParamValue("BUYER_NAME", false),
	'ADDRESS' => CSalePaySystemAction::GetParamValue("BUYER_ADDRESS", false),
	'PHONE' => CSalePaySystemAction::GetParamValue("BUYER_PHONE", false),
	'INN' => CSalePaySystemAction::GetParamValue("BUYER_INN", false)
);

?>
<br>
<br>
<table width="100%">
	<colgroup>
		<col width="50%">
		<col width="50%">
	</colgroup>
	<tr>
		<td><?if($sellerInfo['NAME']):?><b><?=$sellerInfo['NAME'];?></b><?endif;?></td>
		<td><?if($customerInfo['NAME']):?><b><?=$customerInfo['NAME'];?></b><?endif;?></td>
	</tr>
	<tr>
		<td><?
			$sellerAddr = $sellerInfo['ADDRESS'];
			if($sellerAddr)
			{
				if (is_array($sellerAddr))
				{
					if (!empty($sellerAddr))
						$sellerAddr = implode(', ', $sellerAddr);
					else
						$sellerAddr = '';
				}
				if ($sellerAddr)
				{
					?>Адрес: <?= $sellerAddr ?><?
				}
			}
			unset($sellerAddr);
			?></td>
		<td><?
			$customerAddr = $customerInfo['ADDRESS'];
			if($customerAddr)
			{
				if (is_array($customerAddr))
				{
					if (!empty($customerAddr))
						$customerAddr = implode(', ', $customerAddr);
					else
						$customerAddr = '';
				}
				if ($customerAddr)
				{
					?>Адрес: <?= $customerAddr ?><?
				}
			}
			unset($customerAddr);
			?></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['PHONE']):?>Телефон: <?=$sellerInfo['PHONE']?><?endif;?></td>
		<td><?if($customerInfo['PHONE']):?>Телефон: <?=$customerInfo['PHONE']?><?endif;?></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['INN']):?>ИНН: <?=$sellerInfo['INN']?><?endif;?></td>
		<td><?if($customerInfo['INN']):?>ИНН: <?=$customerInfo['INN']?><?endif;?></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['KPP']):?>КПП: <?=$sellerInfo['KPP']?><?endif;?></td>
		<td></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['RS']):?>Расчётный счёт: <?=$sellerInfo['RS']?><?endif;?></td>
		<td></td>
	</tr>
	<tr><?
		$sellerBankCity = '';
		if ($sellerInfo['BANK_CITY'])
		{
			$sellerBankCity = $sellerInfo['BANK_CITY'];
			if (is_array($sellerBankCity))
			{
				if (!empty($sellerBankCity))
					$sellerBankCity = implode(', ', $sellerBankCity);
				else
					$sellerBankCity = '';
			}
			else
			{
				$sellerBankCity = str_replace(array("\r\n", "\n", "\r"), ', ', strval($sellerBankCity));
			}
		}
		?>
		<td><?if($sellerInfo['BANK']):?>Банк: <?=$sellerBankCity ? ($sellerInfo['BANK'].', '.$sellerBankCity) : $sellerInfo['BANK']?><?endif;?></td>
		<td></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['BIK']):?>БИК: <?=$sellerInfo['BIK']?><?endif;?></td>
		<td></td>
	</tr>
	<tr>
		<td><?if($sellerInfo['KS']):?>Корреспондентский счет: <?=$sellerInfo['KS']?><?endif;?></td>
		<td></td>
	</tr>
</table>
<br>
<br>

<? if (!$blank) { ?>
<div style="position: relative; "><?=CFile::ShowImage(
	CSalePaySystemAction::GetParamValue("PATH_TO_STAMP", false),
	160, 160,
	'style="position: absolute; left: 40pt; "'
); ?></div>
<? } ?>

<div style="position: relative">
	<table class="sign">
		<? if (CSalePaySystemAction::GetParamValue("SELLER_DIR_POS", false)) { ?>
		<tr>
			<td style="width: 150pt; "><?=CSalePaySystemAction::GetParamValue("SELLER_DIR_POS", false); ?></td>
			<td style="width: 160pt; border: 1pt solid #000000; border-width: 0pt 0pt 1pt 0pt; text-align: center; ">
				<? if (!$blank) { ?>
				<?=CFile::ShowImage(CSalePaySystemAction::GetParamValue("SELLER_DIR_SIGN", false), 200, 50); ?>
				<? } ?>
			</td>
			<td>
				<? if (CSalePaySystemAction::GetParamValue("SELLER_DIR", false)) { ?>
				(<?=CSalePaySystemAction::GetParamValue("SELLER_DIR", false); ?>)
				<? } ?>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<? } ?>
		<? if (CSalePaySystemAction::GetParamValue("SELLER_ACC_POS", false)) { ?>
		<tr>
			<td style="width: 150pt; "><?=CSalePaySystemAction::GetParamValue("SELLER_ACC_POS", false); ?></td>
			<td style="width: 160pt; border: 1pt solid #000000; border-width: 0pt 0pt 1pt 0pt; text-align: center; ">
				<? if (!$blank) { ?>
				<?=CFile::ShowImage(CSalePaySystemAction::GetParamValue("SELLER_ACC_SIGN", false), 200, 50); ?>
				<? } ?>
			</td>
			<td>
				<? if (CSalePaySystemAction::GetParamValue("SELLER_ACC", false)) { ?>
				(<?=CSalePaySystemAction::GetParamValue("SELLER_ACC", false); ?>)
				<? } ?>
			</td>
		</tr>
		<? } ?>
	</table>
</div>

</div>

</body>
</html>