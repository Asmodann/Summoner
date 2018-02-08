# Summoner
Micro framework full php from scratch

__How to start my server?__

*With php server*
> php -S 127.0.0.1:4000

*Without php server*
> Configure .htaccess

## Console
__You can use some commands to generate files:__
> php bin/console build:controller MyController

Generate new controller named MyController
> php bin/console build:entity MyEntity

Generate new entity named MyEntity<br />
_*Before to generate new entity, you must create the table in your database*_
<br />
<br />
## Entity - _Relations_
File name: MyEntity.relation.php
> "questions" => [
    "class" => Question::class,
    "type" => "OneToMany",
    "field_out" => "id",
    "field_in" => "id_categorie"
    ]
* **key:** Variable name
* **class:** Class object
* **type:** Relation type (OneToOne or OneToMany)
* **field_out:** Field in **class** entity
* **field_in:** Field in current entity



## Entity - _Schemas_
File name: MyEntity.schema.php
> return [
"_primary" => ["id"],
  "id_categorie",
  "question"
  ]
* **_primary:** Entity primary key
* **Entity others fields**
