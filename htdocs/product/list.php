<?php

/* Copyright (C) 2001-2006 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2011      Philippe Grand       <philippe.grand@atoo-net.com>
 * Copyright (C) 2011-2012 Herve Prot           <herve.prot@symeos.com>
 * Copyright (C) 2011      Patrick Mary         <laube@hotmail.fr>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require '../main.inc.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';

$type = GETPOST("type", "alpha");

$canvas = GETPOST("canvas");
$objcanvas = '';
if (!empty($canvas)) {
    require_once DOL_DOCUMENT_ROOT . '/core/class/canvas.class.php';
    $objcanvas = new Canvas($db, $action);
    $objcanvas->getCanvas('product', 'list', $canvas);
}

// Security check
if ($type == 'PRODUCT')
    $result = restrictedArea($user, 'produit', '', '', '', '', '', $objcanvas);
else if ($type == 'SERVICE')
    $result = restrictedArea($user, 'service', '', '', '', '', '', $objcanvas);
else
    $result = restrictedArea($user, 'produit|service', '', '', '', '', '', $objcanvas);

$object = new Product($db);
/*
 * View
 */

llxHeader('', $langs->trans("ProductsAndServices"), $help_url, '', '', '', '');

$title = $langs->trans("ProductsAndServices");

if (!empty($type)) {
    if ($type == "SERVICE") {
        $title = $langs->trans("Services");
    } else {
        $title = $langs->trans("Products");
    }
}

print_fiche_titre($title);
/* ?>
  <div class="dashboard">
  <div class="columns">
  <div class="four-columns twelve-columns-mobile graph">
  <?php $object->graphPieStatus(); ?>
  </div>

  <div class="eight-columns twelve-columns-mobile new-row-mobile graph">
  <?php $object->graphBarStatus(); ?>
  </div>
  </div>
  </div>
  <?php */
print '<div class="with-padding">';

//print start_box($titre,"twelve","16-Companies.png");

/*
 * Barre d'actions
 *
 */

print '<p class="button-height right">';
if ($type == "SERVICE" || empty($type))
    print '<a class="button icon-star" href="' . strtolower(get_class($object)) . '/fiche.php?action=create&type=SERVICE">' . $langs->trans("NewService") . '</a> ';
if ($type == "PRODUCT" || empty($type))
    print '<a class="button icon-star" href="' . strtolower(get_class($object)) . '/fiche.php?action=create&type=PRODUCT">' . $langs->trans("NewProduct") . '</a>';
print "</p>";

$i = 0;
$obj = new stdClass();
print '<table class="display dt_act" id="product" >';
// Ligne des titres
print'<thead>';
print'<tr>';
print'<th>';
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "_id";
$obj->aoColumns[$i]->bUseRendered = false;
$obj->aoColumns[$i]->bSearchable = false;
$obj->aoColumns[$i]->bVisible = false;
$i++;
print'<th class="essential">';
print $title;
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "name";
$obj->aoColumns[$i]->bUseRendered = false;
$obj->aoColumns[$i]->bSearchable = true;
$obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("name", "url");
$i++;
print'<th class="essential">';
print $langs->trans('Label');
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "label";
$obj->aoColumns[$i]->sDefaultContent = "";
$i++;
print'<th class="essential">';
print $langs->trans('Categories');
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "Tag";
$obj->aoColumns[$i]->sClass = "center";
$obj->aoColumns[$i]->sDefaultContent = "";
$obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("Tag", "tag");
$i++;
print'<th class="essential">';
print $langs->trans('SellingPrice');
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "price.price";
$obj->aoColumns[$i]->sDefaultContent = "0.00";
$obj->aoColumns[$i]->sClass = "fright";
$obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("price.price", "price");
$i++;
print'<th class="essential">';
print $langs->trans("DatePriceUTD");
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "price.tms";
$obj->aoColumns[$i]->sClass = "center";
$obj->aoColumns[$i]->bUseRendered = false;
$obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("price.tms", "date");
$i++;
if (empty($type)) {
    print'<th class="essential">';
    print $langs->trans("Type");
    print'</th>';
    $obj->aoColumns[$i] = new stdClass();
    $obj->aoColumns[$i]->mDataProp = "type";
    $obj->aoColumns[$i]->sClass = "center";
    $obj->aoColumns[$i]->sWidth = "60px";
    $obj->aoColumns[$i]->sDefaultContent = "PRODUCT";
    $obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("type", "status");
    $i++;
}
print'<th class="essential">';
print $langs->trans("Status");
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "Status";
$obj->aoColumns[$i]->sClass = "dol_select center";
$obj->aoColumns[$i]->sWidth = "100px";
$obj->aoColumns[$i]->sDefaultContent = "ST_NEVER";
$obj->aoColumns[$i]->fnRender = $object->datatablesFnRender("Status", "status");
$i++;
print'<th class="essential">';
print $langs->trans('Action');
print'</th>';
$obj->aoColumns[$i] = new stdClass();
$obj->aoColumns[$i]->mDataProp = "";
$obj->aoColumns[$i]->sClass = "center content_actions";
$obj->aoColumns[$i]->sWidth = "60px";
$obj->aoColumns[$i]->bSortable = false;
$obj->aoColumns[$i]->sDefaultContent = "";

$url = "product/fiche.php";
$obj->aoColumns[$i]->fnRender = 'function(obj) {
	var ar = [];
	ar[ar.length] = "<a href=\"' . $url . '?id=";
	ar[ar.length] = obj.aData._id.toString();
	ar[ar.length] = "&action=edit&backtopage=' . $_SERVER['PHP_SELF'] . '\" class=\"sepV_a\" title=\"' . $langs->trans("Edit") . '\"><img src=\"' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/edit.png\" alt=\"\" /></a>";
	ar[ar.length] = "<a href=\"\"";
	ar[ar.length] = " class=\"delEnqBtn\" title=\"' . $langs->trans("Delete") . '\"><img src=\"' . DOL_URL_ROOT . '/theme/' . $conf->theme . '/img/delete.png\" alt=\"\" /></a>";
	var str = ar.join("");
	return str;
}';
print'</tr>';
print'</thead>';
print'<tfoot>';
/* input search view */
$i = 0; //Doesn't work with bServerSide
print'<tr>';
print'<th id="' . $i . '"></th>';
$i++;
print'<th id="' . $i . '"><input type="text" placeholder="' . $langs->trans("Search Product") . '" /></th>';
$i++;
print'<th id="' . $i . '"><input type="text" placeholder="' . $langs->trans("Search Label") . '" /></th>';
$i++;
print'<th id="' . $i . '"><input type="text" placeholder="' . $langs->trans("Search category") . '" /></th>';
$i++;
print'<th id="' . $i . '"></th>';
$i++;
print'<th id="' . $i . '"></th>';
$i++;
if (empty($type)) {
    print'<th id="' . $i . '"></th>';
    $i++;
}
print'<th id="' . $i . '"><input type="text" placeholder="' . $langs->trans("Search status") . '" /></th>';
$i++;
print'<th id="' . $i . '"></th>';
$i++;
print'</tr>';
print'</tfoot>';
print'<tbody>';
print'</tbody>';
print "</table>";

if (!empty($type))
    $obj->sAjaxSource = DOL_URL_ROOT . "/core/ajax/listdatatables.php?json=listType&class=" . get_class($object) . "&key=" . $type;
//$obj->bServerSide = true;
//$obj->sDom = 'C<\"clear\">lfrtip';
$object->datatablesCreate($obj, "product", true, true);

//print end_box();
print '</div>'; // end

llxFooter();
?>