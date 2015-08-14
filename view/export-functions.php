<?php

/*********** TAGS ***************/

function getNutrientDefsPlain() {
     return array(
        array(
            "field" => "nutrient_%s_energy",
            "tagGroupingPrefixEN" => "Energy (Nutrient)",
            "tagGroupingPrefixDE" => "Energie (Lebensmittel)",
            "unit" => "kJ"
        ),
        array(
            "field" => "nutrient_%s_fat_total",
            "tagGroupingPrefixEN" => "Fat total (Nutrient)",
            "tagGroupingPrefixDE" => "Fett gesamt (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_fat_saturated",
            "tagGroupingPrefixEN" => "Fat saturated (Nutrient)",
            "tagGroupingPrefixDE" => "Gesättigte Fettsäuren (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_protein",
            "tagGroupingPrefixEN" => "Protein (Nutrient)",
            "tagGroupingPrefixDE" => "Eiweiß (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_fibers",
            "tagGroupingPrefixEN" => "Fibers (Nutrient)",
            "tagGroupingPrefixDE" => "Ballaststoffe (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_calcium",
            "tagGroupingPrefixEN" => "Calcium (Nutrient)",
            "tagGroupingPrefixDE" => "Calcium (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_carb",
            "tagGroupingPrefixEN" => "Carbohydrate (Nutrient)",
            "tagGroupingPrefixDE" => "Kohlenhydrate (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_sugar",
            "tagGroupingPrefixEN" => "Sugar (Nutrient)",
            "tagGroupingPrefixDE" => "Zucker (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_salt",
            "tagGroupingPrefixEN" => "Salt (Nutrient)",
            "tagGroupingPrefixDE" => "Salz (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_lactose",
            "tagGroupingPrefixEN" => "Lactose (Nutrient)",
            "tagGroupingPrefixDE" => "Laktose (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_natrium",
            "tagGroupingPrefixEN" => "Natrium (Nutrient)",
            "tagGroupingPrefixDE" => "Natrium (Lebensmittel)",
            "unit" => "g"
        ),
        array(
            "field" => "nutrient_%s_bread_unit",
            "tagGroupingPrefixEN" => "Bread Unit (Nutrient)",
            "tagGroupingPrefixDE" => "Broteinheiten (Lebensmittel)",
            "unit" => "g"
        )
    );
}


function getNutrientDefs() {
    $nutrient_defs = array();
    
    $amounts = ["100","snd"];
    
    foreach($amounts as $a) {
        foreach(getNutrientDefsPlain() as $f) {
            $tp = $f;
            $tp["field"] = str_replace("%s",$a,$f["field"]);
            $tp["amount"] = $a;
            array_push($nutrient_defs,$tp);
        }
    }
    return $nutrient_defs;
}


// get all prepared tag columns for the nutrients
function getNutrientTagColumns($row) {
    
    $tagdefs = array();
    
    foreach(getNutrientDefs() as $nut) {
        $tc = getNutrientTagColumn($row,$nut);
        
        if($tc!==false) {
            array_push($tagdefs,getNutrientTagColumn($row,$nut));
        }
    }
    
    return $tagdefs;
}


function getNutrientTagColumn($row,$nut) {
    
    // no value was inserted (also not a zero!), therefore we do not export any tag
    if($row[$nut["field"]]===null) {
        return false;
    }
    
    $tag_column = array();
    
    $tgam = "100".$row["nutrient_unit"];
    $dgam = $tgam;
    if($nut["amount"]==="snd") {
        $tgam = "serving";
        $dgam = "Einheit";
    }
    
    
    // for the tag UID we are in englisch language, use . as seperator for any numbers
    $fullvalue = str_replace(",",".",(string) $row[$nut["field"]])." ".$nut["unit"];
    
    
    if($nut["amount"]==="snd") {
        $fullvalue .= " (per ".$row["nutrient_snd_amount"].$row["nutrient_unit"];
        
        if($row["nutrient_snd_additional"]!="") {
            $fullvalue .= " with ".$row["nutrient_snd_additional"];
        }
        $fullvalue .= ")";
    }
    
    $fullgroup = $nut["tagGroupingPrefixEN"]." per ".$tgam;
    
    $tag_column["tagGroupingUid"] = $fullgroup;
    
    $tag_column["tagGroupingName de_DE"] = $nut["tagGroupingPrefixDE"]." pro ".$dgam;
    
    $tag_column["tagGroupingTagNumericalRequired"] = "Yes";
    
    $tag_column["tagUid"] = $fullgroup." : ".$fullvalue;
    
    
    $deTagName = str_replace(".",",",(string) $row[$nut["field"]])." ".$nut["unit"];
    
    // add kcal in case of kj
    if($nut["unit"]==="kJ") {
        $deTagName .= " (".getKcalFromKj($row[$nut["field"]])." kCal)";
    }
    
    // add the with
    if($nut["amount"]==="snd") {
        $deTagName .= " (pro ".$row["nutrient_snd_amount"].$row["nutrient_unit"];
        
        if($row["nutrient_snd_additional"]!="") {
            $deTagName .= " mit ".$row["nutrient_snd_additional_de"];
        }
        $deTagName .= ")";
    }
    
    $tag_column["tagName de_DE"] = $deTagName;
    
    $tag_column["tagNumericalValueRangeStart"] = $row[$nut["field"]];
    $tag_column["tagNumericalValueRangeEnd"] = $row[$nut["field"]];
    $tag_column["tagType"] = "ArticleDescribing";
    
    return $tag_column;
}


function getKcalFromKj($kcal) {
    return round($kcal * 0.23900573614);
}



/********** ALLERGENE ************/
function getAllergeneTagColumns($row) {
    $tagdefs = array();
    
    $allergenes = array("a","b","c","d","e","f","g","h","l","m","n","o","p","r");
    
    foreach($allergenes as $allergen) {
        if($row["allergen_".$allergen]) {
            array_push($tagdefs,getAllergenTagColumn($allergen));
        }
    }
    return $tagdefs;
}

function getAllergenTagColumn($allergen) {
    $tag_column["tagGroupingUid"] = "Allergen";
    $tag_column["tagGroupingName de_DE"] = "Allergene";
    $tag_column["tagGroupingTagNumericalRequired"] = "No";
    
    switch($allergen):
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
function getSealetcTagColumns($row) {
    $tagColumns = array();
    
    global $db;
    
    $stmt = $db->prepare('SELECT sealetc.name FROM sealetc, fdata_sealetc as con WHERE con.fdata_id=:id AND con.sealetc_id = sealetc.id');
    $stmt->bindValue(":id",$row['id']);
    $stmt->execute();
    $sealetc = $stmt->fetchAll();
    
    foreach($sealetc as $s) {
        array_push($tagColumns,getSealetcTagColumn($s['name']));
    }
    return $tagColumns;
}


function getSealetcTagColumn($label) {
    $tag_column = array();
    
    $tag_column["tagGroupingUid"] = "Attribute (Food)";
    $tag_column["tagGroupingName de_DE"] = "Attribut (Nahrungsmittel)";
    $tag_column["tagGroupingTagNumericalRequired"] = "No";
    
    $tag_column["tagUid"] = "Attribute (Food) : ".$label;
    $tag_column["tagName de_DE"] = $label;
    $tag_column["tagType"] = "ArticleDescribing";
    
    return $tag_column;
}



/************ CATEGORIES: Category Path **********/
function getCategoryExportPath($id) {
    global $db;
    
    $stmt = $db->prepare('SELECT c.* FROM category AS c, fdata_category as con, fdata WHERE fdata.id = :id AND con.fdata_id = fdata.id AND c.gid = con.category_id');
    $stmt->bindValue(":id",$id);
    $stmt->execute();
    $cats = $stmt->fetchAll();
    
    $result = "";
    
    foreach($cats as $cat) {
        $catgath = array();
        for($i=1;$i<=7; $i++) {
            if($cat["lvl_".$i]!="") {
                array_push($catgath,$cat['lvl_'.$i]);
            }
        }
        $result = implode(" >> ",$catgath);
        break; // ONLY AS LONG AS ONE SINGLE CAT IS USED
    }
    return $result;
}


/********** INGREDIENTS (Only used in description) ***********/
function getIngredientExport($id) {
    // get all Ingredients and add them to description
    global $db;
    
    $stmt = $db->prepare('SELECT ingredient.name FROM ingredient, fdata_ingredient as con WHERE con.fdata_id=:id AND con.ingredient_id = ingredient.id');
    $stmt->bindValue(":id",$id);
    $stmt->execute();
    $ingredients = $stmt->fetchAll();
    
    $ingrs = array();
    
    foreach($ingredients as $i) {
        array_push($ingrs,$i['name']);
    }
    
    if(!empty($ingrs)) {
        return "

        Inhaltsstoffe: ".implode(", ",$ingrs);
    }
    return "";
}




/************ GENERAL ***********/

/**
 * Returns full tags as multi-dimensional array for a given row.
 */
function getAllTagsForRow($row) {

    $nutrient_columns = getNutrientTagColumns($row);
    $allergene_columns = getAllergeneTagColumns($row);
    $sealetc_columns = getSealetcTagColumns($row);
    
    return array_merge($nutrient_columns,$allergene_columns,$sealetc_columns);

}

/**
 * Returns all Tag IDs as array (numerical index) for a given row.
 */
function getTagIDsForRow($row) {
    
    $alltags = getAllTagsForRow($row);
    
    $tagids = array();
    
    foreach($alltags as $t) {
        array_push($tagids,$t["tagUid"]);
    }
    
    return $tagids;
}

/**
 * Get a prepared tag path with connected tags.
 */
function getPreparedTagPathForRow($row) {
    return " >> ".implode(" >> ",  getTagIDsForRow($row));
}


function tagGroupingFilterRemoveDuplicate($alltags) {
    $exists = array();
    
    foreach($alltags as $key => $t) {
        
        if(in_array($t['tagGroupingUid'],$exists)) {
            $alltags[$key]["tagGroupingName de_DE"]="";
            $alltags[$key]["tagGroupingTagNumericalRequired"]="";
        } else {
            array_push($exists,$t['tagGroupingUid']);
        }
    }
    return $alltags;
}