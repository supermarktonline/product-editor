<?php

$minstate = intval((isset($_GET['minstate'])) ? $_GET['minstate'] : "0");
$maxstate = intval((isset($_GET['maxstate'])) ? $_GET['maxstate'] : "20");

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id AND status >=:minstate AND status <= :maxstate ORDER BY id ASC');
$stmt->bindValue(":import_id", urldecode($_GET['edit']));
$stmt->bindValue(":minstate", $minstate);
$stmt->bindValue(":maxstate", $maxstate);
$stmt->execute();
$imports = $stmt->fetchAll();


$stmt2 = $db->prepare('SELECT * FROM ingredient ORDER BY name DESC');
$stmt2->execute();
$ingredients = $stmt2->fetchAll();

$stmt_brand = $db->prepare('SELECT DISTINCT "productBrand de_AT" FROM fdata ORDER BY "productBrand de_AT" ASC');
$stmt_brand->execute();
$extractBrand = function ($row) {
    return $row['productBrand de_AT'];
};
$brands = array_map($extractBrand, $stmt_brand->fetchAll());

$stmt_corp = $db->prepare('SELECT DISTINCT "productCorporation de_AT" FROM fdata ORDER BY "productCorporation de_AT" ASC');
$stmt_corp->execute();
$extractCorp = function ($row) {
    return $row['productCorporation de_AT'];
};
$corporations = array_map($extractCorp, $stmt_corp->fetchAll());

$stmt3 = $db->prepare('SELECT * FROM category ORDER BY segment_description_en,family_description_en,class_description_en,brick_description_en');
$stmt3->execute();
$categories = $stmt3->fetchAll();


$stmt4 = $db->prepare('SELECT * FROM tag WHERE gs1_attribute_value_code IS NULL ORDER BY name_de');
$stmt4->execute();
$tags = $stmt4->fetchAll();

$stmt5 = $db->prepare('SELECT name,media_path FROM import WHERE id = :id');
$stmt5->bindValue(":id", urldecode($_GET['edit']));
$stmt5->execute();
$properties = $stmt5->fetch();


$stmt6 = $db->prepare('SELECT * FROM taggroup ORDER BY name DESC');
$stmt6->execute();
$taggroups = $stmt6->fetchAll();


$name = $properties["name"];
$media_path = $properties["media_path"];
?>
<!DOCTYPE html>
<html>
<?php include("header.php"); ?>
<body>


<nav class="navbar navbar-default this-navbar">
    <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand">Produkteditor</a></div>
        <div id="menu-navbar">
            <ul class="nav navbar-nav">
                <li><a href="/" class="dropdown-toggle">&laquo; Zurück zur Listenverwaltung</a></li>

                <li>
                    <input type="text" id="claim_name" placeholder="Benutzername" value=""/>
                </li>

                <li>
                    <form action="" method="get">
                        <input type="hidden" name="edit" value="<?php echo $_GET['edit']; ?>"/>
                        <input type="text" name="minstate" value="<?php echo $minstate; ?>" size="2"/>
                        <input type="text" name="maxstate" value="<?php echo $maxstate; ?>" size="2"/>
                        <input type="submit" value="Status filtern"/>
                        <a href="#" id="show_status_info">(?)</a>
                    </form>
                </li>
                <li>
                    <span class="admin-area admin-hint">Admin Modus aktiv</span>
                </li>
            </ul>
        </div>
        <div class="navbar-right-label">
            <?php echo $name . ", " . urldecode($_GET['edit']); ?>
        </div>
    </div>
</nav>


<div id="table-container-wrapper">
    <div id="table-container">
        <table id="product-table" class="table table-striped">
            <tr class="head-row">
                <th>#</th>
                <th>Status</th>
                <th>Reservierung</th>
                <th>Name</th>
                <th>EAN Code</th>
                <th>Marke</th>
            </tr>

            <?php
            // <tr class="row-active">
            $i = 0;
            foreach ($imports as $imp) {
                $i++;
                $nextImpId = "-";
                if (array_key_exists($i, $imports)) $nextImpId = $imports[$i]["id"];
                ?>
                <tr data-open-next-id="<?php echo $nextImpId; ?>" data-open_edit_id="<?php echo $imp["id"]; ?>">
                    <td><?php echo $imp["id"]; ?></td>
                    <td><span class="eds eds-state-<?php echo $imp["status"]; ?>"><?php echo $imp["status"]; ?></span>
                    </td>
                    <td class="reserve"><input type="checkbox"
                                               data-res="<?php echo $imp["id"]; ?>" <?php echo ($imp["reserved_by"]) ? 'checked="checked"' : ''; ?> />
                        <span class="reserved_by"><?php echo $imp["reserved_by"]; ?></span></td>
                    <td data-nfieldu="<?php echo $imp["id"]; ?>"><?php
                        if (strlen($tp = $imp["productName de_AT"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productName de_DE"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productName en_US"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productName es_ES"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productName fr_FR"]) > 1) {
                            echo $tp;
                        }
                        ?></td>
                    <td><?php echo preg_replace('/^.*~/', '', $imp["articleEanCode"] . $imp["articleBarCode"]); ?></td>
                    <td data-nfieldb="<?php echo $imp["id"]; ?>"><?php
                        if (strlen($tp = $imp["productBrand de_AT"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productBrand de_DE"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productBrand en_US"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productBrand es_ES"]) > 1) {
                            echo $tp;
                        } else if (strlen($tp = $imp["productBrand fr_FR"]) > 1) {
                            echo $tp;
                        }
                        ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </div>
</div>

<div id="isreserved-container" class="no-show">
    <div id="isreserved-container-inner">
        <p>Dieses Produkt ist für einen anderen Benutzer reserviert. <span class="no-admin-area">Um es zu bearbeiten, muss die Reservierung aufgehoben werden.</span>
        </p>
    </div>
</div>

<div id="name-required-container" class="no-show">
    <div id="name-required-container-inner">
        <br>
        <p>Produkte können nur editiert werden, wenn ein Benutzername angegeben worden ist.</p>
    </div>
</div>

<div id="main-container" class="no-show">

    <!-- Images -->


    <div id="img-container">

        <div id="current_image_wrapper">

        </div>

        <div id="thumb-container" class="clearfix">

        </div>

    </div>


    <!-- input container -->
    <div id="input-container">
        <ul id="tab-list" class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" id="li-tab1" class="active"><a href="#tab1" aria-controls="tab1" role="tab"
                                                                   data-toggle="tab">Allgemein & Nährwerte</a></li>
            <li role="presentation" id="li-tab2"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Inhaltsstoffe,
                    Kategorisierung, Tagging</a></li>
        </ul>


        <div id="tab-menu" class="tab-content">
            <div id="tab1" role="tabpanel" class="tab-pane active">
                <form methop="post" action="/save" id="product-form">
                    <div id="form-readonly">
                        <br>
                        <h1>Dateneingabe ist deaktiviert bis das Produkt reserviert wurde.</h1>
                        <br>
                        <br>
                    </div>
                    <fieldset id="edit-form" disabled="disabled">
                        <div class="form-group clearfix">
                            <div class="lit c25">
                                <label class="control-label">Name</label>
                                <input type="text" id="name" value="" class="form-control">
                            </div>

                            <div class="lit c25">
                                <label class="control-label">Firmenname</label>
                                <input type="text" id="company" value="" class="form-control">
                            </div>

                            <div class="lit c2">
                                <label class="control-label">Marke</label>
                                <input type="text" id="brand" value="" class="form-control">
                            </div>

                            <div class="lit c25">
                                <label class="control-label">Herkunftsland</label>
                                <select id="origin" class="form-control">
                                    <option value="">-- Unbekannt --</option>
                                    <?php echo get_german_country_options(); ?>
                                </select>
                            </div>


                            <div class="lit c15">
                                <label class="control-label">Lagerung</label>
                                <select id="store" class="form-control">
                                    <option value="normal">Normal</option>
                                    <option value="cooled">Kühl</option>
                                    <option value="frozen">Tiefgekühlt</option>
                                    <option value="not_cooled">Nicht gekühlt</option>
                                </select>
                            </div>

                            <div class="lit c3">
                                <label class="control-label">Behälter (wenn leer, benutzerdefiniert)</label>
                                <select id="container" style="display:inline-block">
                                    <option></option>
                                    <option value="Packung">Packung</option>
                                    <option value="Beutel">Beutel</option>
                                    <option value="Karton">Karton</option>
                                    <option value="Dose">Dose</option>
                                    <option value="Glas">Glas</option>
                                    <option value="Glasflasche">Glasflasche</option>
                                    <option value="Konststoffflasche">Kunststoffflasche</option>
                                    <option value="Riegel">Riegel</option>
                                    <option value="Tafel">Tafel</option>
                                    <option value="Tafel">Tafel</option>
                                </select>
                                <input type="text" id="container_custom" value="" class="form-control"
                                       style="max-width:150px; display:inline-block">
                            </div>


                        </div>
                        <div class="form-group clearfix">

                            <div class="lit" style="width:75%">
                                <label class="control-label">Beschreibung</label>
                                <textarea id="description" rows="2" class="form-control"></textarea>
                            </div>

                            <div class="lit" style="width:25%;">
                                <label class="control-label">Inhalt (zur Preisberechnung)</label>
                                <div style="white-space:nowrap">
                                    <input type="text" id="weight_amount" value="" class="form-control"
                                           style="width:100px; display:inline">
                                    <select id="weight_amount_unit" class="inline">
                                        <option></option>
                                        <option>g</option>
                                        <option>kg</option>
                                        <option>ml</option>
                                        <option>l</option>
                                        <option>m</option>
                                        <option>m²</option>
                                        <option>m³</option>
                                        <option value="uses">Anwendungen / Stück</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="nutrition-container">
                            <label class="control-label">Nährwertangaben:</label><br>


                            <div class="nutrition-container-meta">

                                <div class="c50">
                                    <label class="control-label">pro 100</label>
                                    <select id="nutrient_unit">
                                        <option>g</option>
                                        <option>ml</option>
                                    </select>
                                    <button type="button" id="generate_nw">Generate right</button>
                                </div>

                                <div class="c50">
                                    <label class="control-label">pro</label>
                                    <input type="text" class="myTextInput" id="nutrient_snd_amount" value="0">
                                    <span id="nutrient_unit_copy"></span>
                                    <br/>
                                    with: <input type="text" class="myTextInput" id="nutrient_snd_additional">
                                    mit: <input type="text" class="myTextInput" id="nutrient_snd_additional_de">
                                    Nährwerte gelten für zubereitet? <input type="checkbox" id="nutrient_snd_prepared"/>
                                </div>
                                <div class="clear"></div>
                            </div>


                            <div class="nrg-group">


                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Energie / Brennwert (in kJ)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_energy" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Fett (total) (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_fat_total" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Fett (gesättigt) (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_fat_saturated" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Kohlenhydrate (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_carb" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Zucker (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_sugar" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Proteine/Eiweiß (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_protein" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Salz (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_salt" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Ballaststoffe (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_fibers" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Calcium (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_calcium" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Lactose (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_lactose" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Natrium (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_natrium" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Broteinheiten</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_100_bread_unit" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="nrg-group">
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Energie / Brennwert (in KJ)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_energy" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Fett (total) (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_fat_total" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Fett (gesättigt) (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_fat_saturated" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm form-horizontal">
                                    <label class="control-label">Kohlenhydrate (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_carb" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Zucker (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_sugar" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Proteine/Eiweiß (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_protein" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Salz (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_salt" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Ballaststoffe (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_fibers" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Calcium (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_calcium" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Lactose (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_lactose" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Natrium (in g)</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_natrium" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group form-group-sm">
                                    <label class="control-label">Broteinheiten</label>
                                    <div class="col-sm-5">
                                        <input type="text" id="nutrient_snd_bread_unit" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </fieldset>
                </form>
            </div>


            <div id="tab2" role="tabpanel" class="tab-pane">

                <div class="form-group">
                    <label class="control-label">Inhaltsstoffe</label>

                    <p>
                        <span id="ingredients_collector"></span>
                <span id="ingredients_selwrap">
                    <input type="text" id="ingredients_selector" data-type="standard"/>
                    <div id="ingredients_suggestor"></div>
                </span>
                    </p>
                </div>

                <!-- -->
                <div class="form-group">
                    <div class="div-allergene">
                        <label class="control-label">Allergene für <span id="current_ingredient" data-id="">...</span>:</label>
                        <div id="allergy-select">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_a" data-cur_ingr="a">A - glutenhaltiges Getreide
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_b" data-cur_ingr="b">B - Krebstiere
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_c" data-cur_ingr="c">C - Ei
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_d" data-cur_ingr="d">D - Fisch
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_e" data-cur_ingr="e">E - Erdnuss
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_f" data-cur_ingr="f">F - Soja
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_g" data-cur_ingr="g">G - Milch oder Laktose
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_h" data-cur_ingr="h">H - Schalenfrüchte
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_l" data-cur_ingr="l">L - Sellerie
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_m" data-cur_ingr="m">M - Senf
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_n" data-cur_ingr="n">N - Sesam
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_o" data-cur_ingr="o">O - Sulfite
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_p" data-cur_ingr="p">P - Lupinen
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_r" data-cur_ingr="r">R - Weichtiere
                                </label>
                            </div>
                            <div>
                                <label>-</label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_fleisch" data-cur_ingr="fleisch">Fleisch/Gelatine/Tierprodukt
                                    (wg. vegetarisch)
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="cur_ingr_honig" data-cur_ingr="honig">Honig (für Vegan
                                    Berechnung)
                                </label>
                            </div>
                            <div>
                      <span class="admin-area">
                        <input type="hidden" id="ingredient_upnew_id" name="ingredient_upnew_id" value="0"/>
                        <input type="text" id="ingredient_upnew" value=""/>
                        <button id="ingredient_updater">Inhaltsstoff aktualisieren</button>
                      </span>
                                <button id="ingredient_deleter">Inhaltsstoff löschen</button>
                            </div>

                        </div>
                    </div>

                    <div class="div-allergene div-allergene-right">
                        <label class="control-label">Allergene (insgesamt):</label>
                        <div id="allergy-select">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_a" data-art_ingr="a">A - glutenhaltiges Getreide
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_b" data-art_ingr="b">B - Krebstiere
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_c" data-art_ingr="c">C - Ei
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_d" data-art_ingr="d">D - Fisch
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_e" data-art_ingr="e">E - Erdnuss
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_f" data-art_ingr="f">F - Soja
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_g" data-art_ingr="g">G - Milch oder Laktose
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_h" data-art_ingr="h">H - Schalenfrüchte
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_l" data-art_ingr="l">L - Sellerie
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_m" data-art_ingr="m">M - Senf
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_n" data-art_ingr="n">N - Sesam
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_o" data-art_ingr="o">O - Sulfite
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_p" data-art_ingr="p">P - Lupinen
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="art_ingr_r" data-art_ingr="r">R - Weichtiere
                                </label>
                            </div>
                            <div>
                                <label>-</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" id="art_ingr_fleisch" data-art_ingr="fleisch">Fleisch/Gelatine/Tierprodukt
                                    (wg. vegetarisch)</label>
                                <label><input type="checkbox" id="check_no_meat"/> Vegetarisch</label>

                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" id="art_ingr_honig" data-art_ingr="honig">Honig</label>
                                <label><input type="checkbox" id="check_no_honey"/> Kein Honig (wg. vegan)</label>

                            </div>
                        </div>
                    </div>


                    <hr/>

                    <div>
                        <p>
                            <label class="enthaltwrp">Kann Spuren von enthalten:</label>
                            <span id="enthalt_spuren_collector"></span>
                            <span><input type="text" id="enthalt_spuren" data-type="enthalt"/></span>
                        </p>
                    </div>

                    <div>
                        <p>
                            <label class="enthaltwrp">Enthält eine geringe Menge:</label>
                            <span id="enthalt_gering_collector""></span>
                            <span><input type="text" id="enthalt_gering" data-type="gering"/></span>
                            <span></span>
                        </p>
                    </div>


                    <hr/>

                    <div id="category-container">
                        <label>Kategorie</label><br>
                        <div id="category_select_wrapper">
                            <div id="cs_segment"></div>
                            <div id="cs_family"></div>
                            <div id="cs_class"></div>
                            <div id="cs_brick"></div>
                        </div>
                    </div>
                    <hr>


                    <div id="tag-container">

                        <div id="admin-area" class="admin-area">
                            <div id="tag_group_wrapper">

                                <p><label>Admin - Tag-Gruppe anlegen:</label> MUID (en): <input type="text"
                                                                                                id="tag_group_new_muid"
                                                                                                value=""/> Name (de):
                                    <input type="text" id="tag_group_new_name"/>
                                    Numerical required: <input type="checkbox" id="tag_group_new_numerical_required"
                                                               value=""/>

                                    <button id="tag_group_new_create">Gruppe anlegen</button>

                                    <label>Admin - Tag-Gruppe löschen:</label> <input type="text"
                                                                                      id="tag_group_delete_selector"
                                                                                      value=""/><input type="hidden"
                                                                                                       id="tag_group_delete_selected_id"
                                                                                                       value="0"/>
                                    <button id="tag_group_delete">Gruppe löschen</button>
                                </p>
                            </div>

                            <div id="tag_wrapper">
                                <p><label>Admin - Tag anlegen</label> (Erklärung: ~ ... Platzhalter für numerischen Wert
                                    in MUID/Name, $ ... Platzhalter für numerischen Value Type in MUID/Name, wenn nicht
                                    numerisch: keine Placeholder, typ leerlassen)</p>
                                <p>
                                    Gruppe wählen: <input type="text" id="tag_group_selector" value=""/><input
                                        type="hidden" id="tag_group_selected_id" value="0"/>
                                    UID (EN): <input type="text" id="tag_uid_new" value=""/>
                                    Name (DE): <input type="text" id="tag_name_new" value=""/>
                                    Name (AT) *: <input type="text" id="tag_name_at_new" value=""/>
                                    Numerical Typ:
                                    <select id="tag_numerical_new_type">
                                        <option value="">-- nicht numerisch --</option>
                                        <option value="numeric">(Zahl ohne Einheit)</option>
                                        <option value="percent">% (percent)</option>
                                        <option value="kilogram">kg (kilogram)</option>
                                        <option value="gram">g (gram)</option>
                                        <option value="milligram">mg (miligram)</option>
                                        <option value="liter">l (liter)</option>
                                        <option value="milliliter">ml (milliliter)</option>
                                        <option value="seconds">s (seconds)</option>
                                        <option value="minutes">m (minutes)</option>
                                        <option value="hours">h (hours)</option>
                                        <option value="days">d (days)</option>
                                        <option value="permill">‰ (promille)</option>
                                        <option value="squaremeters">m² (square meters)</option>
                                        <option value="cubicmeters">m³ (cubic meters)</option>
                                    </select>
                                    <button id="tag_new_create">Tag anlegen</button>
                                </p>
                                <p>
                                    <label>Admin - Tag löschen:</label> <input type="text" id="tag_delete_selector"
                                                                               value=""/><input type="hidden"
                                                                                                id="tag_delete_selected_id"
                                                                                                value="0"/>
                                    <button id="tag_delete">Tag löschen</button>
                                </p>
                            </div>

                            <hr>

                        </div>
                    </div>


                    <label>Artikelbeschreibende Information</label>

                    <div class="cfg_row">
                        Aktive Kategorie:
                        <input type="hidden" id="active_category" value=""/>
                        <span id="active_category_display">-- Keine --</span>
                <span class="admin-area">
                &nbsp;&nbsp;&nbsp;<button id="active_category_tag_update">Speichere Tag-Vorschläge</button>
                &nbsp;&nbsp;&nbsp;<button data-ishidden="0" id="switch_show_recommended">Alle Tags
                        einblenden/ausblenden
                    </button>
                </span>
                    </div>

                    <div id="attributes-container">
                        <p><label>GS1 Tags</label></p>
                        <div id="tags_gs1" class="div-attributes"></div>
                        <div class="clear"></div>
                        <p><label>Eigene Tags</label></p>
                        <div id="guetesiegel" class="div-attributes"></div>
                        <div class="clear"></div>
                        <p><label>Numerische Tags</label> (0 ist ein Wert)</p>
                        <div id="tags_numerical" class="div-attributes"></div>
                        <div class="clear"></div>
                    </div>
                    <hr>

                </div>

            </div>

        </div>
        <div id="send-container">
            <label class="control-label">Anmerkung(en) (allg. Anmerkungen zum Einpflegen des Artikels, Schwierigkeiten,
                Hinweise, ...)</label>
            <textarea id="notice" rows="2" class="form-control"></textarea>
            <div class="clear"></div>
            <div id="save_id" data-save_id=""></div>
            <div id="last_state" data-last_state="0"></div>
            <button id="save_now" class="btn btn-default save_current_product" data-state="5">Speichern</button>
            <button id="finish_now" class="btn btn-default save_current_product" data-state="10">Abschließen</button>
            <br/><br/>
            <button id="state7_now" class="btn btn-default save_current_product" data-state="7">Speichern (für später)
            </button>
            <button id="state8_now" class="btn btn-default save_current_product" data-state="8">Speichern
                (Problemfall)
            </button>
            <button id="exported_now" class="btn btn-default save_current_product" data-state="15">Speichern (bereits
                exportiert)
            </button>

            <div id="custom_state_wrapper" class="admin-area"><span
                    class="bold">Admin: Benutzerdefinierter Status</span> <input type="text" id="custom_state"
                                                                                 value=""/></div>
            <div id="message_container"></div>
            <div id="message_container_save"></div>
        </div>
    </div>


    <div class="hidden" id="ingredients"><?php echo json_encode($ingredients); ?></div>

    <div class="hidden" id="corporations"><?php echo json_encode($corporations); ?></div>

    <div class="hidden" id="brands"><?php echo json_encode($brands); ?></div>

    <div class="hidden" id="taggroups"><?php echo json_encode($taggroups); ?></div>

    <div class="hidden" id="categories"><?php echo json_encode($categories); ?></div>

    <div class="hidden" id="tags"><?php echo json_encode($tags); ?></div>

    <div class="hidden" id="media_path"><?php echo $media_path; ?></div>

    <div class="clear"></div>
</div>
</body>
</html>