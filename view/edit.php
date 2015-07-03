<?php

// query the list of the desired import
$stmt = $db->prepare('SELECT * FROM fdata WHERE import_id = :import_id ORDER BY id ASC');
$stmt->bindValue(":import_id",urldecode($_GET['edit']));
$stmt->execute();
$imports = $stmt->fetchAll();

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
    <div id="main-container">
        
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
                
              <div class="nutrition-container">
                <label class="control-label">Nährwertangaben:</label><br>
                <div class="nrg-group">
                  <label class="control-label">pro 100</label>
                  <select id="nutrient_unit">
                    <option>g</option>
                    <option>ml</option>
                  </select>
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
              </div>
            </form>
          </div>
          <div id="tab2" role="tabpanel" class="tab-pane">
            <div class="form-group">
            <label class="control-label">Inhaltsstoffe</label>
            <input type="text"  data-role="tagsinput" class="form-control">
          </div>
          <div class="form-group"> 
            <div class="div-allergene">  
              <label class="control-label">Allergene (pro Inhaltsstoff):</label>
              <div id="allergy-select">
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">A - glutenhaltiges Getreide
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">B - Krebstiere
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">C - Ei
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">D - Fisch
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">E - Erdnuss
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">F - Soja
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">G - Milch oder Laktose
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">H - Schalenfrüchte
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">L - Sellerie
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">M - Senf
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">N - Sesam
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">O - Sulfite
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">P - Lupinen
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">R - Weichtiere
                  </label>
                </div>
              </div>
            </div>
            <div class="div-allergene div-allergene-right">  
              <label class="control-label">Allergene (insgesamt):</label>
              <div id="allergy-select">
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">A - glutenhaltiges Getreide
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">B - Krebstiere
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">C - Ei
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">D - Fisch
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">E - Erdnuss
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">F - Soja
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">G - Milch oder Laktose
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">H - Schalenfrüchte
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">L - Sellerie
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">M - Senf
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">N - Sesam
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">O - Sulfite
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">P - Lupinen
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">R - Weichtiere
                  </label>
                </div>
              </div>
            </div>
            <hr>
            <div id="category-container">
              <label>Kategorie</label><br>
              <div class="div-categories">  
                <select>
                  <option>-- wählen --</option>
                  <option>Milchprodukte</option>
                  <option>Fleisch und Wurst</option>
                  <option>Brot und Gebäck</option>
                </select>
              </div>
              <div class="div-categories">  
                <select>
                  <option>-- wählen --</option>
                  <option>Milchprodukte</option>
                  <option>Fleisch und Wurst</option>
                  <option>Brot und Gebäck</option>
                </select>
              </div>
              <div class="div-categories">  
                <select>
                  <option>-- wählen --</option>
                  <option>Milchprodukte</option>
                  <option>Fleisch und Wurst</option>
                  <option>Brot und Gebäck</option>
                </select>
              </div>
            </div>
            <hr>
            <label class="control-label">Gütesiegel, etc.</label>
            <div id="attributes-container">
              <div class="div-attributes">  
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Fairtrade
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Bio
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Glutenfrei
                  </label>
                </div>
              </div>
              <div class="div-attributes">  
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Laktosefrei
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Vegetarisch
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Vegan
                  </label>
                </div>
              </div>
              <div class="div-attributes">  
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Fettarm
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Fruktosefrei
                  </label>
                </div>
                <div class="checkbox"> 
                  <label>
                    <input type="checkbox">Zuckerfrei
                  </label>
                </div>
              </div>
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

  </body>
</html>