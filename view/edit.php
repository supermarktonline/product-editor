<!DOCTYPE html>
<html>
  <?php include ("header.php"); ?>
  <body>
    
      
    <nav class="navbar navbar-default this-navbar">
      <div class="container-fluid">
        <div class="navbar-header"><a class="navbar-brand">Produkteditor</a></div>
        <div id="menu-navbar">
          <ul class="nav navbar-nav">
            <li class="active"><a class="dropdown-toggle">Home<span class="sr-only">(current)</span></a></li>
            <li><a onclick="toggleList();" class="toggleList-button dropdown-toggle">Produktliste ein/ausblenden</a></li>
            <li><a href="#" class="dropdown-toggle">Editierte Produkte</a></li>
            <li><a href="#" class="dropdown-toggle">Import/Export</a></li>
            
          </ul>
        </div>
      </div>
    </nav>
      
      
    <div id="table-container">
      <table id="product-table" class="table table-striped">
        <tr class="head-row">
          <th>#</th>
          <th>Name</th>
          <th>EAN Code</th>
          <th>Marke</th>
        </tr>
        <tr class="row-active">
          <td>1</td>
          <td>Erbsen und Karotten</td>
          <td>4054665465</td>
          <td>Spar</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
        <tr>
          <td>4</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
        <tr>
          <td>5</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
        <tr>
          <td>6</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
        <tr>
          <td>7</td>
          <td>Fruchtjoghurt</td>
          <td>94564649831</td>
          <td>Nöm</td>
        </tr>
      </table>
    </div>
    <div id="main-container">
      <div id="img-container">  
        
        <div class="div-img">
          <img src="<?php echo VIEWPATH; ?>img/prod2.jpg">  
        </div>
        <div id="thumb-container">
          <img src="<?php echo VIEWPATH; ?>img/product_ex.jpg" class="img-thumbnail my-thumbnail">
          <img src="<?php echo VIEWPATH; ?>img/product_ex.jpg" class="img-thumbnail my-thumbnail">
          <img src="<?php echo VIEWPATH; ?>img/product_ex.jpg" class="img-thumbnail my-thumbnail">
        </div>
      </div>
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
                <input type="text" name="name" value="Erbsen und Karotten" class="form-control">
              </div>
              <div class="form-group">
                <label class="control-label">Beschreibung</label>
                <textarea name="description" rows="2" class="form-control">Das sind schöne Erbsen mit Karotten!</textarea>
              </div>
              <div class="nutrition-container">
                <label class="control-label">Nährwertangaben:</label><br>
                <div class="nrg-group">
                  <label class="control-label">pro 100</label>
                  <select>
                    <option>g</option>
                    <option>ml</option>
                  </select>
                  <br><br>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-energy" class="control-label">Energie (in KJ)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionEnergy" type="text" placeholder="14" id="input-energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-fat" class="control-label">Fett (total)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionFat" type="text" placeholder="14" id="input-fat" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-fatSat" class="control-label">Fett (gesättigt)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionFatSat" type="text" placeholder="14" id="input-fatSat" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-protein" class="control-label">Proteine</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionProtein" type="text" placeholder="14" id="input-protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-fibers" class="control-label">Ballaststoffe</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionFibers" type="text" placeholder="14" id="input-fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-calcium" class="control-label">Calcium</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionCalcium" type="text" placeholder="14" id="input-calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-carb" class="control-label">Kohlenhydrate</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionCarb" type="text" placeholder="14" id="input-carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-sugar" class="control-label">Zucker</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionSugar" type="text" placeholder="14" id="input-sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-sodium" class="control-label">Salz</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionSodium" type="text" placeholder="14" id="input-sodium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-lactose" class="control-label">Lactose</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionLactose" type="text" placeholder="14" id="input-lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-natrium" class="control-label">Natrium</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionNatrium" type="text" placeholder="14" id="input-natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-breadunits" class="control-label">Broteinheiten</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionBreadunits" type="text" placeholder="14" id="input-breadunits" class="form-control">
                    </div>
                  </div>
                </div>
                <div class="nrg-group">
                  <label class="control-label">pro</label>
                  <input type="text" class="myTextInput" placeholder="250">
                  <select>
                    <option>g</option>
                    <option>ml</option>
                  </select>
                  <input type="text" class="myTextInput" placeholder="z.B. 'inklusive Milch'">
                  <br><br>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-indi-energy" class="control-label">Energie (in KJ)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiEnergy" type="text" placeholder="14" id="input-indi-energy" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-indi-fat" class="control-label">Fett (total)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiFat" type="text" placeholder="14" id="input-indi-fat" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-indi-fatSat" class="control-label">Fett (gesättigt)</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiFatSat" type="text" placeholder="14" id="input-indi-fatSat" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-protein" class="control-label">Proteine</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiProtein" type="text" placeholder="14" id="input-indi-protein" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-fibers" class="control-label">Ballaststoffe</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiFibers" type="text" placeholder="14" id="input-indi-fibers" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-calcium" class="control-label">Calcium</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiCalcium" type="text" placeholder="14" id="input-indi-calcium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm form-horizontal">
                    <label for="input-indi-carb" class="control-label">Kohlenhydrate</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiCarb" type="text" placeholder="14" id="input-indi-carb" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-sugar" class="control-label">Zucker</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiSugar" type="text" placeholder="14" id="input-indi-sugar" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-sodium" class="control-label">Salz</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiSodium" type="text" placeholder="14" id="input-indi-sodium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-lactose" class="control-label">Lactose</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiLactose" type="text" placeholder="14" id="input-indi-lactose" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-natrium" class="control-label">Natrium</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiNatrium" type="text" placeholder="14" id="input-natrium" class="form-control">
                    </div>
                  </div>
                  <div class="form-group form-group-sm">
                    <label for="input-indi-breadunits" class="control-label">Broteinheiten</label>
                    <div class="col-sm-5"> 
                      <input name="nutritionIndiBreadunits" type="text" placeholder="14" id="input-breadunits" class="form-control">
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div id="tab2" role="tabpanel" class="tab-pane">
            <div class="form-group">
            <label class="control-label">Inhaltsstoffe</label>
            <input type="text" name="tags" data-role="tagsinput" class="form-control">
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
        <button class="btn btn-default">sichern</button>
        <button class="btn btn-default send-btn">senden</button>
      </div>
    </div>

  </body>
</html>