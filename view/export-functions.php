<?php

function quoteForCsv($value)
{
    return preg_replace('/"/', '""', trim("" . $value));
}


/*********** TAGS ***************/

function getNutrientDefsPlain()
{
    return array(
        array(
            "field" => "nutrient_%s_energy",
            "tagGroupingPrefixEN" => "Energy (Nutrient)",
            "tagGroupingPrefixDE" => "Energie",
            "unit" => "kJ"
        ),
        array(
            "field" => "nutrient_%s_fat_total",
            "tagGroupingPrefixEN" => "Fat total (Nutrient)",
            "tagGroupingPrefixDE" => "Fett gesamt",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_fat_saturated",
            "tagGroupingPrefixEN" => "Fat saturated (Nutrient)",
            "tagGroupingPrefixDE" => "Gesättigte Fettsäuren",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_protein",
            "tagGroupingPrefixEN" => "Protein (Nutrient)",
            "tagGroupingPrefixDE" => "Eiweiß",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_fibers",
            "tagGroupingPrefixEN" => "Fibers (Nutrient)",
            "tagGroupingPrefixDE" => "Ballaststoffe",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_calcium",
            "tagGroupingPrefixEN" => "Calcium (Nutrient)",
            "tagGroupingPrefixDE" => "Calcium",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_carb",
            "tagGroupingPrefixEN" => "Carbohydrate (Nutrient)",
            "tagGroupingPrefixDE" => "Kohlenhydrate",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_sugar",
            "tagGroupingPrefixEN" => "Sugar (Nutrient)",
            "tagGroupingPrefixDE" => "Zucker",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_salt",
            "tagGroupingPrefixEN" => "Salt (Nutrient)",
            "tagGroupingPrefixDE" => "Salz",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_lactose",
            "tagGroupingPrefixEN" => "Lactose (Nutrient)",
            "tagGroupingPrefixDE" => "Laktose",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_natrium",
            "tagGroupingPrefixEN" => "Natrium (Nutrient)",
            "tagGroupingPrefixDE" => "Natrium",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_bread_unit",
            "tagGroupingPrefixEN" => "Bread Unit (Nutrient)",
            "tagGroupingPrefixDE" => "Broteinheiten",
            "unit" => "g"
        )
    );
}


function getNutrientDefs()
{
    $nutrient_defs = array();

    $amounts = ["100", "snd"];

    foreach ($amounts as $a) {
        foreach (getNutrientDefsPlain() as $f) {
            $tp = $f;
            $tp["field"] = str_replace("%s", $a, $f["field"]);
            $tp["amount"] = $a;
            array_push($nutrient_defs, $tp);
        }
    }
    return $nutrient_defs;
}


// get all prepared tag columns for the nutrients
function getNutrientTagColumns($row)
{

    $tagdefs = array();

    foreach (getNutrientDefs() as $nut) {
        $tc = getNutrientTagColumn($row, $nut);

        if ($tc !== false) {
            array_push($tagdefs, getNutrientTagColumn($row, $nut));
        }
    }

    return $tagdefs;
}


function getNutrientTagColumn($row, $nut)
{

    // no value was inserted (also not a zero!), therefore we do not export any tag
    if ($row[$nut["field"]] === null) {
        return false;
    }

    $tag_column = array();

    $tgam = "100" . $row["nutrient_unit"];
    $dgam = $tgam;
    if ($nut["amount"] === "snd") {
        $tgam = "serving";
        $dgam = "Portion";
    }


    // for the tag UID we are in englisch language, use . as seperator for any numbers
    $fullvalue = str_replace(",", ".", (string)$row[$nut["field"]]) . " " . $nut["unit"];


    if ($nut["amount"] === "snd") {
        $fullvalue .= " (per " . $row["nutrient_snd_amount"] . $row["nutrient_unit"];

        if ($row["nutrient_snd_additional"] != "") {
            $fullvalue .= " with " . $row["nutrient_snd_additional"];
        }
        $fullvalue .= ")";
    }

    $fullgroup = $nut["tagGroupingPrefixEN"] . " per " . $tgam;

    $tag_column["tagGroupingUid"] = $fullgroup;

    $tag_column["tagGroupingName de_DE"] = $nut["tagGroupingPrefixDE"] . " pro " . $dgam;

    $tag_column["tagGroupingTagNumericalRequired"] = "Yes";

    $tag_column["tagUid"] = $fullgroup . " : " . $fullvalue;


    $deTagName = str_replace(".", ",", (string)$row[$nut["field"]]) . " " . $nut["unit"];

    // add kcal in case of kj
    if ($nut["unit"] === "kJ") {
        $deTagName .= " (" . getKcalFromKj($row[$nut["field"]]) . " kCal)";
    }

    // add the with
    if ($nut["amount"] === "snd") {
        $deTagName .= " (pro " . $row["nutrient_snd_amount"] . $row["nutrient_unit"];

        if ($row["nutrient_snd_additional"] != "") {
            $deTagName .= " mit " . $row["nutrient_snd_additional_de"];
        }
        $deTagName .= ")";
    }

    $tag_column["tagName de_DE"] = $deTagName;

    $tag_column["tagNumericalValueRangeStart"] = $row[$nut["field"]];
    $tag_column["tagNumericalValueRangeEnd"] = $row[$nut["field"]];
    $tag_column["tagType"] = "ArticleDescribing";

    return $tag_column;
}


function getKcalFromKj($kcal)
{
    return round($kcal * 0.23900573614);
}


/********** ALLERGENE ************/
function getAllergeneTagColumns($row)
{
    $tagdefs = array();

    $allergenes = array("a", "b", "c", "d", "e", "f", "g", "h", "l", "m", "n", "o", "p", "r");

    foreach ($allergenes as $allergen) {
        if ($row["allergen_" . $allergen]) {
            array_push($tagdefs, getAllergenTagColumn($allergen));
        }
    }
    return $tagdefs;
}

function getAllergenTagColumn($allergen)
{
    $tag_column["tagGroupingUid"] = "Allergen";
    $tag_column["tagGroupingName de_DE"] = "Allergene";
    $tag_column["tagGroupingTagNumericalRequired"] = "No";

    switch ($allergen):
        case "a": {
            $tag_column["tagUid"] = "Allergen : Gluten";
            $tag_column["tagName de_AT"] = "glutenhaltiges Getreide (A)";
            $tag_column["tagName de_DE"] = "Getreideprodukte (Glutenhaltig) (1,A)";

            break;
        }
        case "b": {
            $tag_column["tagUid"] = "Allergen : Shellfishes";
            $tag_column["tagName de_AT"] = "Krebstiere (B)";
            $tag_column["tagName de_DE"] = "Krebstiere (3,C)";

            break;
        }
        case "c": {
            $tag_column["tagUid"] = "Allergen : Egg";
            $tag_column["tagName de_AT"] = "Ei (C)";
            $tag_column["tagName de_DE"] = "Eier (9,I)";

            break;
        }
        case "d": {
            $tag_column["tagUid"] = "Allergen : Fish";
            $tag_column["tagName de_AT"] = "Fisch (D)";
            $tag_column["tagName de_DE"] = "Fisch (2,B)";

            break;
        }
        case "e": {
            $tag_column["tagUid"] = "Allergen : Peanut";
            $tag_column["tagName de_AT"] = "Erdnuss (E)";
            $tag_column["tagName de_DE"] = "Erdnüsse (14,N)";

            break;
        }
        case "f": {
            $tag_column["tagUid"] = "Allergen : Soy";
            $tag_column["tagName de_AT"] = "Soja (F)";
            $tag_column["tagName de_DE"] = "Soja (12,L)";

            break;
        }
        case "g": {
            $tag_column["tagUid"] = "Allergen : Lactose";
            $tag_column["tagName de_AT"] = "Milch oder Laktose (G)";
            $tag_column["tagName de_DE"] = "Milch und Laktose (6,F)";

            break;
        }
        case "h": {
            $tag_column["tagUid"] = "Allergen : Nuts";
            $tag_column["tagName de_AT"] = "Schalenfrüchte (H)";
            $tag_column["tagName de_DE"] = "Nüsse (8,H)";

            break;
        }
        case "l": {
            $tag_column["tagUid"] = "Allergen : Celery";
            $tag_column["tagName de_AT"] = "Sellerie (L)";
            $tag_column["tagName de_DE"] = "Sellerie (5,E)";

            break;
        }
        case "m": {
            $tag_column["tagUid"] = "Allergen : Mustard";
            $tag_column["tagName de_AT"] = "Senf (M)";
            $tag_column["tagName de_DE"] = "Senf (11,K)";

            break;
        }
        case "n": {
            $tag_column["tagUid"] = "Allergen : Sesame";
            $tag_column["tagName de_AT"] = "Sesam (N)";
            $tag_column["tagName de_DE"] = "Sesamsamen (7,G)";

            break;
        }
        case "o": {
            $tag_column["tagUid"] = "Allergen : Sulfite";
            $tag_column["tagName de_AT"] = "Sulfite (O)";
            $tag_column["tagName de_DE"] = "Schwefeldioxide und Sulfite (4,D)";

            break;
        }
        case "p": {
            $tag_column["tagUid"] = "Allergen : Lupine";
            $tag_column["tagName de_AT"] = "Lupinen (P)";
            $tag_column["tagName de_DE"] = "Lupinen (10,J)";

            break;
        }
        case "r": {
            $tag_column["tagUid"] = "Allergen : Mollusca";
            $tag_column["tagName de_AT"] = "Weichtiere (R)";
            $tag_column["tagName de_DE"] = "Weichtiere (13,M)";

            break;
        }


    endswitch;

    $tag_column["tagType"] = "ArticleDescribing";

    return $tag_column;
}


/*********** SEALETC **********/
function getSealetcTagColumns($row)
{
    $tagColumns = array();

    global $db;

    $stmt = $db->prepare('SELECT tg.gs1_attribute_type_code AS group_gs1, tg.muid AS group_muid, tg.name AS group_name, tg.numerical_required, tag.gs1_attribute_value_code, tag.muid, tag.name_de, tag.name_at, tag.type, con.numerical_value FROM taggroup AS tg, tag, fdata_tag AS con WHERE con.fdata_id=:id AND con.tag_id = tag.id AND tag.taggroup = tg.id');
    $stmt->bindValue(":id", $row['id']);
    $stmt->execute();
    $connected_tags = $stmt->fetchAll();

    foreach ($connected_tags as $ct) {

        if ($ct["muid"] == "UNCLASSIFIED" || $ct["muid"] == "UNIDENTIFIED") {
            continue;
        }
        
        $tag_column = array();

        $numerical = floatval($ct["numerical_value"]);
        $isNumerical = $numerical > 0.0;

        $groupMuid = $ct["group_muid"];

        if (intval($ct["group_gs1"]) > 0) {
            $tag_column["tagGroupingGpcId"] = $ct["group_gs1"];
            $groupMuid = "GPC_" . $groupMuid;
        }

        $groupName = strval($ct["group_name"]);
        if ($groupName == "") $groupName = $ct["group_muid"];
        $tag_column["tagGroupingName de_DE"] = $groupName;
        $tag_column["tagGroupingUid"] = $groupMuid;

        if ($isNumerical) {
            $tag_column["tagUid"] = $groupMuid . " : " . numericTag($ct["muid"], $ct["numerical_value"], $ct["type"], "en");
            $tag_column["tagName de_DE"] = numericTag(strval($ct["name_de"]), $ct["numerical_value"], $ct["type"], "de", ",");
            $tag_column["tagNumericalValueRangeStart"] = $numerical;
            $tag_column["tagNumericalValueRangeEnd"] = $numerical;
        } else {
            $tagName = strval($ct["name_de"]);
            if ($tagName == "") $tagName = $ct["muid"];
            $tag_column["tagUid"] = $groupMuid . " : " . $ct["muid"];
            $tag_column["tagName de_DE"] = $tagName;
        }

        $tag_column["tagType"] = "ArticleDescribing";

        if (intval($ct["gs1_attribute_value_code"]) > 0) {
            $tag_column["gpcId"] = $ct["group_gs1"] . "_" . $ct["gs1_attribute_value_code"];
        }

        $tag_column["tagGroupingTagNumericalRequired"] = ($isNumerical && $ct["numerical_required"]) ? "Yes" : "No";

        array_push($tagColumns, $tag_column);

    }

    return $tagColumns;
}

function numericTag($label, $value, $type, $lang = "en", $seperator = ".")
{
    $unsmap = unserialize(NUMERICAL_VALUE_TYPES_MAP);
    return str_replace("~", str_replace(".", $seperator, strval($value)), str_replace("$", $unsmap[$type][$lang], $label));
}


function getFakeAllergenTagColumns($row)
{
    $cols = array();

    if ($row["allergen_honig"]) {
        array_push($cols, getFakeAllergenColumn("Contains Honey", "Enthält Honig"));
    }
    if ($row["allergen_fleisch"]) {
        array_push($cols, getFakeAllergenColumn("Contains Meat", "Enthält Fleisch"));
    }
    return $cols;
}

function getFakeAllergenColumn($label_en, $label_de)
{
    $tag_column = array();
    $tag_column["tagGroupingUid"] = "Attribute (Food)";
    $tag_column["tagGroupingName de_DE"] = "Attribut (Nahrungsmittel)";
    $tag_column["tagGroupingTagNumericalRequired"] = "No";

    $tag_column["tagUid"] = "Attribute (Food) : " . $label_en;
    $tag_column["tagName de_DE"] = $label_de;
    $tag_column["tagType"] = "ArticleDescribing";

    return $tag_column;
}

/******************* Special Tags ****************/
function getSpecialTags($row)
{
    $cols = array();

    $ger = get_countries_german();
    $en = get_countries_english();

    if ($row["origin"] != "") {
        $tag_column = array();
        $tag_column["tagGroupingUid"] = "Country of origin";
        $tag_column["tagGroupingName de_DE"] = "Herkunftsland";
        $tag_column["tagGroupingTagNumericalRequired"] = "No";

        $tag_column["tagUid"] = "Country of origin : " . $en[$row["origin"]];
        $tag_column["tagName de_DE"] = $ger[$row["origin"]];
        $tag_column["tagType"] = "ArticleDescribing";

        array_push($cols, $tag_column);
    }


    if ($row["store"] != "") {

        $tag_column = array();

        $label_de = "Normal";
        $label_en = "Normal";

        switch ($row["store"]):
            case "cooled": {
                $label_de = "Kühl";
                $label_en = "Cooled";
                break;
            }
            case "frozen": {
                $label_de = "Tiefgekühlt";
                $label_en = "Deep frozen";
                break;
            }
            case "not_cooled": {
                $label_de = "Zimmertemperatur";
                $label_en = "Room temperature";
                break;
            }
        endswitch;

        $tag_column["tagGroupingUid"] = "Storage";
        $tag_column["tagGroupingName de_DE"] = "Lagerung";
        $tag_column["tagGroupingTagNumericalRequired"] = "No";

        $tag_column["tagUid"] = "Storage : " . $label_de;
        $tag_column["tagName de_DE"] = $label_en;
        $tag_column["tagType"] = "ArticleDescribing";

        array_push($cols, $tag_column);
    }

    if ($row["container"] != "") {

        $tag_column = array();

        $tag_column["tagGroupingUid"] = "Packaging";
        $tag_column["tagGroupingName de_DE"] = "Verpackung";
        $tag_column["tagGroupingTagNumericalRequired"] = "No";

        $tag_column["tagUid"] = "Packaging : " . $row["container"];
        $tag_column["tagName de_DE"] = $row["container"];
        $tag_column["tagType"] = "ArticleDescribing";

        array_push($cols, $tag_column);
    }

    return $cols;
}


/************ MUID Builder **********/
function _replToAscii($t) {
    return preg_replace("/_*$/", "",
        mb_ereg_replace("[^a-zA-Z0-9]", "_",
            mb_ereg_replace("ä", "ae",
                mb_ereg_replace("Ä", "ae",
                    mb_ereg_replace("ö", "oe",
                        mb_ereg_replace("Ö", "Oe",
                            mb_ereg_replace("ü", "ue",
                                mb_ereg_replace("Ü", "Ue",
                                    mb_ereg_replace("ß", "ss",
                                        $t)))))))));
}

function buildMuid($row, $buildForArticle = false)
{
    $productName = $row["productName de_AT"];
    $barCode = explode("~", $row["articleBarCode"])[1];
    $brand = $row["productBrand de_AT"];

    $muid = preg_replace("/_*$/", "",
        $barCode . "." . _replToAscii(substr($brand, 0, 11)) . "." . _replToAscii(substr($productName, 0, 15)));
    if ($buildForArticle) {
        $weight = $row["articleWeight"];
        $volume = $row["articleVolume"];
        $area = $row["articleArea"];
        $length = $row["articleLength"];
        $uses = $row["articleUses"];
        
        $size = $weight . $volume . $area . $length . $uses;
        
        if ($size != "") $muid .= "." . _replToAscii($size);
    }
    return $muid;
}


/************ CATEGORIES: Category Path **********/
function getCategory($id) {
    global $db;

    $stmt = $db->prepare('SELECT category.* FROM category,fdata WHERE fdata.id = :id AND category.gid = fdata.category');
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    return $stmt->fetch();
}

function replaceNonAsciiInCategories($category) {
    $category = preg_replace('/–/u', '-', $category);
    $category = preg_replace('/:/', '|', $category);
    return $category;
}
function getCategoryExportPath($id)
{
    $cat = getCategory($id);

    $catgath = array();
    array_push($catgath, replaceNonAsciiInCategories("GPC_" . $cat["segment_description_en"]));
    array_push($catgath, replaceNonAsciiInCategories("GPC_" . $cat["family_description_en"]));
    array_push($catgath, replaceNonAsciiInCategories("GPC_" . $cat["class_description_en"]));
    array_push($catgath, replaceNonAsciiInCategories("GPC_" . $cat["brick_description_en"]));

    return implode(" >> ", $catgath);
}


/********** INGREDIENTS & Co. ***********/

function getDescriptionAppendix($id)
{
    return getIngredientExport($id) . getSpurenExport($id) . getGeringeMengeExport($id);
}

function _getIngredients($id) {
    // get all Ingredients and add them to description
    global $db;

    $stmt = $db->prepare('SELECT ingredient.name FROM ingredient, fdata_ingredient AS con WHERE con.fdata_id=:id AND con.ingredient_id = ingredient.id');
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    return $stmt->fetchAll();
}

function countIngredients($id) {
    return count(_getIngredients($id));
}

function getIngredientExport($id)
{
    $ingredients = _getIngredients($id);

    $ingrs = array();

    foreach ($ingredients as $i) {
        array_push($ingrs, $i['name']);
    }

    if (!empty($ingrs)) {
        return
            "

Inhaltsstoffe: " . implode(", ", $ingrs);
    }
    return "";
}

function _combineForSentence($a, $andWord) {
    $c = count($a);

    if ($c == 1) {
        return $a[0];
    } else if ($c > 1) {
        return implode(", ", array_slice($a, 0, $c - 1)) . " $andWord " . end($a);
    }
    return "";
}

function getSpurenExport($id)
{
    // get all Ingredients and add them to description
    global $db;

    $stmt = $db->prepare('SELECT ingredient.name FROM ingredient, fdata_ingredient_enthalt AS con WHERE con.fdata_id=:id AND con.ingredient_id = ingredient.id');
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $ingredients = $stmt->fetchAll();

    $ingrs = array();

    foreach ($ingredients as $i) {
        array_push($ingrs, $i['name']);
    }

    if (empty($ingrs)) return "";

    return
        "

Kann Spuren von " . _combineForSentence($ingrs, "und") . " enthalten.";
}

function getGeringeMengeExport($id)
{
    // get all Ingredients and add them to description
    global $db;

    $stmt = $db->prepare('SELECT ingredient.name FROM ingredient, fdata_ingredient_gering AS con WHERE con.fdata_id=:id AND con.ingredient_id = ingredient.id');
    $stmt->bindValue(":id", $id);
    $stmt->execute();
    $ingredients = $stmt->fetchAll();

    $ingrs = array();

    foreach ($ingredients as $i) {
        array_push($ingrs, $i['name']);
    }

    if (empty($ingrs)) return "";
    
    return
        "

Enthält eine geringe Menge " . _combineForSentence($ingrs, "und") . ".";
}


/************ GENERAL ***********/

/**
 * Returns full tags as multi-dimensional array for a given row.
 */
function getAllTagsForRow($row)
{

    $nutrient_columns = getNutrientTagColumns($row); // nährwerte
    $allergene_columns = getAllergeneTagColumns($row); // allergene

    $tag_columns = getSealetcTagColumns($row); // gs1 / normale / numeric tags

    $special_allergenes = getFakeAllergenTagColumns($row); // fake allergene wie honig, fleisch

    $special_tags = getSpecialTags($row);

    return array_merge($nutrient_columns, $allergene_columns, $tag_columns, $special_allergenes, $special_tags);

}

/**
 * Returns all Tag IDs as array (numerical index) for a given row.
 */
function getTagIDsForRow($row)
{

    $alltags = getAllTagsForRow($row);

    $tagids = array();

    foreach ($alltags as $t) {
        array_push($tagids, $t["tagUid"]);
    }

    return $tagids;
}

/**
 * Get a prepared tag path with connected tags.
 */
function getPreparedTagPathForRow($row)
{
    return " >> " . implode(" >> ", getTagIDsForRow($row));
}


function tagGroupingFilterRemoveDuplicate($alltags)
{
    $exists = array();

    foreach ($alltags as $key => $t) {

        if (in_array($t['tagGroupingUid'], $exists)) {
            $alltags[$key]["tagGroupingName de_DE"] = "";
            $alltags[$key]["tagGroupingTagNumericalRequired"] = "";
        } else {
            array_push($exists, $t['tagGroupingUid']);
        }
    }
    return $alltags;
}
