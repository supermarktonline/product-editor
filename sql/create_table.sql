CREATE TABLE import (
    id timestamp PRIMARY KEY,
    name varchar(255),
    media_path varchar(255)
);


CREATE TABLE fdata (
    id SERIAL PRIMARY KEY,
    import_id timestamp REFERENCES import (id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    productMuid varchar(255),
    productNumber varchar(255),
    productOverrideInsertNew varchar(255),
    productDisplaySortValue varchar(255),
    articleMuid varchar(255),
    articleNumber varchar(255),
    articlePrice varchar(255),
    articleWeight varchar(255),
    articleVolume varchar(255),
    articleArea varchar(255),
    articleLength varchar(255),
    articleUses varchar(255),
    articleShippingWeight varchar(255),
    articleShippingHeight varchar(255),
    articleShippingWidth varchar(255),
    articleShippingDepth varchar(255),
    articleMinQuantity varchar(255),
    articleMinQuantitySteps varchar(255),
    articlePackagingUnit varchar(255),
    articleEanCode varchar(255),
    articleStock varchar(255),
    articleShowStock varchar(255),
    articleAdjMakerData varchar(255),
    articleMsrPrice varchar(255),
    articleUnreducedPrice varchar(255),
    articleUnreducedPriceType varchar(255),
    articleMerchantInfo varchar(255),
    articleBarCode varchar(255),
    articleSortValue varchar(255),
    productImages text,
    articleImages text,
    articleCurrency varchar(10),
    articleTaxCategory varchar(255),
    articleRestrictDeliveryToZone varchar(255),

    "productName de_AT" varchar(255),
    "productName de_DE" varchar(255),
    "productName en_US" varchar(255),
    "productName es_ES" varchar(255),
    "productName fr_FR" varchar(255),

    "productBrand de_AT" varchar(255),
    "productBrand de_DE" varchar(255),
    "productBrand en_US" varchar(255),
    "productBrand es_ES" varchar(255),
    "productBrand fr_FR" varchar(255),

    "productCorporation de_AT" varchar(255),
    "productCorporation de_DE" varchar(255),
    "productCorporation en_US" varchar(255),
    "productCorporation es_ES" varchar(255),
    "productCorporation fr_FR" varchar(255),

    "productDescription de_AT" text,
    "productDescription de_DE" text,
    "productDescription en_US" text,
    "productDescription es_ES" text,
    "productDescription fr_FR" text,


    "articleDescription de_AT" text,
    "articleDescription de_DE" text,
    "articleDescription en_US" text,
    "articleDescription es_ES" text,
    "articleDescription fr_FR" text,


    "articleUnit de_AT" varchar(255),
    "articleUnit de_DE" varchar(255),
    "articleUnit en_US" varchar(255),
    "articleUnit es_ES" varchar(255),
    "articleUnit fr_FR" varchar(255),

    "articleNoticesJson de_AT" text,
    "articleNoticesJson de_DE" text,
    "articleNoticesJson en_US" text,
    "articleNoticesJson es_ES" text,
    "articleNoticesJson fr_FR" text,


    "articlePosText de_AT" varchar(255),
    "articlePosText de_DE" varchar(255),
    "articlePosText en_US" varchar(255),
    "articlePosText es_ES" varchar(255),
    "articlePosText fr_FR" varchar(255),

    articleTagPaths text,
    articleSelectorTags varchar(255),
    articleMerchantTags varchar(255),

    status integer DEFAULT 0,
    notice text DEFAULT '',
    nutrient_unit varchar(10) DEFAULT 'g',

    nutrient_100_energy integer DEFAULT 0,
    nutrient_100_fat_total integer DEFAULT 0,
    nutrient_100_fat_saturated integer DEFAULT 0,
    nutrient_100_protein integer DEFAULT 0,
    nutrient_100_fibers integer DEFAULT 0,
    nutrient_100_calcium integer DEFAULT 0,
    nutrient_100_carb integer DEFAULT 0,
    nutrient_100_sugar integer DEFAULT 0,
    nutrient_100_salt integer DEFAULT 0,
    nutrient_100_lactose integer DEFAULT 0,
    nutrient_100_natrium integer DEFAULT 0,
    nutrient_100_bread_unit integer DEFAULT 0,

    nutrient_snd_amount integer DEFAULT 0,
    nutrient_snd_additional varchar(255) DEFAULT '',

    nutrient_snd_energy integer DEFAULT 0,
    nutrient_snd_fat_total integer DEFAULT 0,
    nutrient_snd_fat_saturated integer DEFAULT 0,
    nutrient_snd_protein integer DEFAULT 0,
    nutrient_snd_fibers integer DEFAULT 0,
    nutrient_snd_calcium integer DEFAULT 0,
    nutrient_snd_carb integer DEFAULT 0,
    nutrient_snd_sugar integer DEFAULT 0,
    nutrient_snd_salt integer DEFAULT 0,
    nutrient_snd_lactose integer DEFAULT 0,
    nutrient_snd_natrium integer DEFAULT 0,
    nutrient_snd_bread_unit integer DEFAULT 0,

    allergen_a boolean DEFAULT false,
    allergen_b boolean DEFAULT false,
    allergen_c boolean DEFAULT false,
    allergen_d boolean DEFAULT false,
    allergen_e boolean DEFAULT false,
    allergen_f boolean DEFAULT false,
    allergen_g boolean DEFAULT false,
    allergen_h boolean DEFAULT false,
    allergen_l boolean DEFAULT false,
    allergen_m boolean DEFAULT false,
    allergen_n boolean DEFAULT false,
    allergen_o boolean DEFAULT false,
    allergen_p boolean DEFAULT false,
    allergen_r boolean DEFAULT false

);


CREATE TABLE ingredient (

    id SERIAL PRIMARY KEY,
    name varchar(255) UNIQUE,
    a boolean DEFAULT false,
    b boolean DEFAULT false,
    c boolean DEFAULT false,
    d boolean DEFAULT false,
    e boolean DEFAULT false,
    f boolean DEFAULT false,
    g boolean DEFAULT false,
    h boolean DEFAULT false,
    l boolean DEFAULT false,
    m boolean DEFAULT false,
    n boolean DEFAULT false,
    o boolean DEFAULT false,
    p boolean DEFAULT false,
    r boolean DEFAULT false
);



CREATE TABLE fdata_ingredient (
  fdata_id    int REFERENCES fdata (id) ON UPDATE CASCADE ON DELETE CASCADE
, ingredient_id int REFERENCES ingredient(id) ON UPDATE CASCADE ON DELETE CASCADE
  -- explicit pk
, CONSTRAINT fdata_ingredient_pkey PRIMARY KEY (fdata_id, ingredient_id)
);


CREATE TABLE category (
    gid integer PRIMARY KEY,
    lvl_1 varchar(255),
    lvl_2 varchar(255),
    lvl_3 varchar(255),
    lvl_4 varchar(255),
    lvl_5 varchar(255),
    lvl_6 varchar(255),
    lvl_7 varchar(255)
);

CREATE TABLE fdata_category (
  fdata_id    int REFERENCES fdata (id) ON UPDATE CASCADE ON DELETE CASCADE
, category_id int REFERENCES category(gid) ON UPDATE CASCADE ON DELETE CASCADE
, CONSTRAINT fdata_category_pkey PRIMARY KEY (fdata_id, category_id)
);



--- 
CREATE TABLE sealetc (
    id SERIAL PRIMARY KEY,
    name varchar(255) UNIQUE
);


-- saves default suggestions of seal etc for a given category
CREATE TABLE category_sealetc (
  category_id    int REFERENCES category(gid) ON UPDATE CASCADE ON DELETE CASCADE
, sealetc_id int REFERENCES sealetc(id) ON UPDATE CASCADE ON DELETE CASCADE
, CONSTRAINT category_sealetc_pkey PRIMARY KEY (category_id, sealetc_id)
);


CREATE TABLE fdata_sealetc (
  fdata_id    int REFERENCES fdata(id) ON UPDATE CASCADE ON DELETE CASCADE
, sealetc_id int REFERENCES sealetc(id) ON UPDATE CASCADE ON DELETE CASCADE
, CONSTRAINT fdata_sealetc_pkey PRIMARY KEY (fdata_id, sealetc_id)
);