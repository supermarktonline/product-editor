<?php

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

function getNutrientExportPath($row) {
    
    $result = "";
    
    $nu = $row["nutrient_unit"];
    
    $add = ($row["nutrient_snd_additional"] != "") ? " ".$row["nutrient_snd_additional"]:"";
    
    $snd = $row["nutrient_snd_amount"];
    
    $result .= gsnPath($row["nutrient_100_energy"],"Energy (Nutrient) per 100".$nu." : % kJ");
    $result .= gsnPath($row["nutrient_100_fat_total"],"Fat total (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_fat_saturated"],"Fat saturated (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_protein"],"Protein (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_fibers"],"Fibers (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_calcium"],"Calcium (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_carb"],"Carbohydrate (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_sugar"],"Sugar (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_salt"],"Salt (Nutrient) per 100".$nu.": %");
    $result .= gsnPath($row["nutrient_100_lactose"],"Lactose (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_natrium"],"Natrium (Nutrient) per 100".$nu." : %");
    $result .= gsnPath($row["nutrient_100_bread_unit"],"Bread Unit (Nutrient) per 100".$nu." : %");
    
    $result .= gsnPath($row["nutrient_snd_energy"],"Energy (Nutrient) per serving: %  kJ (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_fat_total"],"Fat total (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_fat_saturated"],"Fat saturated (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_protein"],"Protein (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_fibers"],"Fibers (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_calcium"],"Calcium (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_carb"],"Carbohydrate (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_sugar"],"Sugar (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_salt"],"Salt (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_lactose"],"Lactose (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_natrium"],"Natrium (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    $result .= gsnPath($row["nutrient_snd_bread_unit"],"Bread Unit (Nutrient) per serving : % (per ".$snd.$nu.$add.")");
    
    return $result;
}

function gsnPath($field,$subject) {
    if(!intval($field>0)) {
        return "";
    }
    return str_replace("%",$field," >> ".$subject);
}

function getAllergenExportPath($row) {
    $result = "";
    
    $allergenes = array("a","b","c","d","e","f","g","h","l","m","n","o","p","r");
    
    foreach($allergenes as $allergen) {
        if($row["allergen_".$allergen]=="true") {
            $result .= " >> Allergen : ".strtoupper($allergen);
        }
    }
    
    return $result." ";
}

function getSealEtcExportPath($id) {
    $result = "";
    
    global $db;
    
    $stmt = $db->prepare('SELECT sealetc.name FROM sealetc, fdata_sealetc as con WHERE con.fdata_id=:id AND con.sealetc_id = sealetc.id');
    $stmt->bindValue(":id",$id);
    $stmt->execute();
    $sealetc = $stmt->fetchAll();
    
    foreach($sealetc as $s) {
        $result .= ' >> Attribute (Food) : '.$s['name'];
    }
    
    return $result." ";
}

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