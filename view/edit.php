<?php

$minstate = intval((isset($_GET['minstate'])) ? $_GET['minstate']:"0");
$maxstate = intval((isset($_GET['maxstate'])) ? $_GET['maxstate']:"20");

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id AND status >=:minstate AND status <= :maxstate ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['edit']));
$stmt->bindValue(":minstate",$minstate);
$stmt->bindValue(":maxstate",$maxstate);
$stmt->execute();
$imports = $stmt->fetchAll();


$stmt2 = $db->prepare('SELECT * FROM ingredient ORDER BY name DESC');
$stmt2->execute();
$ingredients = $stmt2->fetchAll();


$stmt3 = $db->prepare('SELECT * FROM category ORDER BY gid');
$stmt3->execute();
$categories = $stmt3->fetchAll();


$stmt4 = $db->prepare('SELECT * FROM sealetc ORDER BY name');
$stmt4->execute();
$seals = $stmt4->fetchAll();

$stmt5 = $db->prepare('SELECT name,media_path FROM import WHERE id = :id');
$stmt5->bindValue(":id",urldecode($_GET['edit']));
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
  <?php include ("header.php"); ?>
  <body>
    
      
    <nav class="navbar navbar-default this-navbar">
      <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand">Produkteditor</a></div>
        <div id="menu-navbar">
          <ul class="nav navbar-nav">
            <li><a href="/" class="dropdown-toggle">Import/Export</a></li>
            <li>
                <form action="" method="get">
                    <input type="hidden" name="edit" value="<?php echo $_GET['edit']; ?>" />
                    <input type="text" name="minstate" value="<?php echo $minstate; ?>" size="2" />
                    <input type="text" name="maxstate" value="<?php echo $maxstate; ?>" size="2" />
                    <input type="submit" value="Filter Status" />
                    (0=new,5=edited,10=finished,15=exported once,other=custom)
                </form>
            </li>
          </ul>
        </div>
        <div class="navbar-right-label">
            <?php echo $name.", ".urldecode($_GET['edit']); ?>
        </div>
      </div>
    </nav>



    <div id="table-container-wrapper">
      <div id="table-container">
        <table id="product-table" class="table table-striped">
          <tr class="head-row">
            <th>#</th>
            <th>Status</th>
            <th>Name</th>
            <th>EAN Code</th>
            <th>Marke</th>
          </tr>

          <?php
          // <tr class="row-active">
          foreach($imports as $imp) {

              ?>
          <tr data-open_edit_id="<?php echo $imp["id"]; ?>">
              <td><?php echo $imp["id"]; ?></td>
              <td><span class="eds eds-state-<?php echo $imp["status"]; ?>"><?php echo $imp["status"]; ?></span></td>
              <td data-nfieldu="<?php echo $imp["id"]; ?>"><?php
                  if(strlen($tp = $imp["productName de_AT"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productName de_DE"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productName en_US"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productName es_ES"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productName fr_FR"])>1) {
                      echo $tp;
                  }
              ?></td>
              <td><?php echo $imp["articleEanCode"]; ?></td>
              <td data-nfieldb="<?php echo $imp["id"]; ?>"><?php
                  if(strlen($tp = $imp["productBrand de_AT"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productBrand de_DE"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productBrand en_US"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productBrand es_ES"])>1) {
                      echo $tp;
                  } else if(strlen($tp = $imp["productBrand fr_FR"])>1) {
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
          <li role="presentation" id="li-tab1" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">Allgemein & Nährwerte</a></li>
          <li role="presentation" id="li-tab2"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">Inhaltsstoffe, etc.</a></li>
        </ul>  
          
          
        <div id="tab-menu" class="tab-content">
          <div id="tab1" role="tabpanel" class="tab-pane active">
            <form methop="post" action="/save" id="product-form">
              <div class="form-group clearfix">
                <div class="lit c25">
                  <label class="control-label">Name</label>
                  <input type="text" id="name"   value="" class="form-control">
                </div>

                <div class="lit c25">
                  <label class="control-label">Firmenname</label>
                  <input type="text" id="company"   value="" class="form-control">
                </div>

                <div class="lit c2">
                  <label class="control-label">Marke</label>
                  <input type="text" id="brand" value="" class="form-control">
                </div>

                <div class="lit c3">
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

                <div class="lit c2">
                  <label class="control-label">Behälter</label>
                  <input type="text" id="container" value="" class="form-control">
                </div>


              </div>
              <div class="form-group clearfix">

                  <div class="lit" style="width:75%">
                    <label class="control-label">Beschreibung</label>
                    <textarea  id="description"  rows="2" class="form-control"></textarea>

                    <label class="control-label">Anmerkung(en) (allg. Anmerkungen zum Einpflegen des Artikels)</label>
                    <textarea  id="notice"  rows="2" class="form-control"></textarea>
                  </div>

                  <div class="lit" style="width:25%;">
                      <label class="control-label">Inhalt (zur Preisberechnung)</label>
                      <div style="white-space:nowrap">
                          <input type="text" id="weight_amount" value="" class="form-control" style="width:53px; display:inline">
                          <select id="weight_amount_unit" class="inline">
                              <option>g</option>
                              <option>kg</option>
                              <option>l</option>
                              <option>ml</option>
                              <option>m²</option>
                              <option>Anwendungen</option>
                              <option>Stück</option>
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
                        <input type="text" class="myTextInput" id="nutrient_snd_amount" value="0" >
                        <span id="nutrient_unit_copy"></span>
                        <br/>
                        with:  <input type="text" class="myTextInput" id="nutrient_snd_additional" >
                        mit: <input type="text" class="myTextInput" id="nutrient_snd_additional_de" >
                    </div>
                    <div class="clear"></div>
                </div>
                
                
                
                <div class="nrg-group">
                  
                  
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Energie (in kJ)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (total) (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fat_total" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (gesättigt) (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fat_saturated" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Ballaststoffe (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Calcium (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Kohlenhydrate (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Zucker (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Proteine (in g)</label>
                    <div class="col-sm-5">
                      <input  type="text"  id="nutrient_100_protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Salz (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_salt" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Lactose (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Natrium (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Broteinheiten (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_bread_unit" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="nrg-group">
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Energie (in KJ)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (total) (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fat_total" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (gesättigt) (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fat_saturated" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Ballaststoffe (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Calcium (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Kohlenhydrate (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Zucker (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Proteine (in g)</label>
                    <div class="col-sm-5">
                      <input  type="text"  id="nutrient_snd_protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Salz (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_salt" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Lactose (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Natrium (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Broteinheiten (in g)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_bread_unit" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="clear"></div>
              </div>
            </form>
          </div>




          <div id="tab2" role="tabpanel" class="tab-pane">

          <div class="form-group">
            <label class="control-label">Inhaltsstoffe</label>

            <p>
                <span id="ingredients_collector"></span>
                <span id="ingredients_selwrap">
                    <input type="text" id="ingredients_selector" data-type="standard" />
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
                    <input type="checkbox" id="cur_ingr_b"  data-cur_ingr="b">B - Krebstiere
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_c"  data-cur_ingr="c">C - Ei
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_d"   data-cur_ingr="d">D - Fisch
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_e"  data-cur_ingr="e">E - Erdnuss
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_f"  data-cur_ingr="f">F - Soja
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_g"  data-cur_ingr="g">G - Milch oder Laktose
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_h"  data-cur_ingr="h">H - Schalenfrüchte
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_l"  data-cur_ingr="l">L - Sellerie
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_m"  data-cur_ingr="m">M - Senf
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_n"  data-cur_ingr="n">N - Sesam
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_o"  data-cur_ingr="o">O - Sulfite
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox" id="cur_ingr_p"  data-cur_ingr="p">P - Lupinen
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox"  id="cur_ingr_r"  data-cur_ingr="r">R - Weichtiere
                  </label>
                </div>
                
                  <div>
                      <button id="ingredient_deleter">Inhaltsstoff komplett löschen</button>
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
              </div>
            </div>


            <hr/>

            <div>
              <p>
                <label class="enthaltwrp">Enthält Spuren von:</label>
                <span id="enthalt_spuren_collector"></span>
                <span><input type="text" id="enthalt_spuren" data-type="enthalt" /></span>
              </p>
            </div>

            <div>
              <p>
                <label class="enthaltwrp">Enthält eine geringe Menge:</label>
                <span id="enthalt_gering_collector""></span>
                <span><input type="text" id="enthalt_gering" data-type="gering" /></span>
                <span></span>
              </p>
            </div>


            <hr/>

            <div id="category-container">
                <label>Kategorie</label><span id="cat_adder">+</span><br>
              <div id="category_select_wrapper"></div>
            </div>
            <hr>


            <div id="tag-container">

              <div id="admin-area">
                <div id="tag_group_selector_wrapper">
                  <p><label>Admin - Tag Creator</label></p>

                  <p><label>Tag-Gruppe anlegen:</label>  MUID (en): <input type="text" id="tag_group_new_muid" value="" /> Name (de): <input type="text" id="tag_group_new_name" /> <button id="tag_group_new_create">Gruppe anlegen</button>

                  <label>Tag-Gruppe löschen:</label> <input type="text" id="tag_group_delete_selector" value="" /><input type="hidden" id="tag_group_delete_selected_id" value="0" /> <button id="tag_group_delete">Gruppe löschen</button></p>
                </div>




                <p>Gruppe wählen: <input type="text" id="tag_group_selector" value="" /><input type="hidden" id="tag_group_selected_id" value="0" /></p>

                <div id="tag_group_selector">
                  <p>Tag anlegen (Erklärung: ~ ... Platzhalter für numerischen Wert in MUID/Name, $ ... Platzhalter für numerischen Value Type in MUID/Name, wenn nicht numerisch: Numerical value leer lassen)</p>
                  <p>
                  UID (EN): <input type="text" id="tag_uid_new" value="" />
                  Name (DE): <input type="text" id="tag_name_new" value="" />
                  Name (AT) *: <input type="text" id="tag_name_at_new" value="" />
                  Numerical Value *: <input type="text" id="tag_numerical_new" value="" />
                    <select id="tag_numerical_new_type">
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
                </div>
              </div>

              <hr>

              <label>Artikelbeschreibende Information</label>

              <div class="cfg_row">
                Aktive Kategorie:
                <input type="hidden" id="active_category" value="" />
                <span id="active_category_display">-- Keine --</span>
                &nbsp;&nbsp;&nbsp;<button id="active_category_seal_update">Speichere Highlight-Konfiguration</button>
              </div>

              <div id="attributes-container">
                <div id="guetesiegel" class="div-attributes"></div>
                <div class="clear"></div>
              </div>
              <hr>

            </div>

            <!-- @ Will DIE
            <div class="cfg_row">
                <input type="text" id="seal_new" value="" /><span id="seal_adder">+</span>
                <input type="text" id="seal_remove" value="" /><span id="seal_remover">-</span>
            </div>
            -->
            

            
          
          </div>
          
        </div>
        
      </div>
      <div id="send-container">
        <button id="finish_now" class="btn btn-default" data-save_id="">abschließen</button>
        <button id="save_now" class="btn btn-default" data-save_id="">sichern</button>
        <input type="text" id="custom_state" value="" />
        <div id="message_container"></div>
      </div>
    </div>

      
      <div class="hidden" id="ingredients"><?php echo json_encode($ingredients); ?></div>

      <div class="hidden" id="taggroups"><?php echo json_encode($taggroups); ?></div>
      
      <div class="hidden" id="categories"><?php echo json_encode($categories); ?></div>
      
      <div class="hidden" id="seals"><?php echo json_encode($seals); ?></div>
      
      <div class="hidden" id="media_path"><?php echo $media_path; ?></div>
    
      <div class="clear"></div>
    </div>
  </body>
</html>