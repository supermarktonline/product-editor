<?php

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['edit']));
$stmt->execute();
$imports = $stmt->fetchAll();


$stmt2 = $db->prepare('SELECT * FROM ingredient ORDER BY name DESC');
$stmt2->execute();
$ingredients = $stmt2->fetchAll();


$stmt3 = $db->prepare('SELECT * FROM category ORDER BY gid');
$stmt3->execute();
$categories = $stmt3->fetchAll();


$stmt3 = $db->prepare('SELECT * FROM sealetc ORDER BY name');
$stmt3->execute();
$seals = $stmt3->fetchAll();


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
            <li><a onclick="toggleList();" class="toggleList-button dropdown-toggle">Produktliste ein/ausblenden</a></li>
            <li><a href="/" class="dropdown-toggle">Import/Export</a></li>
          </ul>
        </div>
      </div>
    </nav>
      
      
    <div id="table-container">
      <table id="product-table" class="table table-striped">
        <tr class="head-row">
          <th>#</th>
          <th>Bearbeitet</th>
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
            <td><span class="eds <?php echo ($imp["edited"]) ? "eds-edited":"eds-new"; ?>"></span></td>
            <td><?php
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
            <td><?php echo $imp["articleeancode"]; ?></td>
            <td><?php
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
    <div id="main-container" class="no-show">
        
      <!-- Images -->
      <div id="img-container">  
        
          <div id="current_image">
              
          </div>
          <div id="thumb-container">
              
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
              <div class="form-group">
                <label class="control-label">Name</label>
                <input type="text" id="name"   value="" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label">Beschreibung</label>
                <textarea  id="description"  rows="2" class="form-control"></textarea>
              </div>
              <div class="form-group">
                  <label class="control-label">Anmerkung(en) (allg. Anmerkungen zum Einpflegen des Artikels)</label>
                  <textarea  id="notice"  rows="2" class="form-control"></textarea>
              </div>
                
              <div class="nutrition-container">
                <label class="control-label">Nährwertangaben:</label><br>
                <div class="nrg-group">
                  <label class="control-label">pro 100</label>
                  <select id="nutrient_unit">
                    <option>g</option>
                    <option>ml</option>
                  </select>
                  <button type="button" id="generate_nw">Generate right</button>
                  <br><br>
                  
                  
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Energie (in KJ)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (total)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fat_total" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (gesättigt)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fat_saturated" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Proteine</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Ballaststoffe</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Calcium</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Kohlenhydrate</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Zucker</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Salz</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_salt" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Lactose</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Natrium</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Broteinheiten</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_100_bread_unit" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="nrg-group">
                  <label class="control-label">pro</label>
                  <input type="text" class="myTextInput" id="nutrient_snd_amount" >
                  <span id="nutrient_unit_copy"></span>
                  
                  
                  <input type="text" class="myTextInput" id="nutrient_snd_additional" >
                  
                  
                  
                  <br><br>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Energie (in KJ)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (total)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fat_total" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Fett (gesättigt)</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fat_saturated" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Proteine</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Ballaststoffe</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Calcium</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label  class="control-label">Kohlenhydrate</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Zucker</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Salz</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_salt" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Lactose</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Natrium</label>
                    <div class="col-sm-5"> 
                      <input  type="text"  id="nutrient_snd_natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label  class="control-label">Broteinheiten</label>
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
                    <input type="text" id="ingredients_selector" />
                    <div id="ingredients_suggestor"></div>
                </span>
            </p>
            
          </div>
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
            <hr>
            <div id="category-container">
                <label>Kategorie</label><span id="cat_adder">+</span><br>
              <div id="category_select_wrapper"></div>
            </div>
            <hr>
            
            <label>Gütesiegel, etc.</label>
            <input type="text" id="seal_new" value="" /><span id="seal_adder">+</span>
            <input type="text" id="seal_remove" value="" /><span id="seal_remover">-</span>
            <br>
            
            <div id="attributes-container">
              <div id="guetesiegel" class="div-attributes"></div>
                <div class="clear"></div>
            </div>
            <hr>
            
          
          </div>
          
        </div>
        
      </div>
      <div id="send-container">
        <button id="save_now" class="btn btn-default" data-save_id="">sichern</button>
        <div id="message_container"></div>
      </div>
    </div>

      
      <div class="hidden" id="ingredients"><?php echo json_encode($ingredients); ?></div>
      
      <div class="hidden" id="categories"><?php echo json_encode($categories); ?></div>
      
      <div class="hidden" id="seals"><?php echo json_encode($seals); ?></div>
    
      <div class="clear"></div>
    </div>
  </body>
</html>