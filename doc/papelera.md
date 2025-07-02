
```
{#% extends '@SonataAdmin/standard_layout.html.twig' %#}

{% block sonata_admin_content %}

  <div class="sonata-ba-view">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <div class="box-header">
              <h4 class="box-title">Asientos Disponibles</h4>
          </div>
          <div class="box-body">
              {% set transporte = servicio.transporte %}
              {{ include('ServicioAdmin/_asiento_layout.html.twig') }}
          </div>
        </div>
      </div>
    </div>
  </div>

  {{ form(form) }}
{% endblock %}
```


https://github.com/sonata-project/SonataMediaBundle/blob/4.x/src/Block/GalleryBlockService.php

```
// simulate an association ...
$fieldDescription = $this->getGalleryAdmin()->getModelManager()->getNewFieldDescriptionInstance($this->getGalleryAdmin()->getClass(), 'media', array(
    'translation_domain' => 'SonataMediaBundle',
));
$fieldDescription->setAssociationAdmin($this->getGalleryAdmin());
$fieldDescription->setAdmin($formMapper->getAdmin());
$fieldDescription->setOption('edit', 'list');
$fieldDescription->setAssociationMapping(array('fieldName' => 'gallery', 'type' => ClassMetadataInfo::MANY_TO_ONE));

$builder = $formMapper->create('galleryId', 'sonata_type_model_list', array(
    'sonata_field_description' => $fieldDescription,
    'class'                    => $this->getGalleryAdmin()->getClass(),
    'model_manager'            => $this->getGalleryAdmin()->getModelManager(),
    'label'                    => 'form.label_gallery',
));


$formMapper->add('settings', 'sonata_type_immutable_array', array(
    'keys' => array(
        array($builder, null, array()),
    ),
    'translation_domain' => 'SonataMediaBundle',
));
```


Public Key
APP_USR-483ab6f4-783b-4412-9e79-8b9f9d16e0cc

Access Token
APP_USR-2352583760257351-063020-7330011c5de9a15c6b572acd3ae1c413-214189294

Client ID
2352583760257351

Client Secret
DpRdRKGpFz0tPaomoX0AmmQ75wKoJJDv

clave secreta
b865a2e56dc4070944859a2e19c85c754d0cec55f097cb62bfd3fe4d368e7aee

